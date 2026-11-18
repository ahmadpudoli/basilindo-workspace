<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PERMISSIONS = [
        'print_hris_reimbursement',
        'print_hris_cash_advance',
        'print_hris_overtime',
        'print_hris_leave',
    ];

    public function up(): void
    {
        $schema = Schema::connection('core');
        if (! $schema->hasTable('permissions')) return;

        $db = DB::connection('core');
        $now = now();
        foreach (self::PERMISSIONS as $name) {
            $db->table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['updated_at' => $now, 'created_at' => $now],
            );
        }

        if (! $schema->hasTable('roles') || ! $schema->hasTable('role_has_permissions')) return;

        $permissionIds = $db->table('permissions')
            ->where('guard_name', 'web')
            ->whereIn('name', self::PERMISSIONS)
            ->pluck('id');
        $roleIds = $db->table('roles')
            ->where('guard_name', 'web')
            ->where('name', 'super_admin')
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                $db->table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Keep master permission rows and grants to preserve assigned access.
    }
};
