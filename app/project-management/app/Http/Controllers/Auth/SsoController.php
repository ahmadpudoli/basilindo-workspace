<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Core\Models\ExternalIdentity;
use Core\Models\Role;
use Core\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class SsoController extends Controller
{
    public function redirect(): RedirectResponse
    {
        abort_unless(config('sso.enabled'), 503, 'SSO perusahaan belum dikonfigurasi.');

        $state = Str::random(64);
        $nonce = Str::random(64);
        $codeVerifier = Str::random(96);
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        session([
            'sso_state' => $state,
            'sso_nonce' => $nonce,
            'sso_code_verifier' => $codeVerifier,
            "sso_transactions.{$state}" => [
                'nonce' => $nonce,
                'code_verifier' => $codeVerifier,
                'redirect_uri' => config('sso.redirect_uri'),
                'created_at' => now()->timestamp,
            ],
        ]);

        $query = http_build_query([
            'client_id' => config('sso.client_id'),
            'redirect_uri' => config('sso.redirect_uri'),
            'response_type' => 'code',
            'scope' => implode(' ', config('sso.scopes', ['openid', 'profile', 'email'])),
            'state' => $state,
            'nonce' => $nonce,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);

        return redirect()->away(rtrim(config('sso.issuer_url'), '/').'/oauth/authorize?'.$query);
    }

    public function callback(Request $request): RedirectResponse
    {
        abort_unless(config('sso.enabled'), 404);

        $state = $request->string('state')->toString();
        $transaction = session("sso_transactions.{$state}");

        if (! is_array($transaction) && hash_equals((string) session('sso_state'), $state)) {
            $transaction = [
                'nonce' => session('sso_nonce'),
                'code_verifier' => session('sso_code_verifier'),
                'redirect_uri' => config('sso.redirect_uri'),
            ];
        }

        if (! is_array($transaction)) {
            abort(419, 'SSO state mismatch.');
        }

        try {
            $tokenResponse = Http::asForm()->post(rtrim(config('sso.issuer_url'), '/').'/oauth/token', [
                'grant_type' => 'authorization_code',
                'client_id' => config('sso.client_id'),
                'client_secret' => config('sso.client_secret'),
                'redirect_uri' => $transaction['redirect_uri'] ?? config('sso.redirect_uri'),
                'code' => $request->string('code')->toString(),
                'code_verifier' => (string) ($transaction['code_verifier'] ?? ''),
            ])->throw()->json();
        } catch (RequestException $exception) {
            session()->forget("sso_transactions.{$state}");
            Log::warning('Project Management SSO authorization code exchange failed', [
                'state_present' => $state !== '',
                'status' => $exception->response?->status(),
                'error' => $exception->response?->json('error'),
            ]);

            return redirect()->route('auth.sso')->withErrors([
                'sso' => 'Sesi login SSO kedaluwarsa atau sudah digunakan. Silakan coba login kembali.',
            ]);
        }

        $claims = Http::withToken($tokenResponse['access_token'])
            ->get(rtrim(config('sso.issuer_url'), '/').'/oidc/userinfo')
            ->throw()
            ->json();
        $issuer = rtrim(config('sso.issuer_url'), '/');
        $subject = (string) ($claims['sub'] ?? '');

        abort_if($subject === '', 422, 'SSO subject tidak tersedia.');
        abort_unless((bool) data_get($claims, 'applications.current.allowed', false), 403, 'Akun SSO tidak memiliki akses ke Project Management.');

        $user = DB::transaction(function () use ($claims, $issuer, $subject) {
            $identity = ExternalIdentity::where('issuer', $issuer)->where('subject', $subject)->first();

            if ($identity) {
                $identity->update(['claims' => $claims, 'last_login_at' => now()]);
                $user = $identity->user;
                $this->syncMappedRoles($user, $claims);

                return $user;
            }

            $email = (string) ($claims['email'] ?? '');
            $existingUser = $email === '' ? null : User::where('email', $email)->first();

            if ($existingUser && config('sso.allow_system_account_linking') && $existingUser->isProtectedSystemAccount() && (bool) ($claims['email_verified'] ?? false)) {
                ExternalIdentity::create([
                    'user_id' => $existingUser->id,
                    'issuer' => $issuer,
                    'subject' => $subject,
                    'claims' => $claims,
                    'last_login_at' => now(),
                ]);
                $this->syncMappedRoles($existingUser, $claims);

                return $existingUser;
            }

            if ($email === '' || $existingUser) {
                throw new RuntimeException('Akun SSO belum terhubung. Hubungi administrator untuk account linking.');
            }

            $newUser = User::create([
                'name' => $claims['name'] ?? $email,
                'email' => $email,
                'email_verified_at' => ($claims['email_verified'] ?? false) ? now() : null,
                'password' => Str::random(64),
            ]);

            ExternalIdentity::create([
                'user_id' => $newUser->id,
                'issuer' => $issuer,
                'subject' => $subject,
                'claims' => $claims,
                'last_login_at' => now(),
            ]);
            $this->syncMappedRoles($newUser, $claims);

            return $newUser;
        });

        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->forget(['sso_state', 'sso_nonce', 'sso_code_verifier', "sso_transactions.{$state}"]);

        return redirect()->intended('/admin');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function syncMappedRoles(User $user, array $claims): void
    {
        if ($user->isProtectedSystemAccount()) {
            if (! $user->hasRole('super_admin')) {
                $user->assignRole('super_admin');
            }

            return;
        }

        $groups = array_values(array_filter(array_merge(
            (array) ($claims['groups'] ?? []),
            (array) data_get($claims, 'applications.current.roles', []),
        ), 'is_string'));
        $roles = array_values(array_unique(array_filter(array_map(
            fn (string $group) => config('sso.role_map')[$group] ?? null,
            $groups,
        ))));
        $roles = Role::whereIn('name', $roles)->pluck('name')->all();

        if ($roles !== []) {
            $user->syncRoles($roles);
        }
    }
}
