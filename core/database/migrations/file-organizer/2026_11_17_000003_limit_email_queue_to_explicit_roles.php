<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('core')->hasTable('role_has_permissions')
            || ! Schema::connection('core')->hasTable('roles')
            || ! Schema::connection('core')->hasTable('permissions')) {
            return;
        }

        $db = DB::connection('core');
        $adminRoleId = $db->table('roles')
            ->where('name', 'admin')
            ->where('guard_name', 'web')
            ->value('id');
        $permissionId = $db->table('permissions')
            ->where('name', 'view_email_queue')
            ->where('guard_name', 'web')
            ->value('id');

        if ($adminRoleId && $permissionId) {
            $db->table('role_has_permissions')
                ->where('role_id', $adminRoleId)
                ->where('permission_id', $permissionId)
                ->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Do not restore a broad email-queue grant during rollback.
    }
};
