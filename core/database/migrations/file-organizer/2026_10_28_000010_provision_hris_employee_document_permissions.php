<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'view_employee_documents',
            'upload_employee_documents',
            'download_employee_documents',
            'manage_employee_documents',
        ];
        $db = DB::connection('core');
        $now = now();

        foreach ($permissions as $name) {
            $db->table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['updated_at' => $now, 'created_at' => $now],
            );
        }

        $ids = $db->table('permissions')->where('guard_name', 'web')->whereIn('name', $permissions)->pluck('id');
        foreach (['super_admin', 'hris_admin'] as $roleName) {
            $roleId = $db->table('roles')->where('name', $roleName)->where('guard_name', 'web')->value('id');
            if ($roleId) {
                foreach ($ids as $permissionId) {
                    $db->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $roleId], []);
                }
            }
        }
    }

    public function down(): void
    {
        // Authorization catalog is retained on rollback to avoid removing grants from existing roles.
    }
};
