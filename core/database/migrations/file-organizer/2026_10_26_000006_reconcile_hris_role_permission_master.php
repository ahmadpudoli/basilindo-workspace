<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Canonical HRIS authorization catalog.
     *
     * This migration is additive: existing roles, permissions, and unrelated
     * grants are preserved. Missing HRIS rows and grants are provisioned.
     */
    public function up(): void
    {
        $connection = DB::connection('core');

        if (! Schema::connection('core')->hasTable('permissions')
            || ! Schema::connection('core')->hasTable('roles')
            || ! Schema::connection('core')->hasTable('role_has_permissions')) {
            return;
        }

        $permissions = $this->permissionNames();
        $now = now();

        foreach ($permissions as $permission) {
            $connection->table('permissions')->updateOrInsert(
                ['name' => $permission, 'guard_name' => 'web'],
                ['updated_at' => $now, 'created_at' => $now],
            );
        }

        $permissionIds = $connection->table('permissions')
            ->where('guard_name', 'web')
            ->whereIn('name', $permissions)
            ->pluck('id', 'name');

        foreach ($this->rolePermissions() as $roleName => $rolePermissionNames) {
            $roleId = $connection->table('roles')
                ->where('name', $roleName)
                ->where('guard_name', 'web')
                ->value('id');

            if (! $roleId) {
                $roleId = $connection->table('roles')->insertGetId([
                    'name' => $roleName,
                    'guard_name' => 'web',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($rolePermissionNames as $permissionName) {
                $permissionId = $permissionIds->get($permissionName);

                if ($permissionId) {
                    $connection->table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $permissionId,
                        'role_id' => $roleId,
                    ], []);
                }
            }
        }
    }

    public function down(): void
    {
        $connection = DB::connection('core');

        if (! Schema::connection('core')->hasTable('roles')
            || ! Schema::connection('core')->hasTable('permissions')
            || ! Schema::connection('core')->hasTable('role_has_permissions')) {
            return;
        }

        $roleIds = $connection->table('roles')
            ->whereIn('name', ['hris_admin', 'hris_member'])
            ->where('guard_name', 'web')
            ->pluck('id');
        $permissionIds = $connection->table('permissions')
            ->whereIn('name', $this->permissionNames())
            ->where('guard_name', 'web')
            ->pluck('id');

        if ($roleIds->isNotEmpty() && $permissionIds->isNotEmpty()) {
            $connection->table('role_has_permissions')
                ->whereIn('role_id', $roleIds)
                ->whereIn('permission_id', $permissionIds)
                ->delete();
        }

        if ($roleIds->isNotEmpty() && Schema::connection('core')->hasTable('model_has_roles')) {
            $assignedRoleIds = $connection->table('model_has_roles')
                ->whereIn('role_id', $roleIds)
                ->pluck('role_id')
                ->all();

            $connection->table('roles')
                ->whereIn('id', $roleIds->diff($assignedRoleIds))
                ->delete();
        }
    }

    /** @return list<string> */
    private function permissionNames(): array
    {
        $resources = ['employee', 'leave_request', 'overtime', 'claim'];
        $permissions = collect($resources)
            ->flatMap(fn (string $resource): array => collect(['view', 'view_any', 'create', 'update', 'delete'])
                ->map(fn (string $action): string => $action.'_'.$resource)
                ->all())
            ->all();

        return array_merge([
            'access_hris',
            'approve_hris_request',
            'manage_hris_settings',
        ], $permissions);
    }

    /** @return array<string, list<string>> */
    private function rolePermissions(): array
    {
        return [
            'hris_admin' => $this->permissionNames(),
            'hris_member' => [
                'access_hris',
                'view_employee', 'view_any_employee',
                'view_leave_request', 'view_any_leave_request',
                'view_overtime', 'view_any_overtime',
                'view_claim', 'view_any_claim',
            ],
            'super_admin' => $this->permissionNames(),
        ];
    }
};
