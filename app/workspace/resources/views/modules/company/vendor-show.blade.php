@extends('layouts.app', ['title' => $vendor->name])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <a href="{{ route('workspace.company.vendors') }}" class="text-decoration-none">&larr; Vendor</a>
        <div class="d-flex align-items-center gap-3 mt-3">
            <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width:64px;height:64px;font-size:1.25rem">{{ strtoupper(substr($vendor->code ?: $vendor->name, 0, 2)) }}</div>
            <div><h1 class="h3 mb-1">{{ $vendor->name }}</h1><p class="text-secondary mb-0 font-monospace">{{ $vendor->code ?: 'Tanpa kode vendor' }}</p></div>
        </div>
    </div>
    <div class="d-flex gap-2"><a href="{{ route('workspace.company.vendors.edit', $vendor) }}" class="btn btn-primary">Edit vendor</a><form method="POST" action="{{ route('workspace.company.vendors.destroy', $vendor) }}" data-confirm="Vendor ini akan dipindahkan ke arsip. Lanjutkan?" data-loading-message="Mengarsipkan Data">@csrf @method('DELETE')<button class="btn btn-outline-danger" type="submit">Arsipkan</button></form></div>
</div>

<div class="row g-3">
    <div class="col-lg-7"><div class="card border-0 shadow-sm h-100"><div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Informasi vendor</h2><span class="badge rounded-pill {{ $vendor->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $vendor->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span></div>
        <dl class="row mb-0"><dt class="col-sm-4 text-secondary">Nama profil</dt><dd class="col-sm-8">{{ $vendor->name }}</dd><dt class="col-sm-4 text-secondary">Kode vendor</dt><dd class="col-sm-8 font-monospace">{{ $vendor->code ?: '-' }}</dd><dt class="col-sm-4 text-secondary">NPWP / Tax ID</dt><dd class="col-sm-8">{{ $vendor->tax_identifier ?: '-' }}</dd><dt class="col-sm-4 text-secondary">Dibuat</dt><dd class="col-sm-8">{{ optional($vendor->created_at)->format('d M Y, H:i') }}</dd><dt class="col-sm-4 text-secondary">Diperbarui</dt><dd class="col-sm-8">{{ optional($vendor->updated_at)->format('d M Y, H:i') }}</dd></dl>
    </div></div></div>
    <div class="col-lg-5"><div class="card border-0 shadow-sm h-100"><div class="card-body"><h2 class="h5 mb-3">Master perusahaan vendor</h2>
        @if($vendor->vendorCompany)<a href="{{ route('workspace.company.show', $vendor->vendorCompany) }}" class="fw-semibold text-decoration-none">{{ $vendor->vendorCompany->name }}</a><div class="font-monospace text-secondary mt-1">{{ $vendor->vendorCompany->code }}</div><p class="text-secondary small mt-3 mb-0">Satu master perusahaan dapat digunakan pada beberapa perusahaan operasional.</p>@else<p class="text-secondary mb-0">Belum terhubung ke master Company vendor.</p>@endif
    </div></div></div>
    <div class="col-12"><div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5 mb-1">Perusahaan operasional pengguna</h2><p class="text-secondary small mb-3">PT ABC dapat dipakai bersama oleh perusahaan berikut tanpa membuat data vendor duplikat.</p><div class="d-flex flex-wrap gap-2">
        @forelse($vendor->operationalCompanies as $company)<a href="{{ route('workspace.company.show', $company) }}" class="badge rounded-pill text-bg-light border text-decoration-none font-monospace">{{ $company->code }}</a>@empty<span class="text-secondary">Belum ada perusahaan operasional yang dipilih.</span>@endforelse
    </div></div></div></div>
</div>
@endsection
