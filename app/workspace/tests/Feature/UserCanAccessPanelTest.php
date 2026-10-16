<?php

use Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Core\Models\Role;

uses(RefreshDatabase::class);

it('denies panel access to a user with no role even if email is verified', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    expect($user->canAccessPanel(new \Filament\Panel))->toBeFalse();
});

it('denies panel access to a user with a role but an unverified email', function () {
    $user = User::factory()->unverified()->create();
    Role::firstOrCreate(['name' => 'panel_test_member', 'guard_name' => 'web']);
    $user->assignRole('panel_test_member');

    expect($user->canAccessPanel(new \Filament\Panel))->toBeFalse();
});

it('allows panel access to a user with a verified email and a role', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Role::firstOrCreate(['name' => 'panel_test_member', 'guard_name' => 'web']);
    $user->assignRole('panel_test_member');

    expect($user->canAccessPanel(new \Filament\Panel))->toBeTrue();
});
