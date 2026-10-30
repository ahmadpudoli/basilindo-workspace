<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = ['view_hris_holidays', 'create_hris_holiday', 'update_hris_holiday', 'delete_hris_holiday', 'manage_hris_holidays'];
        foreach ($permissions as $name) {
            DB::connection('core')->table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['updated_at' => now(), 'created_at' => now()],
            );
        }

        $permissionIds = DB::connection('core')->table('permissions')->whereIn('name', $permissions)->pluck('id', 'name');
        foreach (['super_admin' => $permissions, 'hris_admin' => $permissions] as $roleName => $rolePermissions) {
            $roleId = DB::connection('core')->table('roles')->where('name', $roleName)->where('guard_name', 'web')->value('id');
            if (! $roleId) {
                continue;
            }
            foreach ($rolePermissions as $permission) {
                DB::connection('core')->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionIds[$permission], 'role_id' => $roleId], []);
            }
        }
    }

    public function down(): void
    {
        $permissions = ['view_hris_holidays', 'create_hris_holiday', 'update_hris_holiday', 'delete_hris_holiday', 'manage_hris_holidays'];
        $ids = DB::connection('core')->table('permissions')->whereIn('name', $permissions)->pluck('id');
        DB::connection('core')->table('role_has_permissions')->whereIn('permission_id', $ids)->delete();
        DB::connection('core')->table('permissions')->whereIn('id', $ids)->delete();
    }
};
