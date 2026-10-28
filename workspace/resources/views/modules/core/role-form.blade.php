@extends('layouts.app', ['title' => isset($role) ? 'Edit Role' : 'Tambah Role'])

@section('content')
@php
    $selectedPermissions = old('permissions', isset($role) ? $role->permissions->pluck('name')->all() : []);
    $isProtectedSuperAdminRole = isset($role) && $role->name === 'super_admin';
    $modulePermissionGroups = [
        'access_company' => 'Company & Vendor',
        'access_crm' => 'CRM',
        'access_project_management' => 'Project Management',
        'access_documents' => 'Document Management',
        'access_hris' => 'HRIS',
        'approve_hris_request' => 'HRIS',
        'manage_hris_settings' => 'HRIS',
        'view_hris_approvals' => 'HRIS',
        'view_hris_leave_summary' => 'HRIS',
        'view_hris_overtime_summary' => 'HRIS',
        'view_hris_reimbursement_summary' => 'HRIS',
        'view_hris_cash_advance_summary' => 'HRIS',
        'access_settings' => 'Core & Pengaturan',
    ];
    $permissionGroups = $permissions->groupBy(function ($permission) use ($modulePermissionGroups) {
        $name = $permission->name;

        return match (true) {
            isset($modulePermissionGroups[$name]) => $modulePermissionGroups[$name],
            str_contains($name, 'company') || str_contains($name, 'vendor') => 'Company & Vendor',
            str_contains($name, 'crm_') => 'CRM',
            str_contains($name, 'document') => 'Document Management',
            str_contains($name, 'project') || str_contains($name, 'ticket') => 'Project Management',
            str_contains($name, 'hris') || str_ends_with($name, '_employee') || str_ends_with($name, '_leave_request') || str_ends_with($name, '_overtime') || str_ends_with($name, '_claim') => 'HRIS',
            str_contains($name, 'user') || str_contains($name, 'role') || str_contains($name, 'audit') || $name === 'access_settings' => 'Core & Pengaturan',
            default => 'Lainnya',
        };
    });
    $groupOrder = ['Core & Pengaturan', 'Company & Vendor', 'CRM', 'Project Management', 'Document Management', 'HRIS', 'Lainnya'];
    $permissionGroups = $permissionGroups->sortBy(fn ($items, $group) => array_search($group, $groupOrder, true));
@endphp

<div class="mb-4">
    <div class="text-uppercase text-secondary small fw-semibold mb-2">Pengaturan authorization</div>
    <h1 class="h3 mb-1">{{ isset($role) ? 'Edit role' : 'Tambah role' }}</h1>
    <p class="text-secondary mb-0">Atur hak akses group pengguna berdasarkan tanggung jawabnya.</p>
</div>

<form method="POST" action="{{ isset($role) ? route('workspace.core.roles.update', $role) : route('workspace.core.roles.store') }}" class="card border-0 shadow-sm">
    <div class="card-body">
        @csrf
        @if(isset($role)) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nama role *</label><input name="name" value="{{ old('name', $role->name ?? '') }}" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Guard *</label><input name="guard_name" value="{{ old('guard_name', $role->guard_name ?? 'web') }}" class="form-control" required></div>
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-2"><label class="form-label mb-0">Permission role</label><span class="text-secondary small">Kelompokkan berdasarkan modul</span></div>
                <div class="workspace-permission-groups">
                    @foreach($permissionGroups as $group => $groupPermissions)
                        @php($permissionPanelId = 'rolePermissionGroup'.$loop->index)
                        <section class="workspace-permission-group">
                            <div class="workspace-permission-group-header">
                                <button type="button" class="workspace-permission-collapse-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#{{ $permissionPanelId }}" aria-expanded="false" aria-controls="{{ $permissionPanelId }}">
                                    <span><strong>{{ $group }}</strong><small>{{ $groupPermissions->count() }} permission</small></span>
                                    <span class="workspace-permission-chevron" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m7 9 5 5 5-5"/></svg></span>
                                </button>
                                <label class="workspace-permission-select-all"><input type="checkbox" data-permission-group-toggle><span>Pilih semua</span></label>
                            </div>
                            <div id="{{ $permissionPanelId }}" class="collapse">
                                <div class="workspace-permission-grid">
                                    @foreach($groupPermissions as $permission)
                                        <label class="workspace-permission-option"><input type="checkbox" name="permissions[]" value="{{ $permission->name }}" data-permission-item @checked($isProtectedSuperAdminRole || in_array($permission->name, $selectedPermissions, true)) @disabled($isProtectedSuperAdminRole)><span>{{ $permission->name }}</span></label>
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    @endforeach
                </div>
                <div class="form-text mt-3">Role <code>super_admin</code> selalu dipulihkan ke seluruh permission.@if($isProtectedSuperAdminRole) Permission role ini dikunci dan tidak dapat dikurangi.@endif</div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2"><button class="btn btn-primary">Simpan role</button><a href="{{ route('workspace.core.roles') }}" class="btn btn-outline-secondary">Batal</a></div>
</form>
@endsection
