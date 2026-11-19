@extends('layouts.app', ['title' => 'Karyawan'])
@section('content')
@php($sortUrl = fn (string $column) => request()->fullUrlWithQuery(['sort' => $column, 'direction' => $sort === $column && $direction === 'asc' ? 'desc' : 'asc', 'page' => null]))
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><div class="text-uppercase text-secondary small fw-semibold mb-2">Master HRIS</div><h1 class="h3 mb-1">Karyawan</h1><p class="text-secondary mb-0">Daftar karyawan sesuai scope perusahaan Anda.</p></div><a href="{{ route('workspace.hris.employees.create') }}" class="workspace-primary-action"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 20v-1.5A4.5 4.5 0 0 1 8 14h2a4.5 4.5 0 0 1 4.5 4.5V20M18.5 8v6M15.5 11h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Tambah Karyawan</span></a></div>
<form class="card card-body border-0 shadow-sm mb-3 workspace-list-filter-form" method="GET"><div class="row g-2 align-items-end"><div class="col-md-4"><label class="form-label">Cari nama/nomor</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Nama atau nomor karyawan"></div><div class="col-md-3"><label class="form-label">Perusahaan operasional</label><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-empty-label="Semua perusahaan"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>{{ count($companyIds) ? count($companyIds).' perusahaan dipilih' : 'Semua perusahaan' }}</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><input type="search" class="form-control form-control-sm mb-2" placeholder="Cari perusahaan..." data-dropdown-search><div class="workspace-dropdown-options">@foreach($companies as $company)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="company_ids[]" value="{{ $company->id }}" data-dropdown-item data-dropdown-value="{{ $company->name }}" @checked(in_array((int) $company->id, $companyIds, true))><span>{{ $company->name }}</span></label>@endforeach</div></div></div></div><div class="col-md-2"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">Semua status</option>@foreach(['active'=>'Aktif','inactive'=>'Nonaktif','archived'=>'Diarsipkan'] as $key=>$label)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach</select></div><div class="col-md-1"><label class="form-label">Baris</label><select class="form-select" name="limit">@foreach([10,25,50,100] as $limit)<option value="{{ $limit }}" @selected($perPage === $limit)>{{ $limit }}</option>@endforeach</select></div><div class="col-md-2"><div class="workspace-filter-actions"><a href="{{ route('workspace.hris.employees') }}" class="btn btn-sm btn-outline-secondary">Reset</a><button class="btn btn-sm btn-primary" type="submit">Terapkan</button></div></div></div></form>
<div class="card border-0 shadow-sm workspace-table-card"><div class="table-responsive"><table class="table align-middle mb-0 workspace-data-table workspace-data-table--employees"><colgroup><col style="width:21%"><col style="width:14%"><col style="width:20%"><col style="width:17%"><col style="width:12%"><col style="width:16%"></colgroup><thead><tr><th><a class="workspace-sort-link {{ $sort === 'full_name' ? 'active' : '' }}" href="{{ $sortUrl('full_name') }}">Nama <span aria-hidden="true">{{ $sort === 'full_name' ? ($direction === 'asc' ? '↑' : '↓') : '↕' }}</span></a></th><th><a class="workspace-sort-link {{ $sort === 'employee_number' ? 'active' : '' }}" href="{{ $sortUrl('employee_number') }}">Nomor <span aria-hidden="true">{{ $sort === 'employee_number' ? ($direction === 'asc' ? '↑' : '↓') : '↕' }}</span></a></th><th>Perusahaan</th><th>Atasan</th><th><a class="workspace-sort-link {{ $sort === 'status' ? 'active' : '' }}" href="{{ $sortUrl('status') }}">Status <span aria-hidden="true">{{ $sort === 'status' ? ($direction === 'asc' ? '↑' : '↓') : '↕' }}</span></a></th><th class="text-end">Aksi</th></tr></thead><tbody>@forelse($employees as $employee)<tr><td class="fw-semibold">{{ $employee->full_name }}<small class="d-block text-secondary">{{ $employee->email ?? 'Tanpa email' }}</small></td><td>{{ $employee->employee_number }}</td><td>{{ $employee->company?->name ?? '—' }}</td><td>{{ $employee->manager?->full_name ?? '—' }}</td><td><span class="badge text-bg-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($employee->status) }}</span></td>@include('modules.partials.hris-employee-row-actions', ['employee' => $employee])</tr>@empty<tr><td colspan="6" class="text-center text-secondary py-5">Belum ada karyawan dalam scope Anda.</td></tr>@endforelse</tbody></table></div><div class="card-footer bg-white workspace-list-pagination"><div class="workspace-pagination-summary">Menampilkan <strong>{{ $employees->firstItem() ?? 0 }}</strong>–<strong>{{ $employees->lastItem() ?? 0 }}</strong> dari <strong>{{ $employees->total() }}</strong> karyawan</div>{{ $employees->onEachSide(1)->links() }}</div></div>
@endsection

@push('scripts')
<script>
(() => {
    let activeAction = null;
    const closeAction = (restoreFocus = false) => {
        if (!activeAction) return;
        activeAction.menu.classList.remove('show');
        activeAction.toggle.setAttribute('aria-expanded', 'false');
        if (restoreFocus) activeAction.toggle.focus();
        activeAction = null;
    };
    const placeAction = () => {
        if (!activeAction) return;
        const { menu, toggle } = activeAction;
        const anchor = toggle.getBoundingClientRect();
        menu.style.visibility = 'hidden';
        menu.style.left = '0px';
        menu.style.top = '0px';
        const box = menu.getBoundingClientRect();
        const left = Math.max(8, Math.min(window.innerWidth - box.width - 8, anchor.right - box.width));
        const below = anchor.bottom + 8;
        const top = below + box.height <= window.innerHeight - 8 ? below : Math.max(8, anchor.top - box.height - 8);
        menu.style.left = `${left}px`;
        menu.style.top = `${top}px`;
        menu.style.visibility = 'visible';
    };
    const initializeActions = () => {
        document.addEventListener('click', (event) => {
            const toggle = event.target.closest('[data-workspace-table-action-toggle]');
            if (toggle) {
                event.preventDefault();
                if (activeAction?.toggle === toggle) { closeAction(); return; }
                closeAction();
                const menu = document.getElementById(toggle.dataset.menuId);
                if (!menu) return;
                document.body.append(menu);
                menu.classList.add('show');
                toggle.setAttribute('aria-expanded', 'true');
                activeAction = { toggle, menu };
                placeAction();
                return;
            }
            if (activeAction?.menu.contains(event.target)) {
                if (event.target.closest('a, button[type="submit"]')) closeAction();
                return;
            }
            closeAction();
        });
        document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeAction(true); });
        window.addEventListener('resize', placeAction);
        window.addEventListener('scroll', placeAction, true);
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeActions, { once: true });
    else initializeActions();
})();
</script>
@endpush
