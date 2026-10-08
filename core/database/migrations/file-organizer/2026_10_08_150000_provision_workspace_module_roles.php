<?php

use Core\Models\Permission;
use Core\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $this->provisionRole('crm_admin', [
            'view_company', 'view_any_company',
            'view_crm_account', 'view_any_crm_account', 'create_crm_account', 'update_crm_account', 'delete_crm_account',
            'view_crm_lead', 'view_any_crm_lead', 'create_crm_lead', 'update_crm_lead', 'delete_crm_lead',
            'view_crm_opportunity', 'view_any_crm_opportunity', 'create_crm_opportunity', 'update_crm_opportunity', 'delete_crm_opportunity',
            'view_crm_activity', 'view_any_crm_activity', 'create_crm_activity', 'update_crm_activity', 'delete_crm_activity',
        ]);

        $this->provisionRole('crm_member', [
            'view_company', 'view_any_company',
            'view_crm_account', 'view_any_crm_account',
            'view_crm_lead', 'view_any_crm_lead',
            'view_crm_opportunity', 'view_any_crm_opportunity',
            'view_crm_activity', 'view_any_crm_activity',
        ]);

        $this->provisionRole('project_admin', [
            'view_company', 'view_any_company', 'view_project', 'view_any_project', 'create_project', 'update_project', 'delete_project',
            'view_ticket', 'view_any_ticket', 'create_ticket', 'update_ticket', 'delete_ticket',
        ]);

        $this->provisionRole('project_member', [
            'view_company', 'view_any_company', 'view_project', 'view_any_project', 'view_ticket', 'view_any_ticket',
        ]);
    }

    private function provisionRole(string $roleName, array $permissionNames): void
    {
        $permissions = collect($permissionNames)->map(function (string $name): Permission {
            return Permission::on('pgsql')->firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        });

        $role = Role::on('pgsql')->firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        // Add only missing permissions; never revoke existing access in an upgrade.
        $role->givePermissionTo($permissions);
    }

    public function down(): void
    {
        // Additive access migrations are intentionally not reversed automatically.
        // Role cleanup requires an explicit administrator decision.
    }
};
