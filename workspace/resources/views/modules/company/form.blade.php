@extends('layouts.app', ['title' => $company->exists ? 'Edit Perusahaan' : 'Tambah Perusahaan'])

@section('content')
@php($selectedRoles = old('roles', $company->roles ?? []))
@php($selectedVisibilityCompanies = old('visibility_company_ids', $company->exists ? $company->operationalVisibility->pluck('id')->all() : []))
@php($hasVendorRelations = $hasVendorRelations ?? false)
<div class="mb-4"><div class="text-uppercase text-secondary small fw-semibold mb-2">Company management</div><h1 class="h3 mb-1">{{ $company->exists ? 'Edit perusahaan' : 'Tambah perusahaan' }}</h1><p class="text-secondary mb-0">Kelola struktur group dan perusahaan internal dalam Basilindo Workspace.</p></div>
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="card border-0 shadow-sm workspace-company-form" data-client-validation="company" novalidate>
    <div class="card-body">
        @csrf @if(($method ?? 'POST') === 'PUT') @method('PUT') @endif
        <div class="company-brand-upload mb-4" data-company-brand-upload>
            <div class="company-brand-upload-card">
                <div class="company-brand-preview" data-company-brand-preview>
                    @if($company->logo_object_key)
                        <img src="{{ route('workspace.company.logo', $company) }}" alt="Logo {{ $company->name }}" data-company-brand-preview-image>
                    @else
                        <span data-company-brand-preview-empty>{{ strtoupper(substr($company->code ?: 'BR', 0, 2)) }}</span>
                    @endif
                </div>
                <div class="company-brand-upload-content">
                    <label class="form-label mb-1">Logo / brand perusahaan <span class="text-secondary fw-normal">(opsional)</span></label>
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" class="form-control @error('logo') is-invalid @enderror" data-company-brand-input>
                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">JPG, PNG, atau WEBP. Maksimal 2 MB.</div>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nama *</label><input name="name" value="{{ old('name', $company->name) }}" class="form-control @error('name') is-invalid @enderror">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label">Kode *</label><input name="code" value="{{ old('code', $company->code) }}" class="form-control @error('code') is-invalid @enderror">@error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label">Klasifikasi *</label>@if($hasVendorRelations)<input type="hidden" name="classification" value="{{ $company->classification }}">@endif<select name="classification" class="form-select @error('classification') is-invalid @enderror" @disabled($hasVendorRelations)><option value="">Pilih klasifikasi</option><option value="internal" @selected(old('classification', $company->classification) === 'internal')>Internal</option><option value="external" @selected(old('classification', $company->classification) === 'external')>External</option></select>@error('classification')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6" data-company-role-control>
                @if($hasVendorRelations)<input type="hidden" name="roles[]" value="vendor">@endif
                <div class="workspace-floating-field">
                    <div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown>
                        <button type="button" class="form-select text-start workspace-dropdown-trigger" data-company-role-trigger data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <span data-dropdown-label>Pilih peran bisnis</span>
                        </button>
                        <div class="dropdown-menu workspace-dropdown-menu p-2">
                            <input type="search" class="form-control form-control-sm mb-2" placeholder="Cari peran..." data-dropdown-search>
                            <div class="workspace-dropdown-options">
                                <label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="roles[]" value="operating_company" data-dropdown-item @checked(in_array('operating_company', $selectedRoles, true))><span>Perusahaan operasional</span></label>
                                <label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="roles[]" value="client" data-dropdown-item @checked(in_array('client', $selectedRoles, true))><span>Client</span></label>
                                <label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="roles[]" value="vendor" data-dropdown-item data-company-vendor-role @disabled($hasVendorRelations) @checked($hasVendorRelations || in_array('vendor', $selectedRoles, true))><span>Vendor</span></label>
                                <label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="roles[]" value="partner" data-dropdown-item @checked(in_array('partner', $selectedRoles, true))><span>Partner</span></label>
                            </div>
                        </div>
                    </div>
                    <label class="form-label">Peran bisnis perusahaan</label>
                </div>
                <div class="form-text" data-company-role-hint>Isi jenis entitas dan klasifikasi terlebih dahulu untuk memilih peran bisnis.</div>
                @error('roles')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6"><label class="form-label">Jenis entitas *</label>@if($hasVendorRelations)<input type="hidden" name="entity_type" value="{{ $company->entity_type }}">@endif<select name="entity_type" class="form-select @error('entity_type') is-invalid @enderror" @disabled($hasVendorRelations)><option value="">Pilih jenis entitas</option><option value="group" @selected(old('entity_type', $company->entity_type) === 'group')>Group</option><option value="company" @selected(old('entity_type', $company->entity_type) === 'company')>Company</option></select>@error('entity_type')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6" data-company-parent-control><label class="form-label">Parent perusahaan</label><select name="parent_company_id" data-company-parent class="form-select @error('parent_company_id') is-invalid @enderror"><option value="">Tidak memiliki parent</option>@foreach($parentCompanies as $parent)<option value="{{ $parent->getKey() }}" data-parent-classification="{{ $parent->classification }}" @selected((string) old('parent_company_id', $company->parent_company_id) === (string) $parent->getKey())>{{ $parent->name }} ({{ $parent->code }})</option>@endforeach</select><div class="form-text">Parent harus memiliki klasifikasi yang sama.</div>@error('parent_company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6 d-none" data-company-group-role-hint><div class="alert alert-primary mb-0 h-100 d-flex align-items-center">Role <strong class="mx-1">Group</strong> ditetapkan otomatis berdasarkan jenis entitas.</div></div>
            <div class="col-md-6" data-company-visibility-control>
                <div class="workspace-floating-field">
                    <div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-empty-label="Global — semua perusahaan operasional">
                        <button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <span data-dropdown-label>Pilih perusahaan operasional</span>
                        </button>
                        <div class="dropdown-menu workspace-dropdown-menu p-2">
                            <input type="search" class="form-control form-control-sm mb-2" placeholder="Cari kode perusahaan..." data-dropdown-search>
                            <div class="workspace-dropdown-options">
                                @forelse($operationalCompanies as $operationalCompany)
                                    <label class="workspace-dropdown-option" data-dropdown-option>
                                        <input type="checkbox" name="visibility_company_ids[]" value="{{ $operationalCompany->getKey() }}" data-dropdown-item data-dropdown-value="{{ $operationalCompany->code }}" @checked(in_array($operationalCompany->getKey(), $selectedVisibilityCompanies))>
                                        <span><strong>{{ $operationalCompany->code }}</strong><small>{{ $operationalCompany->name }}</small></span>
                                    </label>
                                @empty
                                    <div class="text-secondary small p-2">Belum ada perusahaan operasional internal yang aktif.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <label class="form-label">Terlihat pada perusahaan operasional</label>
                </div>
                <div class="form-text">Kosongkan untuk visibilitas global ke semua perusahaan operasional. Jika diisi, hanya kode yang dipilih yang dapat melihat dan menggunakan data ini. Hak akses user tetap berlaku.</div>
                @error('visibility_company_ids.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6" data-company-status-control><label class="form-label">Status *</label><select name="status" class="form-select @error('status') is-invalid @enderror"><option value="active" @selected(old('status', $company->status ?: 'active') === 'active')>Aktif</option><option value="inactive" @selected(old('status', $company->status) === 'inactive')>Nonaktif</option></select>@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2"><button class="btn btn-primary">Simpan</button><a href="{{ route('workspace.company.companies') }}" class="btn btn-outline-secondary">Batal</a></div>
</form>
@endsection
