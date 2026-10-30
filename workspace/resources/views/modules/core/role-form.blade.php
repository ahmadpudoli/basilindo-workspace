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
        'download_employee_documents' => 'HRIS',
        'manage_employee_documents' => 'HRIS',
        'upload_employee_documents' => 'HRIS',
        'view_employee_documents' => 'HRIS',
        'view_own_employee_documents' => 'HRIS',
        'access_settings' => 'Core & Pengaturan',
    ];
    $permissionGroups = $permissions->groupBy(function ($permission) use ($modulePermissionGroups) {
        $name = $permission->name;

        return match (true) {
            isset($modulePermissionGroups[$name]) => $modulePermissionGroups[$name],
            str_contains($name, 'company') || str_contains($name, 'vendor') => 'Company & Vendor',
            str_contains($name, 'crm_') => 'CRM',
            str_contains($name, 'document') => 'Document Management',
            str_contains($name, 'project') || str_contains($name, 'ticket') || str_contains($name, 'epic') || str_contains($name, 'note') || str_contains($name, 'comment') => 'Project Management',
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
                @include('modules.partials.permission-assignment-groups', ['permissionGroups' => $permissionGroups, 'permissionPanelPrefix' => 'rolePermissionGroup', 'selectedPermissions' => $selectedPermissions, 'isProtected' => $isProtectedSuperAdminRole])
                <div class="form-text mt-3">Role <code>super_admin</code> selalu dipulihkan ke seluruh permission. Untuk menampilkan menu HRIS, centang <code>access_hris</code>; permission fitur tetap mengatur halaman dan aksi secara granular.@if($isProtectedSuperAdminRole) Permission role ini dikunci dan tidak dapat dikurangi.@endif</div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2"><button class="btn btn-primary">Simpan role</button><a href="{{ route('workspace.core.roles') }}" class="btn btn-outline-secondary">Batal</a></div>
</form>
@endsection
