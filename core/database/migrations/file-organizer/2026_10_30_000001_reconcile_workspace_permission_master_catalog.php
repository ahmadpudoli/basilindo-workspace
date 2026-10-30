<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Keep the permission master catalog complete for the grouped access UI.
     * This is additive and safe for existing roles and direct user grants.
     */
    public function up(): void
    {
        if (! Schema::connection('core')->hasTable('permissions')) return;

        $permissions = [
            'access_company', 'access_crm', 'access_project_management', 'access_documents', 'access_hris', 'access_settings',
            'view_company', 'view_any_company', 'create_company', 'update_company', 'delete_company',
            'view_vendor', 'view_any_vendor', 'create_vendor', 'update_vendor', 'delete_vendor',
            'view_crm_account', 'view_any_crm_account', 'create_crm_account', 'update_crm_account', 'delete_crm_account',
            'view_crm_lead', 'view_any_crm_lead', 'create_crm_lead', 'update_crm_lead', 'delete_crm_lead',
            'view_crm_opportunity', 'view_any_crm_opportunity', 'create_crm_opportunity', 'update_crm_opportunity', 'delete_crm_opportunity',
            'view_crm_activity', 'view_any_crm_activity', 'create_crm_activity', 'update_crm_activity', 'delete_crm_activity',
            'view_project', 'view_any_project', 'create_project', 'update_project', 'delete_project',
            'view_ticket', 'view_any_ticket', 'create_ticket', 'update_ticket', 'delete_ticket',
            'view_epic', 'view_any_epic', 'create_epic', 'update_epic', 'delete_epic',
            'view_note', 'view_any_note', 'create_note', 'update_note', 'delete_note',
            'view_comment', 'view_any_comment', 'create_comment', 'update_comment', 'delete_comment',
            'view_document', 'view_any_document', 'create_document', 'update_document', 'delete_document',
            'view_document_type', 'view_any_document_type', 'create_document_type', 'update_document_type', 'delete_document_type',
            'view_document_audit_trail',
            'access_hris', 'approve_hris_request', 'manage_hris_settings', 'view_hris_approvals',
            'view_hris_leave_summary', 'view_hris_overtime_summary', 'view_hris_reimbursement_summary', 'view_hris_cash_advance_summary',
            'view_employee', 'view_any_employee', 'create_employee', 'update_employee', 'delete_employee',
            'view_employee_documents', 'view_own_employee_documents', 'upload_employee_documents', 'download_employee_documents', 'manage_employee_documents',
            'view_leave_request', 'view_any_leave_request', 'create_leave_request', 'update_leave_request', 'delete_leave_request',
            'view_overtime', 'view_any_overtime', 'create_overtime', 'update_overtime', 'delete_overtime',
            'view_claim', 'view_any_claim', 'create_claim', 'update_claim', 'delete_claim',
            'view_user', 'view_any_user', 'create_user', 'update_user', 'delete_user',
            'view_role', 'view_any_role', 'create_role', 'update_role', 'delete_role', 'view_audit_trail',
        ];

        $db = DB::connection('core');
        $now = now();
        foreach ($permissions as $name) {
            $db->table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['updated_at' => $now, 'created_at' => $now],
            );
        }
    }

    public function down(): void
    {
        // Permission master data is retained on rollback to avoid removing grants.
    }
};
