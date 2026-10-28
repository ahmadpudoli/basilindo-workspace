<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $moduleSetting = DB::connection('core')->table('settings')->where('key', 'workspace.active_modules')->whereNull('user_id')->first();
        if ($moduleSetting) {
            $modules = json_decode((string) $moduleSetting->value, true);
            if (is_array($modules) && ! in_array('hris', $modules, true)) {
                $modules[] = 'hris';
                DB::connection('core')->table('settings')->where('id', $moduleSetting->id)->update(['value' => json_encode($modules), 'updated_at' => now()]);
            }
        }

        $permissions = ['access_hris', 'approve_hris_request', 'manage_hris_settings', 'view_employee', 'view_any_employee', 'create_employee', 'update_employee', 'delete_employee', 'view_leave_request', 'view_any_leave_request', 'create_leave_request', 'view_overtime', 'view_any_overtime', 'create_overtime', 'view_claim', 'view_any_claim', 'create_claim'];
        foreach ($permissions as $name) {
            DB::connection('core')->table('permissions')->updateOrInsert(['name' => $name, 'guard_name' => 'web'], ['updated_at' => now(), 'created_at' => now()]);
        }

        $permissionIds = DB::connection('core')->table('permissions')->whereIn('name', $permissions)->pluck('id', 'name');
        $superAdmin = DB::connection('core')->table('roles')->where('name', 'super_admin')->where('guard_name', 'web')->value('id');
        if ($superAdmin) {
            foreach ($permissionIds as $permissionId) DB::connection('core')->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $superAdmin], []);
        }

        foreach ([['hris_admin', $permissions], ['hris_member', ['view_employee', 'view_any_employee', 'view_leave_request', 'view_any_leave_request']]] as [$roleName, $rolePermissions]) {
            $roleId = DB::connection('core')->table('roles')->where('name', $roleName)->where('guard_name', 'web')->value('id');
            if (! $roleId) {
                $roleId = DB::connection('core')->table('roles')->insertGetId(['name' => $roleName, 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]);
            }
            foreach ($rolePermissions as $permission) DB::connection('core')->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionIds[$permission], 'role_id' => $roleId], []);
        }
    }

    public function down(): void
    {
        $permissionIds = DB::connection('core')->table('permissions')->whereIn('name', ['access_hris', 'approve_hris_request', 'manage_hris_settings', 'view_employee', 'view_any_employee', 'create_employee', 'update_employee', 'delete_employee', 'view_leave_request', 'view_any_leave_request', 'create_leave_request', 'view_overtime', 'view_any_overtime', 'create_overtime', 'view_claim', 'view_any_claim', 'create_claim'])->pluck('id');
        DB::connection('core')->table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::connection('core')->table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
