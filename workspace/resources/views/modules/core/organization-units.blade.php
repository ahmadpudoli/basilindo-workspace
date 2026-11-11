@extends('layouts.app', ['title' => 'Struktur Organisasi'])
@section('content')
@php
    $sortUrl = function (string $column) use ($sort, $direction): string {
        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $sort === $column && $direction === 'asc' ? 'desc' : 'asc', 'page' => null]);
    };
    $sortMark = fn (string $column): string => $sort === $column ? ($direction === 'asc' ? ' ↑' : ' ↓') : '';
    $typeLabels = ['directorate' => 'Direktorat', 'division' => 'Divisi', 'department' => 'Departemen', 'team' => 'Tim', 'unit' => 'Unit'];
@endphp
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><div class="text-uppercase text-secondary small fw-semibold mb-2">Core · Master data</div><h1 class="h3 mb-1">Struktur organisasi</h1><p class="text-secondary mb-0">Kelola unit organisasi perusahaan operasional internal.</p></div>
    @can('create', \Core\Models\OrganizationUnit::class)<a href="{{ route('workspace.core.organization-units.create') }}" class="btn btn-primary">Tambah unit</a>@endcan
</div>
<form method="GET" class="card card-body border-0 shadow-sm mb-3 workspace-list-filter-form">
    <div class="row g-2 align-items-end">
        <div class="col-lg-3"><label class="form-label">Pencarian</label><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Nama atau kode unit"></div>
        <div class="col-lg-3"><label class="form-label">Perusahaan</label><select name="company_id" class="form-select"><option value="">Semua perusahaan</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected($companyId === (int) $company->id)>{{ $company->name }}</option>@endforeach</select></div>
        <div class="col-lg-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">Semua status</option><option value="active" @selected($status === 'active')>Aktif</option><option value="inactive" @selected($status === 'inactive')>Nonaktif</option></select></div>
        <div class="col-lg-2"><label class="form-label">Tampilan</label><select name="limit" class="form-select">@foreach([10,25,50,100] as $limit)<option value="{{ $limit }}" @selected($perPage === $limit)>{{ $limit }} baris</option>@endforeach</select></div>
        <div class="col-lg-2"><div class="workspace-filter-actions"><a href="{{ route('workspace.core.organization-units') }}" class="btn btn-sm btn-outline-secondary">Reset</a><button class="btn btn-sm btn-primary">Terapkan</button></div></div>
    </div>
</form>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr>
    <th><a class="text-decoration-none text-reset" href="{{ $sortUrl('code') }}">Kode{{ $sortMark('code') }}</a></th>
    <th><a class="text-decoration-none text-reset" href="{{ $sortUrl('name') }}">Unit{{ $sortMark('name') }}</a></th>
    <th><a class="text-decoration-none text-reset" href="{{ $sortUrl('company_id') }}">Perusahaan{{ $sortMark('company_id') }}</a></th>
    <th>Induk</th><th><a class="text-decoration-none text-reset" href="{{ $sortUrl('type') }}">Tipe{{ $sortMark('type') }}</a></th>
    <th><a class="text-decoration-none text-reset" href="{{ $sortUrl('status') }}">Status{{ $sortMark('status') }}</a></th><th class="text-end">Aksi</th>
</tr></thead><tbody>@forelse($units as $unit)<tr>
    <td><code>{{ $unit->code }}</code></td><td><div class="fw-semibold">{{ $unit->name }}</div>@if($unit->children_count)<small class="text-secondary">{{ $unit->children_count }} unit di bawahnya</small>@endif</td>
    <td>{{ $unit->company?->name ?? '-' }}</td><td>{{ $unit->parent?->name ?? 'Akar organisasi' }}</td><td>{{ $typeLabels[$unit->type] ?? ucfirst($unit->type) }}</td>
    <td><span class="badge {{ $unit->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $unit->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span></td>
    <td class="text-end text-nowrap">@can('update', $unit)<a href="{{ route('workspace.core.organization-units.edit', $unit) }}" class="btn btn-sm btn-outline-secondary">Ubah</a>@endcan @can('delete', $unit)<form method="POST" action="{{ route('workspace.core.organization-units.destroy', $unit) }}" class="d-inline" data-confirm="Unit akan diarsipkan atau dinonaktifkan bila masih digunakan. Lanjutkan?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Arsipkan</button></form>@endcan</td>
</tr>@empty<tr><td colspan="7" class="text-center text-secondary py-5">Belum ada unit organisasi sesuai filter.</td></tr>@endforelse</tbody></table></div>
<div class="card-footer bg-white workspace-list-pagination"><div class="workspace-pagination-summary">Menampilkan <strong>{{ $units->firstItem() ?? 0 }}</strong>–<strong>{{ $units->lastItem() ?? 0 }}</strong> dari <strong>{{ $units->total() }}</strong> unit</div>{{ $units->onEachSide(1)->links() }}</div></div>
@endsection
