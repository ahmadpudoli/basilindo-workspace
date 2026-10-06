<?php

use App\Models\ExternalIdentity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('redirects the File Organizer login page to company SSO', function () {
    config()->set([
        'sso.enabled' => true,
        'sso.issuer_url' => 'http://127.0.0.1:8100',
        'sso.client_id' => 'file-organizer-test',
        'sso.redirect_uri' => 'http://localhost:8000/auth/sso/callback',
    ]);

    $response = $this->get('/admin/login');
    $response->assertRedirect(route('auth.sso'));

    $providerResponse = $this->get('/auth/sso');
    $providerResponse->assertRedirect();
    expect(parse_url($providerResponse->headers->get('Location'), PHP_URL_PATH))
        ->toBe('/oauth/authorize');
});

it('starts SSO with a PKCE challenge', function () {
    config()->set(['sso.enabled' => true, 'sso.issuer_url' => 'http://127.0.0.1:8100']);

    $response = $this->get('/auth/sso');
    parse_str((string) parse_url($response->headers->get('Location'), PHP_URL_QUERY), $query);

    $response->assertRedirect();
    expect($query['code_challenge_method'])->toBe('S256')
        ->and($query['code_challenge'])->toHaveLength(43)
        ->and(session('sso_code_verifier'))->toHaveLength(96);
});

it('links a new local account to a project-sso subject after callback validation', function () {
    config()->set([
        'sso.enabled' => true,
        'sso.issuer_url' => 'http://127.0.0.1:8100',
        'sso.client_id' => 'file-organizer-test',
        'sso.client_secret' => 'test-secret',
        'sso.redirect_uri' => 'http://localhost:8000/auth/sso/callback',
    ]);

    Http::fake([
        'http://127.0.0.1:8100/oauth/token' => Http::response(['access_token' => 'access-token'], 200),
        'http://127.0.0.1:8100/oidc/userinfo' => Http::response([
            'sub' => 'employee-001',
            'name' => 'Employee One',
            'email' => 'employee.one@example.com',
            'email_verified' => true,
            'applications' => [
                'current' => ['code' => 'file-organizer', 'allowed' => true, 'roles' => ['document_admin']],
                'available' => [],
            ],
        ], 200),
    ]);

    $response = $this->withSession(['sso_state' => 'state-123', 'sso_nonce' => 'nonce-123', 'sso_code_verifier' => 'verifier-123'])
        ->get('/auth/sso/callback?code=authorization-code&state=state-123');

    $response->assertRedirect('/admin');
    Http::assertSent(fn ($request) => $request->url() === 'http://127.0.0.1:8100/oauth/token' && $request['code_verifier'] === 'verifier-123');
    expect(User::where('email', 'employee.one@example.com')->exists())->toBeTrue()
        ->and(ExternalIdentity::where('issuer', 'http://127.0.0.1:8100')->where('subject', 'employee-001')->exists())->toBeTrue();
});

it('links the protected local recovery account only when explicitly enabled', function () {
    config()->set([
        'sso.enabled' => true,
        'sso.allow_system_account_linking' => true,
        'sso.issuer_url' => 'http://127.0.0.1:8100',
        'sso.client_id' => 'file-organizer-test',
        'sso.client_secret' => 'test-secret',
        'sso.redirect_uri' => 'http://localhost:8000/auth/sso/callback',
    ]);

    $admin = User::factory()->create(['email' => 'admin@example.com']);
    $admin->forceFill(['is_system_account' => true])->save();

    Http::fake([
        'http://127.0.0.1:8100/oauth/token' => Http::response(['access_token' => 'access-token'], 200),
        'http://127.0.0.1:8100/oidc/userinfo' => Http::response([
            'sub' => 'admin-subject',
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'email_verified' => true,
            'applications' => ['current' => ['allowed' => true, 'roles' => ['admin']], 'available' => []],
        ], 200),
    ]);

    $this->withSession(['sso_state' => 'state-123', 'sso_code_verifier' => 'verifier-123'])
        ->get('/auth/sso/callback?code=authorization-code&state=state-123')
        ->assertRedirect('/admin');

    expect(ExternalIdentity::where('user_id', $admin->id)->where('subject', 'admin-subject')->exists())->toBeTrue();
});

it('rejects a callback with a mismatched state', function () {
    config()->set(['sso.enabled' => true]);

    $this->withSession(['sso_state' => 'expected-state'])
        ->get('/auth/sso/callback?code=authorization-code&state=wrong-state')
        ->assertStatus(419);
});
