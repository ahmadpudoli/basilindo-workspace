<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ExternalIdentity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
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
        session(['sso_state' => $state, 'sso_nonce' => $nonce, 'sso_code_verifier' => $codeVerifier]);

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

        if (! hash_equals((string) session('sso_state'), (string) $request->string('state'))) {
            abort(419, 'SSO state mismatch.');
        }

        $tokenResponse = Http::asForm()->post(rtrim(config('sso.issuer_url'), '/').'/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('sso.client_id'),
            'client_secret' => config('sso.client_secret'),
            'redirect_uri' => config('sso.redirect_uri'),
            'code' => $request->string('code')->toString(),
            'code_verifier' => (string) session('sso_code_verifier'),
        ])->throw()->json();

        $claims = Http::withToken($tokenResponse['access_token'])->get(rtrim(config('sso.issuer_url'), '/').'/oidc/userinfo')->throw()->json();
        $issuer = rtrim(config('sso.issuer_url'), '/');
        $subject = (string) ($claims['sub'] ?? '');

        abort_if($subject === '', 422, 'SSO subject tidak tersedia.');
        abort_unless((bool) data_get($claims, 'applications.current.allowed', false), 403, 'Akun SSO tidak memiliki akses ke File Organizer.');

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

            ExternalIdentity::create(['user_id' => $newUser->id, 'issuer' => $issuer, 'subject' => $subject, 'claims' => $claims, 'last_login_at' => now()]);
            $this->syncMappedRoles($newUser, $claims);
            return $newUser;
        });

        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->forget(['sso_state', 'sso_nonce', 'sso_code_verifier']);

        return redirect()->intended('/admin');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->away(rtrim(config('sso.issuer_url'), '/').'/oidc/logout?'.http_build_query([
            'post_logout_redirect_uri' => url('/'),
        ]));
    }

    private function syncMappedRoles(User $user, array $claims): void
    {
        $groups = array_values(array_filter(array_merge(
            (array) ($claims['groups'] ?? []),
            (array) data_get($claims, 'applications.current.roles', []),
        ), 'is_string'));
        $roleMap = config('sso.role_map', []);
        $roles = array_values(array_unique(array_filter(array_map(
            fn (string $group) => $roleMap[$group] ?? null,
            $groups,
        ))));
        $roles = Role::whereIn('name', $roles)->pluck('name')->all();
        if ($roles !== []) {
            $user->syncRoles($roles);
        } else {
            Log::info('SSO login tanpa role mapping', ['user_id' => $user->id]);
        }
    }
}
