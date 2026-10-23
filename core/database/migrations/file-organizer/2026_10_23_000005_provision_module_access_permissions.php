<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'access_company',
            'access_crm',
            'access_project_management',
            'access_documents',
        ];

        foreach ($permissions as $name) {
            DB::connection('core')->table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['updated_at' => now(), 'created_at' => now()],
            );
        }

        $permissionIds = DB::connection('core')->table('permissions')
            ->whereIn('name', $permissions)
            ->pluck('id', 'name');

        $moduleSignals = [
            'access_company' => ['view_any_company', 'view_any_vendor'],
            'access_crm' => ['view_any_crm_account', 'view_any_crm_lead', 'view_any_crm_opportunity', 'view_any_crm_activity'],
            'access_project_management' => ['view_any_project', 'view_any_ticket'],
            'access_documents' => ['view_any_document', 'view_any_document_type'],
        ];

        $roles = DB::connection('core')->table('roles')->get();
        foreach ($roles as $role) {
            foreach ($moduleSignals as $accessPermission => $signals) {
                $hasSignal = DB::connection('core')->table('role_has_permissions')
                    ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where('role_has_permissions.role_id', $role->id)
                    ->whereIn('permissions.name', $signals)
                    ->exists();

                if (($role->name === 'super_admin' || $hasSignal) && isset($permissionIds[$accessPermission])) {
                    DB::connection('core')->table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $permissionIds[$accessPermission],
                        'role_id' => $role->id,
                    ], []);
                }
            }
        }
    }

    public function down(): void
    {
        $permissionIds = DB::connection('core')->table('permissions')
            ->whereIn('name', ['access_company', 'access_crm', 'access_project_management', 'access_documents'])
            ->pluck('id');

        DB::connection('core')->table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::connection('core')->table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
