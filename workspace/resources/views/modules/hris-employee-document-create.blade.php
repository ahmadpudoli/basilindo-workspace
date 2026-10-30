@extends('layouts.app', ['title' => 'Upload Dokumen Karyawan'])

@section('content')
<div class="mb-4">
    <div class="text-uppercase text-secondary small fw-semibold mb-2">Master HRIS</div>
    <h1 class="h3 mb-1">Upload dokumen karyawan</h1>
    <p class="text-secondary mb-0">Kelola kontrak dan dokumen personal dengan metadata yang rapi, aman, dan mudah ditelusuri.</p>
</div>

<form method="POST" action="{{ route('workspace.hris.employee-documents.store') }}" enctype="multipart/form-data" class="card border-0 shadow-sm" data-document-upload novalidate>
    @csrf
    <input type="hidden" name="document_number" value="{{ old('document_number', $documentNumber) }}">
    <div class="card-body p-4 p-xl-5">
        @if($errors->any())
            <div class="alert alert-danger mb-4" role="alert"><strong>Dokumen belum disimpan.</strong><ul class="mb-0 mt-2">@foreach(array_unique($errors->all()) as $message)<li>{{ $message }}</li>@endforeach</ul></div>
        @endif

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="workspace-document-intake-panel h-100">
                    <div class="workspace-document-intake-heading"><div><span class="workspace-form-kicker">Lampiran utama</span><label for="employee-document-file" class="form-label mb-1">File dokumen <span class="text-danger">*</span></label><p class="form-text mb-0">PDF, JPG, atau PNG dengan ukuran maksimal 20 MB.</p></div><span class="workspace-document-intake-required">Wajib</span></div>
                    <div class="workspace-document-upload-pair mt-3">
                        <div class="workspace-document-upload-control"><input id="employee-document-file" type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept="application/pdf,image/jpeg,image/png" data-document-upload-input required>@error('file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                        <div class="workspace-document-upload-preview" data-document-upload-preview aria-live="polite"><div class="workspace-document-preview-empty"><span class="workspace-document-preview-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6zM14 3v5h5M9 13h6M9 17h4"/></svg></span><span>Pilih file untuk melihat preview</span></div></div>
                    </div>
                    <div class="form-text mt-3">File akan disimpan ke area private HRIS dan tidak masuk ke daftar Document Management umum.</div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card h-100 border bg-light-subtle">
                    <div class="card-body p-4"><span class="workspace-form-kicker">Identitas dokumen</span><h2 class="h5 mt-1 mb-3">Kode otomatis & metadata</h2>
                        <div class="p-3 rounded-3 bg-white border mb-3"><div class="small text-secondary mb-1">Nomor dokumen</div><div class="fw-bold text-primary">{{ old('document_number', $documentNumber) }}</div><div class="small text-secondary mt-2">Nomor ini dibuat otomatis dan akan digunakan saat dokumen disimpan.</div></div>
                        <div class="small text-secondary d-flex gap-2 align-items-start"><span class="text-primary">✓</span><span>Nomor ini digunakan untuk pencarian, referensi internal, dan nama object file.</span></div>
                    </div>
                </div>
            </div>

            <div class="col-12"><hr class="my-1"><span class="workspace-form-kicker">Informasi dokumen</span></div>
            <div class="col-12"><label for="employee-document-title" class="form-label">Judul dokumen <span class="text-danger">*</span></label><input id="employee-document-title" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Contoh: PKWT Ahmad Pudoli 2026" required>@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-lg-6"><label class="form-label">Karyawan <span class="text-danger">*</span></label><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-single-select="true" data-dropdown-empty-label="Pilih karyawan"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>Pilih karyawan</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><input type="search" class="form-control form-control-sm mb-2" placeholder="Cari nama atau NIP..." data-dropdown-search><div class="workspace-dropdown-options">@foreach($employees as $employee)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="employee_id" value="{{ $employee->id }}" data-dropdown-item data-dropdown-value="{{ $employee->full_name }} · {{ $employee->employee_number }}" @checked(old('employee_id') === $employee->id)><span><strong>{{ $employee->full_name }}</strong><small>{{ $employee->employee_number }}{{ $employee->company ? ' · '.$employee->company->name : '' }}</small></span></label>@endforeach</div></div></div>@error('employee_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
            <div class="col-lg-6"><label for="employee-document-type" class="form-label">Jenis dokumen <span class="text-danger">*</span></label><select id="employee-document-type" name="type" class="form-select @error('type') is-invalid @enderror" required><option value="">Pilih jenis dokumen</option>@foreach(['employment_contract'=>'Kontrak kerja','contract_addendum'=>'Addendum kontrak','appointment_letter'=>'Surat pengangkatan','identity_document'=>'Dokumen identitas','other'=>'Dokumen lainnya'] as $key => $label)<option value="{{ $key }}" @selected(old('type') === $key)>{{ $label }}</option>@endforeach</select>@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-lg-6"><label for="employee-document-effective" class="form-label">Berlaku mulai</label><input id="employee-document-effective" type="date" name="effective_from" value="{{ old('effective_from') }}" class="form-control @error('effective_from') is-invalid @enderror">@error('effective_from')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-lg-6"><label for="employee-document-expires" class="form-label">Berakhir pada</label><input id="employee-document-expires" type="date" name="expires_at" value="{{ old('expires_at') }}" class="form-control @error('expires_at') is-invalid @enderror"><div class="form-text">Kosongkan jika dokumen tidak memiliki masa kedaluwarsa.</div>@error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        </div>
    </div>
    <div class="card-footer bg-white border-0 p-4 p-xl-5 pt-0 d-flex flex-wrap justify-content-between align-items-center gap-3"><div class="small text-secondary"><span class="text-danger">*</span> Wajib diisi · Akses mengikuti scope perusahaan HRIS.</div><div class="d-flex gap-2"><a href="{{ route('workspace.hris.employee-documents') }}" class="btn btn-outline-secondary">Batal</a><button class="btn btn-primary px-4" type="submit">Upload & simpan</button></div></div>
</form>
@endsection
