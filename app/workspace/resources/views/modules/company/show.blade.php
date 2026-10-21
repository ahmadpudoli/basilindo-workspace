@extends('layouts.app', ['title' => $company->name])

@section('content')
@php
    $roleLabels = ['operating_company' => 'Perusahaan operasional', 'client' => 'Client', 'vendor' => 'Vendor', 'partner' => 'Partner', 'group' => 'Group'];
    $isGlobal = $company->operationalVisibility->isEmpty();
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <a href="{{ route('workspace.company.companies') }}" class="text-decoration-none">&larr; Perusahaan</a>
        <div class="d-flex align-items-center gap-3 mt-3">
            @if($company->logo_object_key)
                <img src="{{ route('workspace.company.logo', $company) }}" alt="Logo {{ $company->name }}" class="rounded-3 border bg-white p-1" style="width:72px;height:72px;object-fit:contain">
            @else
                <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width:72px;height:72px;font-size:1.5rem">{{ strtoupper(substr($company->code, 0, 2)) }}</div>
            @endif
            <div><h1 class="h3 mb-1">{{ $company->name }}</h1><p class="text-secondary mb-0 font-monospace">{{ $company->code }}</p></div>
        </div>
    </div>
    <a href="{{ route('workspace.company.edit', $company) }}" class="btn btn-primary">Edit perusahaan</a>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3"><div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Informasi perusahaan</h2><span class="badge rounded-pill {{ $company->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $company->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span></div>
            <dl class="row mb-0">
                <dt class="col-sm-4 text-secondary">Jenis entitas</dt><dd class="col-sm-8"><span class="badge rounded-pill {{ $company->entity_type === 'group' ? 'text-bg-primary' : 'text-bg-info' }}">{{ ucfirst($company->entity_type) }}</span></dd>
                <dt class="col-sm-4 text-secondary">Klasifikasi</dt><dd class="col-sm-8">{{ ucfirst($company->classification) }}</dd>
                <dt class="col-sm-4 text-secondary">Peran bisnis</dt><dd class="col-sm-8">{{ collect($company->roles ?? [])->map(fn ($role) => $roleLabels[$role] ?? $role)->join(', ') ?: 'Belum ditentukan' }}</dd>
                <dt class="col-sm-4 text-secondary">Parent perusahaan</dt><dd class="col-sm-8">@if($company->parent){{ $company->parent->name }} <span class="font-monospace text-secondary">({{ $company->parent->code }})</span>@else Tidak memiliki parent @endif</dd>
                <dt class="col-sm-4 text-secondary">Dibuat</dt><dd class="col-sm-8">{{ optional($company->created_at)->format('d M Y, H:i') }}</dd>
                <dt class="col-sm-4 text-secondary">Diperbarui</dt><dd class="col-sm-8">{{ optional($company->updated_at)->format('d M Y, H:i') }}</dd>
            </dl>
        </div></div>
        <div class="card border-0 shadow-sm"><div class="card-body">
            <h2 class="h5 mb-3">Visibilitas data</h2>
            @if($isGlobal)<div class="alert alert-primary mb-0">Data perusahaan ini bersifat global dan dapat digunakan oleh semua perusahaan operasional yang diizinkan untuk user.</div>@else<p class="text-secondary small">Data perusahaan ini hanya terlihat pada perusahaan operasional berikut:</p><div class="d-flex flex-wrap gap-2">@foreach($company->operationalVisibility as $operationalCompany)<span class="badge rounded-pill text-bg-light border font-monospace">{{ $operationalCompany->code }}</span>@endforeach</div>@endif
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3"><div class="card-body"><h2 class="h5 mb-3">Struktur perusahaan</h2>@if($company->subsidiaries->isEmpty())<p class="text-secondary mb-0">Belum ada perusahaan turunan.</p>@else<ul class="list-group list-group-flush">@foreach($company->subsidiaries as $subsidiary)<li class="list-group-item px-0 d-flex justify-content-between"><span>{{ $subsidiary->name }}</span><span class="font-monospace text-secondary">{{ $subsidiary->code }}</span></li>@endforeach</ul>@endif</div></div>
        <div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5 mb-3">Brand perusahaan</h2>@if($company->logo_object_key)<img src="{{ route('workspace.company.logo', $company) }}" alt="Logo {{ $company->name }}" class="img-fluid rounded-3 border p-3 bg-white" style="max-height:180px;width:100%;object-fit:contain">@else<div class="text-center text-secondary py-4">Belum ada logo atau brand.</div>@endif</div></div>
    </div>
</div>
@endsection
