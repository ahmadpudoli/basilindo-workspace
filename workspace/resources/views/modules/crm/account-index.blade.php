@extends('layouts.app', ['title' => $label])

@section('content')
@php
    $selectedAccountTypes = (array) request('account_type', []);
    $selectedOwnerCompanyIds = array_map('strval', (array) request('owner_company_id', []));
    $selectedPartyCompanyIds = array_map('strval', (array) request('party_company_id', []));
@endphp
<style>
    .crm-account-filter-dropdown .workspace-dropdown-menu { min-width: 240px; }
    .crm-account-filter-dropdown .workspace-dropdown-options { max-height: 220px; }
</style>
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">{{ $label }}</h1>
        <p class="text-secondary mb-0">Kelola account perusahaan dan perorangan CRM.</p>
    </div>
    <a href="{{ route('workspace.crm.create', $resource) }}" class="btn btn-primary">Tambah {{ $label }}</a>
</div>

<form method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-lg-4">
                <label for="account-search" class="form-label">Pencarian</label>
                <input id="account-search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama account...">
            </div>
            <div class="col-6 col-lg-2">
                <label class="form-label">Tipe</label>
                <div class="workspace-dropdown-multiselect dropdown crm-account-filter-dropdown" data-searchable-dropdown data-dropdown-empty-label="Semua tipe">
                    <button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Pilih tipe account">
                        <span data-dropdown-label>Semua tipe</span>
                    </button>
                    <div class="dropdown-menu workspace-dropdown-menu p-2">
                        <div class="workspace-dropdown-options">
                            <label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="account_type[]" value="company" data-dropdown-item data-dropdown-value="Perusahaan" @checked(in_array('company', $selectedAccountTypes, true))><span>Perusahaan</span></label>
                            <label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="account_type[]" value="person" data-dropdown-item data-dropdown-value="Perorangan" @checked(in_array('person', $selectedAccountTypes, true))><span>Perorangan</span></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label class="form-label">Perusahaan pemilik</label>
                <div class="workspace-dropdown-multiselect dropdown crm-account-filter-dropdown" data-searchable-dropdown data-dropdown-empty-label="Semua pemilik">
                    <button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Pilih perusahaan pemilik">
                        <span data-dropdown-label>Semua pemilik</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end workspace-dropdown-menu p-2">
                        <input type="search" class="form-control form-control-sm mb-2" placeholder="Cari perusahaan..." data-dropdown-search>
                        <div class="workspace-dropdown-options">
                            @foreach($ownerCompanies as $company)
                                <label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="owner_company_id[]" value="{{ $company->id }}" data-dropdown-item data-dropdown-value="{{ $company->name }}" @checked(in_array((string) $company->id, $selectedOwnerCompanyIds, true))><span>{{ $company->name }}</span></label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label class="form-label">Client / partner</label>
                <div class="workspace-dropdown-multiselect dropdown crm-account-filter-dropdown" data-searchable-dropdown data-dropdown-empty-label="Semua client / partner">
                    <button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Pilih client atau partner">
                        <span data-dropdown-label>Semua client / partner</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end workspace-dropdown-menu p-2">
                        <input type="search" class="form-control form-control-sm mb-2" placeholder="Cari client / partner..." data-dropdown-search>
                        <div class="workspace-dropdown-options">
                            @foreach($partyCompanies as $company)
                                <label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="party_company_id[]" value="{{ $company->id }}" data-dropdown-item data-dropdown-value="{{ $company->name }}" @checked(in_array((string) $company->id, $selectedPartyCompanyIds, true))><span>{{ $company->name }}</span></label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
            <small class="text-secondary">{{ $records->total() }} account ditemukan.</small>
            <div class="d-flex align-items-center gap-2">
                <label for="per-page" class="small text-secondary mb-0">Baris per halaman</label>
                <select id="per-page" name="per_page" class="form-select form-select-sm" style="width: 90px">
                    @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary">Terapkan</button>
                <a href="{{ route('workspace.crm.resource', $resource) }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0 crm-account-table">
            <thead>
            <tr>
                @foreach(['name' => 'Account', 'account_type' => 'Tipe', 'party_company' => 'Client / partner', 'company' => 'Perusahaan pemilik'] as $column => $heading)
                    <th>
                        @if(in_array($column, ['name', 'account_type'], true))
                            @php($nextDirection = $sort === $column && $direction === 'asc' ? 'desc' : 'asc')
                            <a class="text-decoration-none text-dark" href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection, 'page' => null]) }}">
                                {{ $heading }} <span class="text-primary">{{ $sort === $column ? ($direction === 'asc' ? '↑' : '↓') : '↕' }}</span>
                            </a>
                        @else
                            {{ $heading }}
                        @endif
                    </th>
                @endforeach
                <th>Kontak</th><th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($records as $record)
                @php($contactNames = $record->contacts->pluck('name')->filter()->values())
                <tr>
                    <td><div class="fw-semibold">{{ $record->name }}</div><small class="text-secondary">{{ $record->getKey() }}</small></td>
                    <td>{{ $record->account_type === 'company' ? 'Perusahaan' : ($record->account_type === 'person' ? 'Perorangan' : 'Belum ditentukan') }}</td>
                    <td>{{ $record->partyCompany?->name ?: '—' }}</td>
                    <td>{{ $record->company?->name ?: '—' }}</td>
                    <td>
                        @if($contactNames->isEmpty())
                            <span class="text-secondary">—</span>
                        @else
                            {{ $contactNames->take(3)->implode(', ') }}@if($contactNames->count() > 3) <span class="text-secondary">(+{{ $contactNames->count() - 3 }})</span>@endif
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('workspace.crm.account.show', $record) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                        <a href="{{ route('workspace.crm.edit', [$resource, $record->getKey()]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        @if(! $record->is_integrated && blank($record->source_system))
                            <form method="POST" action="{{ route('workspace.crm.destroy', [$resource, $record->getKey()]) }}" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        @else
                            <span class="badge rounded-pill text-bg-info">Odoo · sumber terkunci</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-5">Belum ada data.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
        <small class="text-secondary">Menampilkan {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} dari {{ $records->total() }} account.</small>
        {{ $records->onEachSide(1)->links() }}
    </div>
</div>
@endsection
