@extends('layouts.app', ['title' => 'Cuti dan Sakit'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><div class="text-uppercase text-secondary small fw-semibold mb-2">Kehadiran</div><h1 class="h3 mb-1">Cuti & sakit</h1><p class="text-secondary mb-0">{{ $canViewAll ? 'Daftar pengajuan dalam scope perusahaan Anda.' : 'Riwayat pengajuan cuti dan sakit Anda.' }}</p></div>
    @if($canCreate)<a href="{{ route('workspace.hris.leave-requests.create') }}" class="btn btn-primary">Ajukan cuti/sakit</a>@endif
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-secondary small">Kuota cuti {{ $metricYear }}</div><div class="h3 mb-0">{{ $quota }} hari</div></div></div></div>
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-secondary small">Cuti terpakai</div><div class="h3 mb-0">{{ $usedLeave }} hari</div></div></div></div>
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-secondary small">Sisa cuti · Sakit tercatat</div><div class="h3 mb-0">{{ max($quota - $usedLeave, 0) }} · {{ $sickDays }} hari</div></div></div></div>
</div>

<form method="GET" class="card card-body border-0 shadow-sm mb-3 workspace-list-filter-form">
    <div class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label">Jenis</label><select name="type" class="form-select"><option value="">Semua jenis</option><option value="leave" @selected($type === 'leave')>Cuti</option><option value="sick" @selected($type === 'sick')>Sakit</option></select></div>
        <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">Semua status</option>@foreach(['pending_manager'=>'Menunggu atasan','pending_hr'=>'Menunggu HR','needs_revision'=>'Revisi','approved'=>'Disetujui','rejected'=>'Ditolak'] as $key => $label)<option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Tahun</label><select name="year" class="form-select"><option value="all" @selected($year === null)>Semua tahun</option>@foreach(range(now()->year - 2, now()->year + 1) as $optionYear)<option value="{{ $optionYear }}" @selected($year === $optionYear)>{{ $optionYear }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Baris</label><select name="limit" class="form-select">@foreach([10,25,50,100] as $limit)<option value="{{ $limit }}" @selected($perPage === $limit)>{{ $limit }}</option>@endforeach</select></div>
        <div class="col-md-2"><div class="workspace-filter-actions"><a href="{{ route('workspace.hris.leave-requests') }}" class="btn btn-sm btn-outline-secondary">Reset</a><button class="btn btn-sm btn-primary" type="submit">Terapkan</button></div></div>
    </div>
</form>

<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Karyawan</th><th>Jenis</th><th><a class="workspace-sort-link {{ $sort === 'start_date' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'start_date', 'direction' => $sort === 'start_date' && $direction === 'asc' ? 'desc' : 'asc']) }}">Tanggal pengajuan</a></th><th>Hari</th><th><a class="workspace-sort-link {{ $sort === 'status' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => $sort === 'status' && $direction === 'asc' ? 'desc' : 'asc']) }}">Status</a></th><th class="text-end">Aksi</th></tr></thead><tbody>@forelse($requests as $item)<tr><td class="fw-semibold">{{ $item->employee?->full_name ?? '-' }}<small class="d-block text-secondary">{{ $item->employee?->employee_number ?? '-' }}</small></td><td>{{ $item->type === 'leave' ? 'Cuti' : 'Sakit' }}</td><td>{{ $item->date_list }}</td><td>{{ $item->total_days }}</td><td><span class="badge text-bg-{{ in_array($item->status, ['approved'], true) ? 'success' : (in_array($item->status, ['rejected'], true) ? 'danger' : 'warning') }}">{{ $item->status_label }}</span></td><td class="workspace-table-actions text-end"><a href="{{ route('workspace.hris.leave-requests.show', $item) }}" class="btn btn-sm btn-outline-secondary">Detail</a>@if($item->status === 'needs_revision') <a href="{{ route('workspace.hris.leave-requests.edit', $item) }}" class="btn btn-sm btn-outline-primary">Revisi</a>@endif</td></tr>@empty<tr><td colspan="6" class="text-center text-secondary py-5">Belum ada pengajuan.</td></tr>@endforelse</tbody></table></div><div class="card-footer bg-white workspace-list-pagination"><div class="workspace-pagination-summary">Menampilkan <strong>{{ $requests->firstItem() ?? 0 }}</strong>–<strong>{{ $requests->lastItem() ?? 0 }}</strong> dari <strong>{{ $requests->total() }}</strong> pengajuan</div>{{ $requests->onEachSide(1)->links() }}</div></div>
@endsection
