@extends('layouts.app', ['title' => 'Mapping Perusahaan Odoo'])

@section('content')
@php($mappedCount = $companies->whereNotNull('local_id')->count())
@php($pendingCount = $companies->count() - $mappedCount)
<div class="integration-mapping-page">
<div class="integration-mapping-header">
    <div><div class="workspace-eyebrow">INTEGRASI ODOO</div><h1>Mapping Perusahaan</h1><p>Hubungkan perusahaan Odoo dengan perusahaan Workspace sebelum project dipakai HRIS.</p></div>
    <a href="{{ route('workspace.core.integration') }}" class="btn btn-outline-secondary">Kembali</a>
</div>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<div class="integration-mapping-summary">
    <div class="integration-mapping-summary-card"><span class="integration-mapping-summary-icon is-blue">↔</span><span><small>Total perusahaan</small><strong>{{ $companies->count() }}</strong></span></div>
    <div class="integration-mapping-summary-card"><span class="integration-mapping-summary-icon is-green">✓</span><span><small>Sudah mapped</small><strong>{{ $mappedCount }}</strong></span></div>
    <div class="integration-mapping-summary-card"><span class="integration-mapping-summary-icon is-orange">!</span><span><small>Menunggu mapping</small><strong>{{ $pendingCount }}</strong></span></div>
</div>
<div class="workspace-card integration-mapping-card">
    <div class="workspace-card-header integration-mapping-card-header"><div><h2>Perusahaan Odoo</h2><p>Pastikan setiap perusahaan memiliki pasangan Workspace sebelum project dinormalisasi ke HRIS.</p></div><span class="integration-mapping-source">Sumber: Odoo</span></div>
    <div class="table-responsive"><table class="table integration-mapping-table align-middle mb-0"><thead><tr><th>Perusahaan Odoo</th><th>Status</th><th>Perusahaan Workspace</th></tr></thead><tbody>
    @forelse($companies as $company)
    @if($company->candidate_id)<tr><td colspan="3"><div class="integration-mapping-candidate-note">Kandidat nama ditemukan: <strong>{{ $company->candidate_code }}</strong>. Silakan pastikan kecocokan sebelum menyimpan mapping.</div></td></tr>@endif
    <tr><td><div class="integration-company-name"><span class="integration-company-avatar">{{ mb_strtoupper(mb_substr($company->display_name, 0, 1)) }}</span><span><strong>{{ $company->display_name }}</strong><small>External ID: {{ $company->external_id }}</small></span></div></td><td>@if($company->local_id)<span class="integration-status is-mapped"><span>✓</span> Mapped</span><small class="integration-status-code">{{ $company->local_code }}</small>@else<span class="integration-status is-pending"><span>!</span> Belum mapped</span><small class="integration-status-code">Perlu tindakan</small>@endif</td><td><form method="POST" action="{{ route('workspace.core.integration.company-mapping.store') }}" class="integration-mapping-form">@csrf<input type="hidden" name="external_id" value="{{ $company->external_id }}"><select class="form-select" name="company_id" required aria-label="Perusahaan Workspace untuk {{ $company->display_name }}"><option value="">Pilih perusahaan Workspace</option>@foreach($workspaceCompanies as $workspaceCompany)<option value="{{ $workspaceCompany->id }}" @selected((string) $company->local_id === (string) $workspaceCompany->id)>{{ $workspaceCompany->code }} — {{ $workspaceCompany->name }}</option>@endforeach</select><button class="btn btn-primary">Simpan mapping</button></form></td></tr>
    @empty<tr><td colspan="3"><div class="integration-mapping-empty"><strong>Belum ada perusahaan Odoo</strong><span>Jalankan sinkronisasi terlebih dahulu untuk mengambil data perusahaan.</span></div></td></tr>@endforelse
    </tbody></table></div>
</div>
</div>
@endsection
