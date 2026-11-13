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

        $core = DB::connection('core');
        $now = now();
        $core->table('permissions')->updateOrInsert(
            ['name' => 'view_claim_audit_trail', 'guard_name' => 'web'],
            ['created_at' => $now, 'updated_at' => $now],
        );
        $permissionId = $core->table('permissions')->where('name', 'view_claim_audit_trail')->where('guard_name', 'web')->value('id');
        $roleIds = $core->table('roles')->whereIn('name', ['super_admin', 'hris_admin'])->where('guard_name', 'web')->pluck('id');
        foreach ($roleIds as $roleId) {
            $core->table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $roleId], []);
        }
    }

    public function down(): void
    {
        // Keep the granular audit permission and its grants to preserve existing access decisions.
    }
};
