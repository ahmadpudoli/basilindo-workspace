<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $connection = DB::connection('core');
        $permissionId = $connection->table('permissions')->where('name', 'view_audit_trail')->where('guard_name', 'web')->value('id');
        if (! $permissionId) {
            $permissionId = $connection->table('permissions')->insertGetId([
                'name' => 'view_audit_trail',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id');
        }

        $roleNames = ['document_admin', 'finance_admin', 'finance_reviewer', 'auditor'];
        $roles = $connection->table('roles')->whereIn('name', $roleNames)->where('guard_name', 'web')->get(['id', 'name']);

        if (! $roles->contains('name', 'auditor')) {
            $auditorId = $connection->table('roles')->insertGetId([
                'name' => 'auditor',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id');
            $roles->push((object) ['id' => $auditorId, 'name' => 'auditor']);
        }

        foreach ($roles as $role) {
            $connection->table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }
    }

    public function down(): void
    {
        $connection = DB::connection('core');
        $permissionId = $connection->table('permissions')->where('name', 'view_audit_trail')->where('guard_name', 'web')->value('id');

        if (! $permissionId) {
            return;
        }

        $connection->table('role_has_permissions')->where('permission_id', $permissionId)->delete();
        $connection->table('permissions')->where('id', $permissionId)->delete();
    }
};
