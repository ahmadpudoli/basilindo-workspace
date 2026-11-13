<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('core');
        if (! $schema->hasTable('permissions') || ! $schema->hasTable('role_has_permissions')) {
            return;
        }

        $core = DB::connection('core');
        $approvalPermissionId = $core->table('permissions')
            ->where('name', 'view_hris_approvals')
            ->where('guard_name', 'web')
            ->value('id');
        $auditPermissionId = $core->table('permissions')
            ->where('name', 'view_claim_audit_trail')
            ->where('guard_name', 'web')
            ->value('id');

        if (! $approvalPermissionId || ! $auditPermissionId) {
            return;
        }

        $roleIds = $core->table('role_has_permissions')
            ->where('permission_id', $approvalPermissionId)
            ->pluck('role_id');

        foreach ($roleIds as $roleId) {
            $core->table('role_has_permissions')->updateOrInsert(
                ['permission_id' => $auditPermissionId, 'role_id' => $roleId],
                [],
            );
        }
    }

    public function down(): void
    {
        // Preserve grants: roles may have received the permission through later administration.
    }
};
