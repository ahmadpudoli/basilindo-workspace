<?php

use Core\Models\Company;
use Core\Models\Permission;
use Core\Models\Role;
use Core\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $companies = [
            ['name' => 'Group Basilindo', 'code' => 'BASILINDO', 'entity_type' => 'group', 'roles' => []],
            ['name' => 'PT Lintas Telematika Nusantara', 'code' => 'LTN', 'entity_type' => 'company', 'roles' => ['operating_company']],
            ['name' => 'PT Legospay Mitra Sejati', 'code' => 'LEGOSPAY', 'entity_type' => 'company', 'roles' => ['operating_company']],
            ['name' => 'PT Quinsis Lintas Mitra', 'code' => 'QUINSIS', 'entity_type' => 'company', 'roles' => ['operating_company']],
            ['name' => 'PT Mitratama Mahadirga Makmur', 'code' => 'M3', 'entity_type' => 'company', 'roles' => ['operating_company']],
            ['name' => 'PT Maga Inti Solusi', 'code' => 'MAGIS', 'entity_type' => 'company', 'roles' => ['operating_company']],
            ['name' => 'PT Carano Tech Solusi', 'code' => 'CARANO', 'entity_type' => 'company', 'roles' => ['operating_company']],
        ];

        $group = null;
        foreach ($companies as $definition) {
            $company = Company::query()->firstOrNew(['code' => $definition['code']]);
            $company->fill([
                'name' => $definition['name'],
                'entity_type' => $definition['entity_type'],
                'classification' => 'internal',
                'roles' => $definition['roles'],
                'status' => 'active',
            ]);
            $company->save();

            if ($definition['code'] === 'BASILINDO') {
                $group = $company;
            }
        }

        if ($group) {
            Company::query()
                ->where('entity_type', 'company')
                ->whereIn('code', collect($companies)->pluck('code')->reject(fn (string $code): bool => $code === 'BASILINDO'))
                ->update(['parent_company_id' => $group->getKey()]);
        }

        $permissionNames = $this->permissionNames();
        foreach ($permissionNames as $permissionName) {
            Permission::query()->firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $permissions = Permission::query()->where('guard_name', 'web')->whereIn('name', $permissionNames)->get()->keyBy('name');
        foreach ($this->rolePermissions($permissionNames) as $roleName => $rolePermissionNames) {
            $role = Role::query()->firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->givePermissionTo(collect($rolePermissionNames)->map(fn (string $name) => $permissions[$name]));
        }

        $admin = User::query()->whereRaw('LOWER(email) = ?', ['admin@basilindo.co.id'])->first();
        $migratedLegacyAccount = false;
        if (! $admin) {
            $admin = User::query()->whereRaw('LOWER(email) = ?', ['admin@example.com'])->first();
            $migratedLegacyAccount = (bool) $admin;
        }
        if (! $admin) {
            $admin = new User;
        }

        $admin->name = 'Basilindo Super Admin';
        $admin->email = 'admin@basilindo.co.id';
        $admin->is_system_account = true;
        if (! $admin->exists || $migratedLegacyAccount) {
            $admin->password = Hash::make('password');
        }
        $admin->save();

        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }
        $groupId = Company::query()->where('code', 'BASILINDO')->value('id');
        $allowedCompanyIds = Company::query()
            ->where('status', 'active')
            ->where('classification', 'internal')
            ->where(function ($query) use ($groupId): void {
                $query->where('code', 'BASILINDO')
                    ->when($groupId, fn ($nested) => $nested->orWhere('parent_company_id', $groupId));
            })
            ->pluck('id')
            ->mapWithKeys(fn (int $id): array => [$id => ['scope_role' => 'member']])
            ->all();
        $admin->companies()->sync($allowedCompanyIds);
    }

    /** @return list<string> */
    private function permissionNames(): array
    {
        $resources = [
            'company', 'project', 'vendor', 'document_type', 'document', 'user',
            'crm_account', 'crm_lead', 'crm_opportunity', 'crm_activity', 'ticket',
        ];

        return collect($resources)
            ->flatMap(fn (string $resource): array => collect(['view', 'view_any', 'create', 'update', 'delete'])
                ->map(fn (string $action): string => $action.'_'.$resource)->all())
            ->push(
                'access_settings',
                'access_company',
                'access_crm',
                'access_project_management',
                'access_documents',
                'view_document_audit_trail',
            )
            ->values()
            ->all();
    }

    /** @param list<string> $all */
    private function rolePermissions(array $all): array
    {
        $document = [
            'access_company', 'access_project_management', 'access_documents',
            'view_company', 'view_any_company', 'view_project', 'view_any_project',
            'view_vendor', 'view_any_vendor', 'view_document_type', 'view_any_document_type',
            'view_document', 'view_any_document', 'create_document', 'update_document',
            'delete_document', 'create_document_type', 'update_document_type',
        ];

        $crm = [
            'access_company', 'access_crm',
            'view_company', 'view_any_company',
            'view_crm_account', 'view_any_crm_account', 'create_crm_account', 'update_crm_account', 'delete_crm_account',
            'view_crm_lead', 'view_any_crm_lead', 'create_crm_lead', 'update_crm_lead', 'delete_crm_lead',
            'view_crm_opportunity', 'view_any_crm_opportunity', 'create_crm_opportunity', 'update_crm_opportunity', 'delete_crm_opportunity',
            'view_crm_activity', 'view_any_crm_activity', 'create_crm_activity', 'update_crm_activity', 'delete_crm_activity',
        ];

        $project = [
            'access_company', 'access_project_management',
            'view_company', 'view_any_company', 'view_project', 'view_any_project', 'create_project', 'update_project', 'delete_project',
            'view_ticket', 'view_any_ticket', 'create_ticket', 'update_ticket', 'delete_ticket',
        ];

        return [
            'super_admin' => $all,
            'admin' => array_values(array_diff($all, ['delete_user'])),
            'member' => ['access_company', 'access_project_management', 'access_documents', 'view_company', 'view_any_company', 'view_project', 'view_any_project', 'view_vendor', 'view_any_vendor', 'view_document_type', 'view_any_document_type', 'view_document', 'view_any_document'],
            'finance_admin' => array_merge($document, ['view_document_audit_trail']),
            'document_admin' => array_merge($document, ['view_document_audit_trail']),
            'finance_reviewer' => array_merge(array_values(array_filter($document, fn (string $name): bool => str_starts_with($name, 'view'))), ['view_document_audit_trail']),
            'crm_admin' => $crm,
            'crm_member' => array_values(array_filter($crm, fn (string $name): bool => str_starts_with($name, 'view'))),
            'project_admin' => $project,
            'project_member' => array_values(array_filter($project, fn (string $name): bool => str_starts_with($name, 'view'))),
            'auditor' => ['view_document_audit_trail'],
        ];
    }

    public function down(): void
    {
        // Master data is retained on rollback to avoid deleting business data or access history.
    }
};
