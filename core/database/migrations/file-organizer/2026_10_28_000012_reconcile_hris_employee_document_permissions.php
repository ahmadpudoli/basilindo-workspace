<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $db = DB::connection('core');
        $now = now();
        $permissions = [
            'view_employee_documents',
            'view_own_employee_documents',
            'upload_employee_documents',
            'download_employee_documents',
            'manage_employee_documents',
        ];

        foreach ($permissions as $name) {
            $db->table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['updated_at' => $now, 'created_at' => $now],
            );
        }

        $ids = $db->table('permissions')->where('guard_name', 'web')->whereIn('name', $permissions)->pluck('id', 'name');
        foreach (['super_admin', 'hris_admin'] as $roleName) {
            $roleId = $db->table('roles')->where('name', $roleName)->where('guard_name', 'web')->value('id');
            if ($roleId) {
                foreach ($ids as $permissionId) {
                    $db->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $roleId], []);
                }
            }
        }

        $memberRoleId = $db->table('roles')->where('name', 'hris_member')->where('guard_name', 'web')->value('id');
        if ($memberRoleId && $ids->get('view_own_employee_documents')) {
            $db->table('role_has_permissions')->updateOrInsert(['permission_id' => $ids->get('view_own_employee_documents'), 'role_id' => $memberRoleId], []);
        }
    }

    public function down(): void
    {
        // Keep authorization data on rollback to avoid removing existing grants.
    }
};
