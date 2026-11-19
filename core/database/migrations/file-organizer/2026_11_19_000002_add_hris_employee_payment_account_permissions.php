<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PERMISSIONS = [
        'view_employee_payroll_account', 'update_employee_payroll_account',
        'view_employee_reimbursement_account', 'update_employee_reimbursement_account',
    ];

    public function up(): void
    {
        if (! Schema::connection('core')->hasTable('permissions')) return;
        $db = DB::connection('core');
        $now = now();
        foreach (self::PERMISSIONS as $permission) {
            $db->table('permissions')->insertOrIgnore(['name' => $permission, 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now]);
        }
        if (! Schema::connection('core')->hasTable('roles') || ! Schema::connection('core')->hasTable('role_has_permissions')) return;
        $roleId = $db->table('roles')->where('guard_name', 'web')->where('name', 'super_admin')->value('id');
        if (! $roleId) return;
        $ids = $db->table('permissions')->where('guard_name', 'web')->whereIn('name', self::PERMISSIONS)->pluck('id');
        foreach ($ids as $permissionId) {
            $db->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $roleId], []);
        }

        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    public function down(): void
    {
        // Permission assignments are preserved on rollback.
    }
};
