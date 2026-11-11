<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::connection('core')->table('permissions')->updateOrInsert(['name' => 'manage_numbering_rules', 'guard_name' => 'web'], ['updated_at' => now(), 'created_at' => now()]);
        $permissionId = DB::connection('core')->table('permissions')->where('name', 'manage_numbering_rules')->where('guard_name', 'web')->value('id');
        foreach (['super_admin', 'admin'] as $roleName) {
            $roleId = DB::connection('core')->table('roles')->where('name', $roleName)->where('guard_name', 'web')->value('id');
            if ($roleId) DB::connection('core')->table('role_has_permissions')->insertOrIgnore(['permission_id' => $permissionId, 'role_id' => $roleId]);
        }
    }

    public function down(): void {}
};
