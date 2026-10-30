@php
    $featureGroups = [
        'Core & Pengaturan' => ['Modul & akses' => ['access_settings'], 'Pengguna' => ['view_user', 'view_any_user', 'create_user', 'update_user', 'delete_user'], 'Role' => ['view_role', 'view_any_role', 'create_role', 'update_role', 'delete_role'], 'Audit' => ['view_audit_trail']],
        'Company & Vendor' => ['Perusahaan' => ['access_company', 'view_company', 'view_any_company', 'create_company', 'update_company', 'delete_company'], 'Vendor' => ['view_vendor', 'view_any_vendor', 'create_vendor', 'update_vendor', 'delete_vendor']],
        'CRM' => ['Account' => ['access_crm', 'view_crm_account', 'view_any_crm_account', 'create_crm_account', 'update_crm_account', 'delete_crm_account'], 'Lead' => ['view_crm_lead', 'view_any_crm_lead', 'create_crm_lead', 'update_crm_lead', 'delete_crm_lead'], 'Opportunity' => ['view_crm_opportunity', 'view_any_crm_opportunity', 'create_crm_opportunity', 'update_crm_opportunity', 'delete_crm_opportunity'], 'Aktivitas' => ['view_crm_activity', 'view_any_crm_activity', 'create_crm_activity', 'update_crm_activity', 'delete_crm_activity']],
        'Project Management' => ['Project' => ['access_project_management', 'view_project', 'view_any_project', 'create_project', 'update_project', 'delete_project'], 'Ticket' => ['view_ticket', 'view_any_ticket', 'create_ticket', 'update_ticket', 'delete_ticket'], 'Lainnya' => ['view_epic', 'view_any_epic', 'create_epic', 'update_epic', 'delete_epic', 'view_note', 'view_any_note', 'create_note', 'update_note', 'delete_note', 'view_comment', 'view_any_comment', 'create_comment', 'update_comment', 'delete_comment']],
        'Document Management' => ['Dokumen' => ['access_documents', 'view_document', 'view_any_document', 'create_document', 'update_document', 'delete_document'], 'Tipe dokumen' => ['view_document_type', 'view_any_document_type', 'create_document_type', 'update_document_type', 'delete_document_type'], 'Audit dokumen' => ['view_document_audit_trail']],
        'HRIS' => ['Modul & workflow' => ['access_hris', 'approve_hris_request', 'manage_hris_settings'], 'Pegawai' => ['view_employee', 'view_any_employee', 'create_employee', 'update_employee', 'delete_employee'], 'Dokumen karyawan' => ['view_own_employee_documents', 'view_employee_documents', 'upload_employee_documents', 'download_employee_documents', 'manage_employee_documents'], 'Cuti & sakit' => ['view_leave_request', 'view_any_leave_request', 'create_leave_request', 'update_leave_request', 'delete_leave_request'], 'Lembur' => ['view_overtime', 'view_any_overtime', 'create_overtime', 'update_overtime', 'delete_overtime'], 'Claim' => ['view_claim', 'view_any_claim', 'create_claim', 'update_claim', 'delete_claim'], 'Approval & ringkasan' => ['view_hris_approvals', 'view_hris_leave_summary', 'view_hris_overtime_summary', 'view_hris_reimbursement_summary', 'view_hris_cash_advance_summary']],
    ];
@endphp
<div class="workspace-permission-groups">
    @foreach($permissionGroups as $group => $groupPermissions)
        @php
            $permissionPanelId = $permissionPanelPrefix.$loop->index;
            $knownPermissionNames = collect($featureGroups[$group] ?? [])->flatten();
            $features = collect($featureGroups[$group] ?? [])->map(fn (array $names) => $groupPermissions->filter(fn ($permission) => in_array($permission->name, $names, true)))->filter(fn ($items) => $items->isNotEmpty());
            $unassigned = $groupPermissions->reject(fn ($permission) => $knownPermissionNames->contains($permission->name));
            if ($unassigned->isNotEmpty()) $features->put('Lainnya', $unassigned);
        @endphp
        <section class="workspace-permission-group">
            <div class="workspace-permission-group-header"><button type="button" class="workspace-permission-collapse-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#{{ $permissionPanelId }}" aria-expanded="false" aria-controls="{{ $permissionPanelId }}"><span><strong>{{ $group }}</strong><small>{{ $groupPermissions->count() }} permission</small></span><span class="workspace-permission-chevron" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m7 9 5 5 5-5"/></svg></span></button><label class="workspace-permission-select-all"><input type="checkbox" data-permission-group-toggle><span>Pilih semua</span></label></div>
            <div id="{{ $permissionPanelId }}" class="collapse">
                @foreach($features as $featureLabel => $featurePermissions)
                    @php($selectedFeatureCount = $featurePermissions->filter(fn ($permission) => $isProtected || in_array($permission->name, $selectedPermissions, true))->count())
                    <details class="workspace-permission-subgroup">
                        <summary class="workspace-permission-subgroup-title"><span><strong>{{ $featureLabel }}</strong><small>{{ $featurePermissions->count() }} permission</small></span><em>{{ $selectedFeatureCount }} dipilih</em></summary>
                        <div class="workspace-permission-grid">
                            @foreach($featurePermissions as $permission)<label class="workspace-permission-option"><input type="checkbox" name="permissions[]" value="{{ $permission->name }}" data-permission-item @checked($isProtected || in_array($permission->name, $selectedPermissions, true)) @disabled($isProtected)><span>{{ $permission->name }}</span></label>@endforeach
                        </div>
                    </details>
                @endforeach
            </div>
        </section>
    @endforeach
</div>
