@extends('layouts.app', ['title' => isset($role) ? 'Edit Role' : 'Tambah Role'])

@section('content')
@php
    $selectedPermissions = old('permissions', isset($role) ? $role->permissions->pluck('name')->all() : []);
    $permissionGroups = $permissions->groupBy(function ($permission) {
        $name = $permission->name;

        return match (true) {
            str_contains($name, 'company') || str_contains($name, 'vendor') => 'Company & Vendor',
            str_contains($name, 'crm_') => 'CRM',
            str_contains($name, 'document') => 'Document Management',
            str_contains($name, 'project') || str_contains($name, 'ticket') => 'Project Management',
            str_contains($name, 'user') || str_contains($name, 'role') || $name === 'access_settings' => 'Core & Pengaturan',
            default => 'Lainnya',
        };
    });
    $groupOrder = ['Core & Pengaturan', 'Company & Vendor', 'CRM', 'Project Management', 'Document Management', 'Lainnya'];
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
                        <section class="workspace-permission-group">
                            <div class="workspace-permission-group-header"><div><h2>{{ $group }}</h2><span>{{ $groupPermissions->count() }} permission</span></div><label class="workspace-permission-select-all"><input type="checkbox" data-permission-group-toggle><span>Pilih semua</span></label></div>
                            <div class="workspace-permission-grid">
                                @foreach($groupPermissions as $permission)
                                    <label class="workspace-permission-option"><input type="checkbox" name="permissions[]" value="{{ $permission->name }}" data-permission-item @checked(in_array($permission->name, $selectedPermissions, true))><span>{{ $permission->name }}</span></label>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
                <div class="form-text mt-3">Role <code>super_admin</code> selalu dipulihkan ke seluruh permission.</div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2"><button class="btn btn-primary">Simpan role</button><a href="{{ route('workspace.core.roles') }}" class="btn btn-outline-secondary">Batal</a></div>
</form>
@endsection
