<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $connection = DB::connection('core');
        $oldPermissionId = $connection->table('permissions')->where('name', 'view_audit_trail')->where('guard_name', 'web')->value('id');
        $newPermissionId = $connection->table('permissions')->where('name', 'view_document_audit_trail')->where('guard_name', 'web')->value('id');

        if (! $newPermissionId) {
            $newPermissionId = $connection->table('permissions')->insertGetId([
                'name' => 'view_document_audit_trail',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id');
        }

        if (! $oldPermissionId) {
            return;
        }

        foreach ($connection->table('role_has_permissions')->where('permission_id', $oldPermissionId)->get(['role_id']) as $rolePermission) {
            $connection->table('role_has_permissions')->insertOrIgnore(['permission_id' => $newPermissionId, 'role_id' => $rolePermission->role_id]);
        }
        foreach ($connection->table('model_has_permissions')->where('permission_id', $oldPermissionId)->get(['model_id', 'model_type']) as $modelPermission) {
            $connection->table('model_has_permissions')->insertOrIgnore(['permission_id' => $newPermissionId, 'model_id' => $modelPermission->model_id, 'model_type' => $modelPermission->model_type]);
        }

        $connection->table('role_has_permissions')->where('permission_id', $oldPermissionId)->delete();
        $connection->table('model_has_permissions')->where('permission_id', $oldPermissionId)->delete();
        $connection->table('permissions')->where('id', $oldPermissionId)->delete();
    }

    public function down(): void
    {
        $connection = DB::connection('core');
        $newPermissionId = $connection->table('permissions')->where('name', 'view_document_audit_trail')->where('guard_name', 'web')->value('id');
        if (! $newPermissionId) return;
        $oldPermissionId = $connection->table('permissions')->where('name', 'view_audit_trail')->where('guard_name', 'web')->value('id') ?: $connection->table('permissions')->insertGetId(['name' => 'view_audit_trail', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()], 'id');

        foreach ($connection->table('role_has_permissions')->where('permission_id', $newPermissionId)->get(['role_id']) as $rolePermission) {
            $connection->table('role_has_permissions')->insertOrIgnore(['permission_id' => $oldPermissionId, 'role_id' => $rolePermission->role_id]);
        }
        foreach ($connection->table('model_has_permissions')->where('permission_id', $newPermissionId)->get(['model_id', 'model_type']) as $modelPermission) {
            $connection->table('model_has_permissions')->insertOrIgnore(['permission_id' => $oldPermissionId, 'model_id' => $modelPermission->model_id, 'model_type' => $modelPermission->model_type]);
        }
        $connection->table('role_has_permissions')->where('permission_id', $newPermissionId)->delete();
        $connection->table('model_has_permissions')->where('permission_id', $newPermissionId)->delete();
        $connection->table('permissions')->where('id', $newPermissionId)->delete();
    }
};
