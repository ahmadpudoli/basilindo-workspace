<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('core')->hasTable('permissions') || ! Schema::connection('core')->hasTable('roles')) {
            return;
        }

        $db = DB::connection('core');
        $now = now();
        $db->table('permissions')->updateOrInsert(
            ['name' => 'view_email_queue', 'guard_name' => 'web'],
            ['updated_at' => $now, 'created_at' => $now],
        );

        if (Schema::connection('core')->hasTable('role_has_permissions')) {
            $permissionId = $db->table('permissions')
                ->where('name', 'view_email_queue')
                ->where('guard_name', 'web')
                ->value('id');
            $adminRoleIds = $db->table('roles')
                ->where('guard_name', 'web')
                ->whereIn('name', ['super_admin', 'admin'])
                ->pluck('id');

            foreach ($adminRoleIds as $roleId) {
                $db->table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Keep the permission and grants so rollback never revokes live access.
    }
};
