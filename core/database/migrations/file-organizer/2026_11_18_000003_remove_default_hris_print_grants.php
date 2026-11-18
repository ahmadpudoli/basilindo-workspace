<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('core');
        if (! $schema->hasTable('permissions') || ! $schema->hasTable('roles') || ! $schema->hasTable('role_has_permissions')) return;

        $db = DB::connection('core');
        $permissionIds = $db->table('permissions')
            ->where('guard_name', 'web')
            ->whereIn('name', [
                'print_hris_reimbursement',
                'print_hris_cash_advance',
                'print_hris_overtime',
                'print_hris_leave',
            ])
            ->pluck('id');
        $roleIds = $db->table('roles')
            ->where('guard_name', 'web')
            ->whereIn('name', ['admin', 'hris_admin'])
            ->pluck('id');

        if ($permissionIds->isEmpty() || $roleIds->isEmpty()) return;

        $db->table('role_has_permissions')
            ->whereIn('permission_id', $permissionIds)
            ->whereIn('role_id', $roleIds)
            ->delete();
    }

    public function down(): void
    {
        // Do not restore broad grants; print access should remain explicitly assigned.
    }
};
