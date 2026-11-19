<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $db = DB::connection('core');
        $permissionId = $db->table('permissions')->where('name', 'process_hris_payment')->where('guard_name', 'web')->value('id');
        if (! $permissionId) {
            $permissionId = $db->table('permissions')->insertGetId([
                'name' => 'process_hris_payment', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        foreach (['super_admin', 'finance_admin'] as $roleName) {
            $roleId = $db->table('roles')->where('name', $roleName)->where('guard_name', 'web')->value('id');
            if ($roleId) $db->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $roleId], []);
        }

        $adminRoleId = $db->table('roles')->where('name', 'admin')->where('guard_name', 'web')->value('id');
        if ($adminRoleId) $db->table('role_has_permissions')->where('permission_id', $permissionId)->where('role_id', $adminRoleId)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $db = DB::connection('core');
        $permissionId = $db->table('permissions')->where('name', 'process_hris_payment')->where('guard_name', 'web')->value('id');
        if (! $permissionId) return;

        $financeRoleId = $db->table('roles')->where('name', 'finance_admin')->where('guard_name', 'web')->value('id');
        if ($financeRoleId) $db->table('role_has_permissions')->where('permission_id', $permissionId)->where('role_id', $financeRoleId)->delete();

        $adminRoleId = $db->table('roles')->where('name', 'admin')->where('guard_name', 'web')->value('id');
        if ($adminRoleId) $db->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $adminRoleId], []);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
