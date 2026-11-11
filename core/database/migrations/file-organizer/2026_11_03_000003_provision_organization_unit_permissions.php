<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $names = ['view_organization_unit', 'view_any_organization_unit', 'create_organization_unit', 'update_organization_unit', 'delete_organization_unit'];
        foreach ($names as $name) DB::connection('core')->table('permissions')->updateOrInsert(['name' => $name, 'guard_name' => 'web'], ['created_at' => $now, 'updated_at' => $now]);
        $permissionIds = DB::connection('core')->table('permissions')->whereIn('name', $names)->pluck('id');
        $roleIds = DB::connection('core')->table('roles')->whereIn('name', ['super_admin', 'admin'])->pluck('id');
        foreach ($roleIds as $roleId) foreach ($permissionIds as $permissionId) DB::connection('core')->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $roleId]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Assignment dipertahankan agar rollback aplikasi tidak menghapus akses yang sudah diberikan.
    }
};
