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
            ['name' => 'view_hris_approvals', 'guard_name' => 'web'],
            ['created_at' => $now, 'updated_at' => $now],
        );
        $permissionId = $db->table('permissions')->where('name', 'view_hris_approvals')->where('guard_name', 'web')->value('id');
        $roleId = $db->table('roles')->where('name', 'hris_admin')->where('guard_name', 'web')->value('id');
        if ($permissionId && $roleId) $db->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $roleId], []);
    }

    public function down(): void
    {
        $db = DB::connection('core');
        $permissionId = $db->table('permissions')->where('name', 'view_hris_approvals')->where('guard_name', 'web')->value('id');
        if (! $permissionId) return;
        $db->table('role_has_permissions')->where('permission_id', $permissionId)->delete();
        $db->table('permissions')->where('id', $permissionId)->delete();
    }
};
