<?php

use Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the project management dashboard with the active status schema', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/workspace/project-management')
        ->assertOk()
        ->assertSee('Ticket terbuka');
});
