<?php

use Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('uses the shared Laravel login route', function () {
    expect($this->get('/login')->assertOk());
});

it('requires authentication for the shared workspace dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

it('allows an authenticated user to access the shared dashboard', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $this->actingAs($user)->get('/dashboard')->assertOk();
});
