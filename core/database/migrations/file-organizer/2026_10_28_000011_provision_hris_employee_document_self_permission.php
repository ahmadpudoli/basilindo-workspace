<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $db = DB::connection('core');
        $now = now();
        $db->table('permissions')->updateOrInsert(
            ['name' => 'view_own_employee_documents', 'guard_name' => 'web'],
            ['created_at' => $now, 'updated_at' => $now],
        );
        $permissionId = $db->table('permissions')->where('name', 'view_own_employee_documents')->where('guard_name', 'web')->value('id');

        foreach (['super_admin', 'hris_admin', 'hris_member'] as $roleName) {
            $roleId = $db->table('roles')->where('name', $roleName)->where('guard_name', 'web')->value('id');
            if ($roleId) {
                $db->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $roleId], []);
            }
        }
    }

    public function down(): void
    {
        // Keep authorization data on rollback to prevent accidental grant loss.
    }
};
