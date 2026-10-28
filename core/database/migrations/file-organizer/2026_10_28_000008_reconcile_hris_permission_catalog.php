<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Keep the HRIS permission catalog and its master role grants complete
     * when this migration is applied to an existing Workspace database.
     */
    public function up(): void
    {
        $schema = Schema::connection('core');

        if (! $schema->hasTable('permissions')
            || ! $schema->hasTable('roles')
            || ! $schema->hasTable('role_has_permissions')) {
            return;
        }

        $db = DB::connection('core');
        $now = now();
        $permissionNames = $this->permissionNames();

        foreach ($permissionNames as $permissionName) {
            $db->table('permissions')->updateOrInsert(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['updated_at' => $now, 'created_at' => $now],
            );
        }

        $permissionIds = $db->table('permissions')
            ->where('guard_name', 'web')
            ->whereIn('name', $permissionNames)
            ->pluck('id', 'name');

        foreach ($this->rolePermissions() as $roleName => $rolePermissionNames) {
            $roleId = $db->table('roles')
                ->where('name', $roleName)
                ->where('guard_name', 'web')
                ->value('id');

            if (! $roleId) {
                $roleId = $db->table('roles')->insertGetId([
                    'name' => $roleName,
                    'guard_name' => 'web',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($rolePermissionNames as $permissionName) {
                $permissionId = $permissionIds->get($permissionName);

                if ($permissionId) {
                    $db->table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $permissionId,
                        'role_id' => $roleId,
                    ], []);
                }
            }
        }
    }

    /**
     * This migration is additive. A rollback intentionally leaves the
     * catalog and grants intact so existing authorization data is not lost.
     */
    public function down(): void
    {
        // No destructive rollback for master authorization data.
    }

    /** @return list<string> */
    private function permissionNames(): array
    {
        $resources = ['employee', 'leave_request', 'overtime', 'claim'];
        $resourcePermissions = collect($resources)
            ->flatMap(fn (string $resource): array => collect(['view', 'view_any', 'create', 'update', 'delete'])
                ->map(fn (string $action): string => $action.'_'.$resource)
                ->all())
            ->all();

        return array_merge([
            'access_hris',
            'approve_hris_request',
            'manage_hris_settings',
            'view_hris_approvals',
            'view_hris_leave_summary',
            'view_hris_overtime_summary',
            'view_hris_reimbursement_summary',
            'view_hris_cash_advance_summary',
        ], $resourcePermissions);
    }

    /** @return array<string, list<string>> */
    private function rolePermissions(): array
    {
        return [
            'hris_admin' => $this->permissionNames(),
            'hris_member' => [
                'access_hris',
                'view_employee', 'view_any_employee',
                'view_leave_request', 'view_any_leave_request',
                'view_overtime', 'view_any_overtime',
                'view_claim', 'view_any_claim',
            ],
            'super_admin' => $this->permissionNames(),
        ];
    }
};
