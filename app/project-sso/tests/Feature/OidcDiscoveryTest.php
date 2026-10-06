<?php

use App\Models\SsoApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

uses(RefreshDatabase::class);

it('publishes the internal OIDC discovery document', function () {
    $response = $this->getJson('/.well-known/openid-configuration');

    $response->assertOk()
        ->assertJsonPath('issuer', 'http://127.0.0.1:8001')
        ->assertJsonPath('authorization_endpoint', 'http://127.0.0.1:8001/oauth/authorize')
        ->assertJsonPath('token_endpoint', 'http://127.0.0.1:8001/oauth/token')
        ->assertJsonPath('userinfo_endpoint', 'http://127.0.0.1:8001/oidc/userinfo')
        ->assertJsonPath('response_types_supported.0', 'code');
});

it('publishes the Passport RSA signing key as JWKS', function () {
    $response = $this->getJson('/oidc/jwks');

    $response->assertOk()
        ->assertJsonStructure(['keys' => [['kty', 'use', 'alg', 'kid', 'n', 'e']]])
        ->assertJsonPath('keys.0.kty', 'RSA')
        ->assertJsonPath('keys.0.alg', 'RS256');
});

it('publishes application entitlements in userinfo for the oauth client', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $application = SsoApplication::where('code', 'file-organizer')->firstOrFail();
    $application->users()->attach($user->id, ['role_code' => 'document_admin', 'status' => 'active']);

    Passport::actingAs($user, ['openid', 'profile', 'email'], 'api');

    $this->getJson('/oidc/userinfo')
        ->assertOk()
        ->assertJsonPath('applications.current.allowed', false)
        ->assertJsonPath('applications.available.0.code', 'file-organizer')
        ->assertJsonPath('applications.available.0.name', 'File Organizer');
});
