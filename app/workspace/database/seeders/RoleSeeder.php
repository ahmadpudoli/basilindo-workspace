<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Core\Models\Role;
use Core\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Daftar resource Filament
        $resources = [
            'company',
            'project',
            'vendor',
            'document_type',
            'document',
            'user',
            'crm_account',
            'crm_lead',
            'crm_opportunity',
            'crm_activity',
            'ticket',
        ];

        $actions = ['view', 'view_any', 'create', 'update', 'delete'];

        // Buat permission granular untuk setiap resource
        $permissions = [];
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
            $permissions[] = $action . '_' . $resource;
            }
        }

        // Insert permissions jika belum ada
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Buat role super_admin, admin, member
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $member = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        $financeAdmin = Role::firstOrCreate(['name' => 'finance_admin', 'guard_name' => 'web']);
        $financeReviewer = Role::firstOrCreate(['name' => 'finance_reviewer', 'guard_name' => 'web']);
        $documentAdmin = Role::firstOrCreate(['name' => 'document_admin', 'guard_name' => 'web']);
        $crmAdmin = Role::firstOrCreate(['name' => 'crm_admin', 'guard_name' => 'web']);
        $projectAdmin = Role::firstOrCreate(['name' => 'project_admin', 'guard_name' => 'web']);
        $crmMember = Role::firstOrCreate(['name' => 'crm_member', 'guard_name' => 'web']);
        $projectMember = Role::firstOrCreate(['name' => 'project_member', 'guard_name' => 'web']);

        // super_admin: semua permission
        $superAdmin->syncPermissions(Permission::all());

        // admin: semua permission kecuali user delete
        $adminPermissions = Permission::whereNotIn('name', ['delete_user'])->get();
        $admin->syncPermissions($adminPermissions);

        $documentPermissions = Permission::whereIn('name', [
            'view_company', 'view_any_company',
            'view_project', 'view_any_project',
            'view_vendor', 'view_any_vendor',
            'view_document_type', 'view_any_document_type',
            'view_document', 'view_any_document', 'create_document', 'update_document',
            'delete_document', 'create_document_type', 'update_document_type',
        ])->get();
        $documentAdmin->syncPermissions($documentPermissions);
        $financeAdmin->syncPermissions($documentPermissions);
        $financeReviewer->syncPermissions($documentPermissions->filter(fn ($permission) => str_starts_with($permission->name, 'view')));

        $crmPermissions = Permission::whereIn('name', [
            'view_company', 'view_any_company',
            'view_crm_account', 'view_any_crm_account', 'create_crm_account', 'update_crm_account', 'delete_crm_account',
            'view_crm_lead', 'view_any_crm_lead', 'create_crm_lead', 'update_crm_lead', 'delete_crm_lead',
            'view_crm_opportunity', 'view_any_crm_opportunity', 'create_crm_opportunity', 'update_crm_opportunity', 'delete_crm_opportunity',
            'view_crm_activity', 'view_any_crm_activity', 'create_crm_activity', 'update_crm_activity', 'delete_crm_activity',
        ])->get();
        $crmAdmin->syncPermissions($crmPermissions);
        $crmMember->syncPermissions($crmPermissions->filter(fn ($permission) => str_starts_with($permission->name, 'view')));

        $projectPermissions = Permission::whereIn('name', [
            'view_company', 'view_any_company', 'view_project', 'view_any_project', 'create_project', 'update_project', 'delete_project',
            'view_ticket', 'view_any_ticket', 'create_ticket', 'update_ticket', 'delete_ticket',
        ])->get();
        $projectAdmin->syncPermissions($projectPermissions);
        $projectMember->syncPermissions($projectPermissions->filter(fn ($permission) => str_starts_with($permission->name, 'view')));

        // Member hanya melihat katalog dan metadata dokumen.
        $memberPermissions = Permission::where(function($q) {
            $q->whereIn('name', [
                'view_company', 'view_any_company',
                'view_project', 'view_any_project',
                'view_vendor', 'view_any_vendor',
                'view_document_type', 'view_any_document_type',
                'view_document', 'view_any_document',
            ]);
        })->get();
        $member->syncPermissions($memberPermissions);

        // Otomatis assign role member ke user baru (hanya contoh, implementasi production sebaiknya di observer User::created)
        // User::whereDoesntHave('roles')->update(['role_id' => $member->id]);
    }
}
