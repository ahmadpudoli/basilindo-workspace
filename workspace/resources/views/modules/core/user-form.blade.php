@extends('layouts.app', ['title' => isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna'])

@section('content')
@php
    $selectedRoles = old('roles', isset($user) ? $user->roles->pluck('name')->all() : []);
    $selectedCompanies = old('company_ids', isset($user) ? $user->companies->modelKeys() : []);
    $selectedPermissions = old('permissions', isset($user) ? $user->permissions->pluck('name')->all() : []);
    $selectedEmployeeId = old('employee_id', isset($user) ? $employees->firstWhere('user_id', $user->getKey())?->getKey() : null);
    $isProtectedSystemUser = isset($user) && $user->isProtectedSystemAccount();
    if ($isProtectedSystemUser) {
        $selectedPermissions = $permissions->pluck('name')->all();
    }
    $modulePermissionGroups = [
        'access_company' => 'Company & Vendor',
        'access_crm' => 'CRM',
        'access_project_management' => 'Project Management',
        'access_documents' => 'Document Management',
        'access_hris' => 'HRIS',
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

<div class="mb-4"><div class="text-uppercase text-secondary small fw-semibold mb-2">Pengaturan akun</div><h1 class="h3 mb-1">{{ isset($user) ? 'Edit pengguna' : 'Tambah pengguna' }}</h1><p class="text-secondary mb-0">Atur role, permission tambahan, dan perusahaan yang dapat diakses pengguna.</p></div>
<form method="POST" action="{{ $action ?? route('workspace.core.users.store') }}" class="card border-0 shadow-sm">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger workspace-alert mb-4" role="alert">
                <strong>Perubahan pengguna belum disimpan.</strong>
                <ul class="mb-0 mt-2">@foreach(array_unique($errors->all()) as $message)<li>{{ $message }}</li>@endforeach</ul>
            </div>
        @endif
        @if($isProtectedSystemUser)<div class="alert alert-warning">Akun sistem ini memiliki akses penuh. Role, perusahaan, dan permission dikunci.</div>@endif
        @csrf @if(isset($user)) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nama *</label><input name="name" value="{{ old('name', $user->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label">Email *</label><input name="email" type="email" value="{{ old('email', $user->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '*' }}</label><input name="password" type="password" class="form-control @error('password') is-invalid @enderror" {{ isset($user) ? '' : 'required' }}>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label">Konfirmasi password</label><input name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror">@error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12"><label class="form-label">Karyawan terkait <span class="text-secondary fw-normal">(opsional untuk akun non-karyawan)</span></label><select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" @disabled($isProtectedSystemUser)><option value="">Tidak ada relasi karyawan</option>@foreach($employees as $employee)<option value="{{ $employee->getKey() }}" @selected((string) $selectedEmployeeId === (string) $employee->getKey())>{{ $employee->full_name }} — NIP {{ $employee->employee_number }}{{ $employee->company ? ' — '.$employee->company->name : '' }}</option>@endforeach</select>@error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror<div class="form-text">Satu karyawan hanya dapat direlasikan ke satu akun Workspace.</div></div>

            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-2"><label class="form-label mb-0">Role pengguna</label><span class="text-secondary small">Dapat memilih lebih dari satu role</span></div>
                <div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown>
                    <button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>Pilih role</span></button>
                    <div class="dropdown-menu workspace-dropdown-menu p-2">
                        <input type="search" class="form-control form-control-sm mb-2" placeholder="Cari role..." data-dropdown-search>
                        <div class="workspace-dropdown-options">
                            @foreach($roles as $role)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="roles[]" value="{{ $role->name }}" @checked(in_array($role->name, $selectedRoles, true)) @disabled($isProtectedSystemUser) data-dropdown-item><span>{{ $role->name }}</span></label>@endforeach
                        </div>
                    </div>
                    <div class="form-text">Pilih satu atau beberapa role dari daftar.</div>
                </div>
            </div>

            <div class="col-12"><label class="form-label">Perusahaan internal yang dapat diakses</label>@if($companies->isNotEmpty())<div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>Pilih perusahaan</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><input type="search" class="form-control form-control-sm mb-2" placeholder="Cari perusahaan..." data-dropdown-search><div class="workspace-dropdown-options">@foreach($companies as $company)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="company_ids[]" value="{{ $company->getKey() }}" @checked(in_array($company->getKey(), $selectedCompanies)) @disabled($isProtectedSystemUser) data-dropdown-item data-dropdown-value="{{ $company->code }}"><span><strong>{{ $company->name }}</strong><small>{{ $company->code }}</small></span></label>@endforeach</div></div><div class="form-text">Group dan perusahaan eksternal tidak ditampilkan. Pilih satu atau beberapa perusahaan.</div></div>@else<div class="alert alert-light border mb-0">Belum ada perusahaan internal bertipe company yang aktif.</div>@endif</div>

            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-2"><label class="form-label mb-0">Permission tambahan pengguna</label><span class="text-secondary small">Dikelompokkan berdasarkan modul</span></div>
                @include('modules.partials.permission-assignment-groups', ['permissionGroups' => $permissionGroups, 'permissionPanelPrefix' => 'userPermissionGroup', 'selectedPermissions' => $selectedPermissions, 'isProtected' => $isProtectedSystemUser])
                <div class="form-text mt-3">Permission ini menambah hak dari role pengguna.</div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2"><button class="btn btn-primary">Simpan pengguna</button><a href="{{ route('workspace.core.users') }}" class="btn btn-outline-secondary">Batal</a></div>
</form>
@endsection
