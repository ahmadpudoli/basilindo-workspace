@extends('layouts.app', ['title' => 'Biodata Karyawan'])

@section('content')
<div class="mb-4">
    <div class="text-uppercase text-secondary small fw-semibold mb-2">Profil HRIS</div>
    <h1 class="h3 mb-1">Biodata</h1>
    <p class="text-secondary mb-0">Data biodata dan struktur organisasi karyawan yang terhubung dengan akun Anda.</p>
</div>

<div class="card mb-3">
    <div class="card-header">Biodata saya</div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-2">Nama</dt>
            <dd class="col-sm-4">{{ $employee->full_name }}</dd>
            <dt class="col-sm-2">NIP</dt>
            <dd class="col-sm-4">{{ $employee->employee_number }}</dd>
            <dt class="col-sm-2">Perusahaan</dt>
            <dd class="col-sm-4">{{ $employee->company?->name ?? '-' }}</dd>
            <dt class="col-sm-2">Jabatan</dt>
            <dd class="col-sm-4">{{ $employee->job_title ?? '-' }}</dd>
            <dt class="col-sm-2">Tanggal mulai</dt>
            <dd class="col-sm-4">{{ $employee->hire_date?->format('d/m/Y') ?? '-' }}</dd>
            <dt class="col-sm-2">Status</dt>
            <dd class="col-sm-4">{{ ucfirst($employee->status) }}</dd>
        </dl>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Struktur organisasi</span>
        <small class="text-secondary">Klik nama untuk buka/tutup cabang</small>
    </div>
    <div class="card-body">
        <div class="workspace-org-tree workspace-org-tree-root">
            @include('modules.partials.hris-employee-org-tree', ['node' => $organizationTree])
        </div>
    </div>
</div>
@endsection
