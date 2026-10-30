@extends('layouts.app', ['title' => $holiday->exists ? 'Edit Hari Libur' : 'Tambah Hari Libur'])

@section('content')
@php
    $selectedCompanyIds = collect(old('company_ids', $selectedCompanyIds ?? []))->map(fn ($id) => (int) $id)->all();
@endphp
<div class="d-flex justify-content-between align-items-end gap-3 mb-4"><div><div class="text-uppercase text-secondary small fw-semibold mb-2">Manajemen kehadiran</div><h1 class="h3 mb-1">{{ $holiday->exists ? 'Edit hari libur' : 'Tambah hari libur' }}</h1><p class="text-secondary mb-0">Atur tanggal, cakupan perusahaan, dan dampaknya terhadap kuota cuti.</p></div><a href="{{ route('workspace.hris.holidays') }}" class="btn btn-outline-secondary">Kembali</a></div>
<form method="POST" action="{{ $holiday->exists ? route('workspace.hris.holidays.update', $holiday) : route('workspace.hris.holidays.store') }}" class="card card-body border-0 shadow-sm" style="overflow: visible;">
@csrf @if($holiday->exists) @method('PUT') @endif
<div class="row g-3"><div class="col-md-4"><label class="form-label">Tanggal <span class="text-danger">*</span></label><input type="date" name="holiday_date" value="{{ old('holiday_date', $holiday->holiday_date?->toDateString()) }}" class="form-control @error('holiday_date') is-invalid @enderror" required>@error('holiday_date')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-8"><label class="form-label">Nama hari libur <span class="text-danger">*</span></label><input name="name" value="{{ old('name', $holiday->name) }}" class="form-control @error('name') is-invalid @enderror" maxlength="255" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-4"><label class="form-label">Jenis <span class="text-danger">*</span></label><select name="type" class="form-select" required><option value="national" @selected(old('type', $holiday->type) === 'national')>Hari libur nasional</option><option value="company" @selected(old('type', $holiday->type) === 'company')>Libur perusahaan</option><option value="collective_leave" @selected(old('type', $holiday->type) === 'collective_leave')>Cuti bersama</option></select></div><div class="col-md-8"><label class="form-label">Keterangan</label><textarea name="description" class="form-control" rows="2" maxlength="2000">{{ old('description', $holiday->description) }}</textarea></div></div>
<div class="card border-0 bg-light-subtle mt-4 mb-3" style="overflow: visible;">
    <div class="card-body">
        <h2 class="h6 mb-1">Cakupan perusahaan</h2>
        <p class="small text-secondary mb-3">Pilih satu atau beberapa perusahaan. @if($canGlobal)Kosongkan pilihan untuk menerapkan hari libur secara global.@else Pilihan perusahaan wajib diisi sesuai cakupan akses Anda.@endif</p>
        <label class="form-label">Perusahaan yang berlaku</label>
        <div class="workspace-dropdown-multiselect dropdown @error('company_ids') is-invalid @enderror" data-searchable-dropdown data-dropdown-empty-label="{{ $canGlobal ? 'Global — semua perusahaan' : 'Pilih perusahaan' }}">
            <button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <span data-dropdown-label>{{ count($selectedCompanyIds) ? count($selectedCompanyIds).' perusahaan dipilih' : ($canGlobal ? 'Global — semua perusahaan' : 'Pilih perusahaan') }}</span>
            </button>
            <div class="dropdown-menu workspace-dropdown-menu p-2">
                <input type="search" class="form-control form-control-sm mb-2" placeholder="Cari perusahaan..." data-dropdown-search autocomplete="off">
                <div class="workspace-dropdown-options">
                    @foreach($companies as $company)
                        <label class="workspace-dropdown-option" data-dropdown-option>
                            <input type="checkbox" name="company_ids[]" value="{{ $company->id }}" data-dropdown-item data-dropdown-value="{{ $company->name }} ({{ $company->code }})" @checked(in_array((int) $company->id, $selectedCompanyIds, true))>
                            <span>{{ $company->name }} <small>{{ $company->code }}</small></span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div id="companyHelp" class="form-text">Pilih beberapa perusahaan jika cakupannya terbatas. @if($canGlobal)Tanpa pilihan berarti global.@endif</div>
        @error('company_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        @error('company_ids.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
</div>
<div class="form-check mt-3"><input type="checkbox" name="deduct_annual_leave" value="1" class="form-check-input" id="deductAnnualLeave" @checked(old('deduct_annual_leave', $holiday->deduct_annual_leave))><label class="form-check-label" for="deductAnnualLeave">Kurangi kuota cuti tahunan</label><div class="form-text">Gunakan untuk cuti bersama. Hari libur nasional/perusahaan biasanya tidak mengurangi kuota.</div></div>
<div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('workspace.hris.holidays') }}" class="btn btn-light">Batal</a><button class="btn btn-primary">Simpan hari libur</button></div>
</form>
@endsection
