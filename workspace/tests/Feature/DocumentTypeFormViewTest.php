<?php

use Core\Models\Role;
use Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the create document type form without an edit model', function () {
    $user = User::factory()->create(['email' => 'admin@example.com', 'is_system_account' => true]);
    $user->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));

    $this->actingAs($user)
        ->get(route('workspace.documents.types.create'))
        ->assertOk()
        ->assertSee('Tambah tipe dokumen');
});
