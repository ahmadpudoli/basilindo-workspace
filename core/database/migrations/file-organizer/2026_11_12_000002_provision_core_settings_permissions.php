<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('core')->hasTable('permissions') || ! Schema::connection('core')->hasTable('roles')) return;

        $permissions = [
            'manage_workspace_modules',
            'manage_hris_expense_categories',
            'access_integration', 'view_integration', 'manage_integration', 'run_integration_sync',
            'view_queue_jobs', 'manage_numbering_rules',
            'view_organization_unit', 'view_any_organization_unit', 'create_organization_unit', 'update_organization_unit', 'delete_organization_unit',
            'view_audit_trail',
            'view_user', 'view_any_user', 'create_user', 'update_user', 'delete_user',
            'view_role', 'view_any_role', 'create_role', 'update_role', 'delete_role',
        ];
        $db = DB::connection('core');
        foreach ($permissions as $name) {
            $db->table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['updated_at' => now(), 'created_at' => now()],
            );
        }

        $permissionIds = $db->table('permissions')->where('guard_name', 'web')->whereIn('name', $permissions)->pluck('id');
        $adminIds = $db->table('roles')->where('guard_name', 'web')->whereIn('name', ['super_admin', 'admin'])->pluck('id');
        if (Schema::connection('core')->hasTable('role_has_permissions')) {
            foreach ($adminIds as $roleId) {
                foreach ($permissionIds as $permissionId) {
                    $db->table('role_has_permissions')->insertOrIgnore(['permission_id' => $permissionId, 'role_id' => $roleId]);
                }
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Retain permission catalog entries and grants to avoid removing live access assignments.
    }
};
