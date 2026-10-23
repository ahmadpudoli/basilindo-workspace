<?php

use Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows a clear inline authentication error without exposing which credential failed', function () {
    User::factory()->create(['email' => 'known@example.com', 'password' => 'password']);

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => 'unknown@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('login'))
        ->assertSessionHasErrors(['authentication' => 'Email atau kata sandi tidak ditemukan.'])
        ->assertSessionDoesntHaveErrors(['email', 'password']);

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Gagal masuk')
        ->assertSee('Email atau kata sandi tidak ditemukan.')
        ->assertDontSee('Periksa kembali input yang ditandai pada formulir.');
});
