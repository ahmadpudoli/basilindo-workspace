<?php

use App\Models\ExternalIdentity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('maps a project-sso issuer and subject to an internal user', function () {
    $user = User::factory()->create();

    $identity = ExternalIdentity::create([
        'user_id' => $user->id,
        'issuer' => 'http://project-sso.test',
        'subject' => 'employee-001',
        'claims' => ['email' => $user->email],
        'last_login_at' => now(),
    ]);

    expect($identity->user->is($user))->toBeTrue()
        ->and($user->externalIdentities)->toHaveCount(1)
        ->and(config('sso.provider'))->toBe('project-sso');
});
