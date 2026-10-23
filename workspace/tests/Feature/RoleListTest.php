<?php

use Core\Models\Role;
use Core\Models\Permission;
use Core\Models\User;

it('does not treat access_settings as permission to manage users, roles, or global audit', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'access_settings', 'guard_name' => 'web']));

    $this->actingAs($user)
        ->get(route('workspace.core.users'))
        ->assertForbidden();
    $this->actingAs($user)
        ->get(route('workspace.core.roles'))
        ->assertForbidden();
    $this->actingAs($user)
        ->get(route('workspace.core.audit-log'))
        ->assertForbidden();
});

it('lists roles with server-side filters and pagination settings', function () {
    $admin = User::query()->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->first();
    if (! $admin) {
        $admin = User::factory()->create(['is_system_account' => true]);
        $admin->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));
    }

    Role::create(['name' => 'finance_manager', 'guard_name' => 'web']);
    Role::create(['name' => 'project_viewer', 'guard_name' => 'web']);

    $this->actingAs($admin)
        ->get(route('workspace.core.roles', ['q' => 'finance', 'guard' => 'web', 'limit' => 10]))
        ->assertOk()
        ->assertSee('finance_manager')
        ->assertDontSee('project_viewer')
        ->assertSee('name="q"', false)
        ->assertSee('value="10"', false);
});

it('keeps the protected super admin role without a delete button', function () {
    $admin = User::query()->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->first();

    if (! $admin) {
        $admin = User::factory()->create(['is_system_account' => true]);
        $admin->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));
    }

    $this->actingAs($admin)
        ->get(route('workspace.core.roles', ['limit' => 100]))
        ->assertOk()
        ->assertSee('super_admin')
        ->assertSee('Role sistem tidak dapat dihapus.', false);
});

it('updates a role while validating against the core connection', function () {
    $admin = User::query()->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->first();

    if (! $admin) {
        $admin = User::factory()->create(['is_system_account' => true]);
        $admin->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));
    }

    $role = Role::create(['name' => 'role_validation_'.uniqid(), 'guard_name' => 'web']);
    $permission = Permission::firstOrCreate(['name' => 'view_document_audit_trail', 'guard_name' => 'web']);

    $this->actingAs($admin)
        ->put(route('workspace.core.roles.update', $role), [
            'name' => $role->name.'_updated',
            'guard_name' => 'web',
            'permissions' => [$permission->name],
        ])
        ->assertRedirect(route('workspace.core.roles'));

    expect($role->fresh()->name)->toEndWith('_updated')
        ->and($role->fresh()->hasPermissionTo($permission->name))->toBeTrue();
});
