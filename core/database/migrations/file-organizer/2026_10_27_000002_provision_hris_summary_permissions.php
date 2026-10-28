<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $db = DB::connection('core');
        $now = now();
        $permissions = ['view_hris_leave_summary', 'view_hris_overtime_summary', 'view_hris_reimbursement_summary', 'view_hris_cash_advance_summary'];
        foreach ($permissions as $name) {
            $db->table('permissions')->updateOrInsert(['name' => $name, 'guard_name' => 'web'], ['created_at' => $now, 'updated_at' => $now]);
        }
        $roleId = $db->table('roles')->where('name', 'hris_admin')->where('guard_name', 'web')->value('id');
        if ($roleId) {
            $ids = $db->table('permissions')->whereIn('name', $permissions)->where('guard_name', 'web')->pluck('id');
            foreach ($ids as $permissionId) $db->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $roleId], []);
        }
    }

    public function down(): void
    {
        $db = DB::connection('core');
        $ids = $db->table('permissions')->whereIn('name', ['view_hris_leave_summary', 'view_hris_overtime_summary', 'view_hris_reimbursement_summary', 'view_hris_cash_advance_summary'])->where('guard_name', 'web')->pluck('id');
        $db->table('role_has_permissions')->whereIn('permission_id', $ids)->delete();
        $db->table('permissions')->whereIn('id', $ids)->delete();
    }
};
