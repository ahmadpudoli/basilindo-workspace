<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $db = DB::connection('core');
        $catalog = [
            'hris_settings' => ['view', 'create', 'update', 'delete'],
            'hris_workflow' => ['view', 'create', 'update', 'delete'],
            'hris_expense_categories' => ['view', 'create', 'update', 'delete'],
            'numbering_rules' => ['view', 'create', 'update'],
        ];

        foreach ($catalog as $resource => $actions) {
            foreach ($actions as $action) {
                $db->table('permissions')->updateOrInsert(
                    ['name' => "{$action}_{$resource}", 'guard_name' => 'web'],
                    ['created_at' => now(), 'updated_at' => now()],
                );
            }
        }

        // Preserve access held by existing roles while splitting each broad permission.
        $legacyMap = [
            'manage_hris_settings' => ['view_hris_settings', 'create_hris_settings', 'update_hris_settings', 'delete_hris_settings', 'view_hris_workflow', 'create_hris_workflow', 'update_hris_workflow', 'delete_hris_workflow'],
            'manage_hris_expense_categories' => ['view_hris_expense_categories', 'create_hris_expense_categories', 'update_hris_expense_categories', 'delete_hris_expense_categories'],
            'manage_numbering_rules' => ['view_numbering_rules', 'create_numbering_rules', 'update_numbering_rules'],
        ];
        foreach ($legacyMap as $legacy => $newNames) {
            $legacyId = $db->table('permissions')->where(['name' => $legacy, 'guard_name' => 'web'])->value('id');
            if (! $legacyId) continue;
            $roleIds = $db->table('role_has_permissions')->where('permission_id', $legacyId)->pluck('role_id');
            foreach ($newNames as $name) {
                $permissionId = $db->table('permissions')->where(['name' => $name, 'guard_name' => 'web'])->value('id');
                foreach ($roleIds as $roleId) {
                    $db->table('role_has_permissions')->insertOrIgnore(['permission_id' => $permissionId, 'role_id' => $roleId]);
                }
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Keep granular permissions and role assignments to avoid revoking live access.
    }
};
