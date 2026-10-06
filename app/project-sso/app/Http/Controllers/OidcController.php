<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use App\Models\SsoApplication;

class OidcController extends Controller
{
    public function discovery(): JsonResponse
    {
        $issuer = rtrim(config('services.project_sso.issuer'), '/');

        return response()->json([
            'issuer' => $issuer,
            'authorization_endpoint' => $issuer.'/oauth/authorize',
            'token_endpoint' => $issuer.'/oauth/token',
            'userinfo_endpoint' => $issuer.'/oidc/userinfo',
            'end_session_endpoint' => $issuer.'/oidc/logout',
            'jwks_uri' => $issuer.'/oidc/jwks',
            'scopes_supported' => ['openid', 'profile', 'email'],
            'response_types_supported' => ['code'],
            'grant_types_supported' => ['authorization_code', 'refresh_token'],
            'subject_types_supported' => ['public'],
            'id_token_signing_alg_values_supported' => ['RS256'],
            'token_endpoint_auth_methods_supported' => ['client_secret_post', 'client_secret_basic'],
            'claims_supported' => ['sub', 'name', 'email', 'email_verified', 'groups'],
        ]);
    }

    public function userinfo(Request $request): JsonResponse
    {
        $user = $request->user('api');

        abort_unless($user, 401);

        return response()->json([
            'sub' => (string) $user->getAuthIdentifier(),
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => ! is_null($user->email_verified_at),
            'groups' => $user->getRoleNames()->values()->all(),
            'applications' => $this->applicationClaims($user),
        ]);
    }

    private function applicationClaims($user): array
    {
        // Passport's guard owns the client parsed from the bearer token. The
        // token attached to the user can be a transient token in tests or in
        // other guard flows and therefore may not contain the client ID.
        $clientId = auth('api')->client()?->getKey();
        $current = SsoApplication::where('oauth_client_id', $clientId)
            ->where('is_active', true)
            ->first();

        return [
            'current' => [
                'code' => $current?->code,
                'allowed' => $current ? $current->users()->whereKey($user->getKey())->wherePivot('status', 'active')->exists() : false,
                'roles' => $current ? $current->users()->whereKey($user->getKey())->wherePivot('status', 'active')->pluck('role_code')->values()->all() : [],
            ],
            'available' => $user->applications()->where('sso_applications.is_active', true)->wherePivot('status', 'active')->orderBy('sort_order')->get(['sso_applications.code', 'sso_applications.name', 'sso_applications.url', 'sso_applications.icon'])->map(fn ($app) => [
                'code' => $app->code, 'name' => $app->name, 'url' => $app->url, 'icon' => $app->icon,
            ])->values()->all(),
        ];
    }

    public function jwks(): JsonResponse
    {
        $publicKey = config('passport.public_key') ?: Passport::keyPath('oauth-public.key');
        $details = openssl_pkey_get_details(openssl_pkey_get_public(file_get_contents($publicKey)));

        abort_unless(is_array($details) && isset($details['rsa']['n'], $details['rsa']['e']), 503);

        return response()->json(['keys' => [[
            'kty' => 'RSA',
            'use' => 'sig',
            'alg' => 'RS256',
            'kid' => hash('sha256', $details['key']),
            'n' => rtrim(strtr(base64_encode($details['rsa']['n']), '+/', '-_'), '='),
            'e' => rtrim(strtr(base64_encode($details['rsa']['e']), '+/', '-_'), '='),
        ]]]);
    }
}
