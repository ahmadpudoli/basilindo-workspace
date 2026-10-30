@extends('layouts.app', ['title' => 'Cuti dan Sakit'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><div class="text-uppercase text-secondary small fw-semibold mb-2">Kehadiran</div><h1 class="h3 mb-1">{{ $summaryView ? 'Ringkasan Cuti & Sakit Karyawan' : 'Cuti & sakit' }}</h1><p class="text-secondary mb-0">{{ $canViewAll ? 'Daftar pengajuan dalam scope perusahaan Anda.' : 'Riwayat pengajuan cuti dan sakit Anda.' }}</p></div>
    @if($canCreate && ! $summaryView)<a href="{{ route('workspace.hris.leave-requests.create') }}" class="btn btn-primary">Ajukan cuti/sakit</a>@endif
</div>

<section class="workspace-mobile-panel mb-3" data-mobile-panel>
<button type="button" class="workspace-mobile-panel-toggle" data-mobile-toggle aria-expanded="false">Ringkasan cuti & sakit <span data-mobile-toggle-icon>+</span></button>
<div class="workspace-leave-quota workspace-mobile-panel-content" data-quota-panel>
    <button type="button" class="workspace-leave-kpi" data-bs-toggle="modal" data-bs-target="#leave-kpi-modal" data-kpi="quota"><span>Kuota cuti {{ $metricYear }}</span><strong>{{ $quota }} hari</strong></button>
    <button type="button" class="workspace-leave-kpi" data-bs-toggle="modal" data-bs-target="#leave-kpi-modal" data-kpi="collective"><span>Cuti bersama</span><strong>{{ $collectiveDays }} hari</strong></button>
    <button type="button" class="workspace-leave-kpi" data-bs-toggle="modal" data-bs-target="#leave-kpi-modal" data-kpi="used"><span>Cuti terpakai</span><strong>{{ $usedLeave }} hari</strong></button>
    <button type="button" class="workspace-leave-kpi" data-bs-toggle="modal" data-bs-target="#leave-kpi-modal" data-kpi="remaining"><span>Sisa cuti</span><strong>{{ $remainingLeave }} hari</strong></button>
    <button type="button" class="workspace-leave-kpi" data-bs-toggle="modal" data-bs-target="#leave-kpi-modal" data-kpi="sick"><span>Sakit tercatat</span><strong>{{ $sickDays }} hari</strong></button>
</div>
</section>

<section class="workspace-mobile-panel mb-3" data-mobile-panel>
<button type="button" class="workspace-mobile-panel-toggle" data-mobile-toggle aria-expanded="false">Filter pengajuan <span data-mobile-toggle-icon>+</span></button>
<form method="GET" class="card card-body border-0 shadow-sm workspace-mobile-panel-content workspace-list-filter-form">
    <div class="row g-2 align-items-end">
        <div class="col-md-2"><label class="form-label">Jenis</label><select name="type" class="form-select"><option value="">Semua jenis</option><option value="leave" @selected($type === 'leave')>Cuti</option><option value="sick" @selected($type === 'sick')>Sakit</option></select></div>
        <div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">Semua status</option>@foreach(['pending_manager'=>'Menunggu atasan','pending_hr'=>'Menunggu HR','needs_revision'=>'Revisi','approved'=>'Disetujui','rejected'=>'Ditolak'] as $key => $label)<option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Tahun</label><select name="year" class="form-select" data-year-filter><option value="all" @selected($year === null)>Semua tahun</option>@foreach(range($activeYear - 2, $activeYear + 1) as $optionYear)<option value="{{ $optionYear }}" @selected($year === $optionYear)>{{ $optionYear }}{{ $optionYear === $activeYear ? ' (aktif)' : '' }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Bulan</label><select name="month" class="form-select" @disabled($year === null)><option value="">Semua bulan</option>@foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $monthNumber => $monthName)<option value="{{ $monthNumber }}" @selected($month === $monthNumber)>{{ $monthName }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Baris</label><select name="limit" class="form-select">@foreach([10,25,50,100] as $limit)<option value="{{ $limit }}" @selected($perPage === $limit)>{{ $limit }}</option>@endforeach</select></div>
        <div class="col-md-2"><div class="workspace-filter-actions"><a href="{{ route('workspace.hris.leave-requests') }}" class="btn btn-sm btn-outline-secondary">Reset</a><button class="btn btn-sm btn-primary" type="submit">Terapkan</button></div></div>
    </div>
</form>
</section>

<script>
    document.querySelectorAll('[data-mobile-toggle]').forEach((toggle) => toggle.addEventListener('click', () => {
        const panel = toggle.closest('[data-mobile-panel]');
        const expanded = panel.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        const icon = toggle.querySelector('[data-mobile-toggle-icon]');
        if (icon) icon.textContent = expanded ? '−' : '+';
    }));

    document.querySelector('[data-year-filter]')?.addEventListener('change', (event) => {
        const month = document.querySelector('select[name="month"]');
        if (!month) return;
        const allYears = event.target.value === 'all';
        month.disabled = allYears;
        if (allYears) month.value = '';
    });
</script>

<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Karyawan</th><th>Jenis</th><th><a class="workspace-sort-link {{ $sort === 'start_date' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'start_date', 'direction' => $sort === 'start_date' && $direction === 'asc' ? 'desc' : 'asc']) }}">Tanggal pengajuan</a></th><th>Hari</th><th><a class="workspace-sort-link {{ $sort === 'status' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => $sort === 'status' && $direction === 'asc' ? 'desc' : 'asc']) }}">Status</a></th><th class="text-end">Aksi</th></tr></thead><tbody>@forelse($requests as $item)<tr><td class="fw-semibold">{{ $item->employee?->full_name ?? '-' }}<small class="d-block text-secondary">{{ $item->employee?->employee_number ?? '-' }}</small></td><td>{{ $item->type === 'leave' ? 'Cuti' : 'Sakit' }}</td><td>{{ $item->date_list }}</td><td>{{ $item->total_days }}</td><td><span class="badge text-bg-{{ in_array($item->status, ['approved'], true) ? 'success' : (in_array($item->status, ['rejected'], true) ? 'danger' : 'warning') }}">{{ $item->status_label }}</span></td><td class="workspace-table-actions text-end">@if(filled($item->supporting_document_id)) @php($attachmentExtension = strtolower(pathinfo((string) $item->supporting_document_id, PATHINFO_EXTENSION))) @php($attachmentPreviewType = match ($attachmentExtension) { 'pdf' => 'application/pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp' => 'image/'.$attachmentExtension, default => 'application/octet-stream' })<a href="{{ route('workspace.hris.leave-requests.supporting-document', ['leaveRequest' => $item, 'preview' => 1]) }}" class="btn btn-sm btn-outline-primary" data-workspace-preview data-preview-url="{{ route('workspace.hris.leave-requests.supporting-document', ['leaveRequest' => $item, 'preview' => 1]) }}" data-preview-fallback-url="{{ route('workspace.hris.leave-requests.supporting-document', $item) }}" data-preview-type="{{ $attachmentPreviewType }}" data-preview-title="Dokumen pendukung {{ $item->request_number }}">Lihat dokumen</a> @endif<a href="{{ route('workspace.hris.leave-requests.show', ['leaveRequest' => $item, 'from' => 'leave-requests']) }}" class="btn btn-sm btn-outline-secondary">Detail</a>@if($item->status === 'needs_revision') <a href="{{ route('workspace.hris.leave-requests.edit', $item) }}" class="btn btn-sm btn-outline-primary">Revisi</a>@endif</td></tr>@empty<tr><td colspan="6" class="text-center text-secondary py-5">Belum ada pengajuan.</td></tr>@endforelse</tbody></table></div><div class="card-footer bg-white workspace-list-pagination"><div class="workspace-pagination-summary">Menampilkan <strong>{{ $requests->firstItem() ?? 0 }}</strong>–<strong>{{ $requests->lastItem() ?? 0 }}</strong> dari <strong>{{ $requests->total() }}</strong> pengajuan</div>{{ $requests->onEachSide(1)->links() }}</div></div>

<div class="modal fade" id="leave-kpi-modal" tabindex="-1" aria-labelledby="leaveKpiModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div><div class="text-uppercase text-secondary small fw-semibold">Rincian KPI</div><h2 class="modal-title h5 mb-0" id="leaveKpiModalTitle">Rincian cuti</h2></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <section data-kpi-detail="quota"><p class="text-secondary">Kuota efektif adalah kuota cuti tahunan dari parameter HRIS setelah dikurangi cuti bersama yang ditetapkan mengurangi kuota.</p><div class="row g-2"><div class="col-md-4"><div class="border rounded-3 p-3"><small class="text-secondary d-block">Kuota tahunan</small><strong>{{ $baseQuota }} hari</strong></div></div><div class="col-md-4"><div class="border rounded-3 p-3"><small class="text-secondary d-block">Pengurang cuti bersama</small><strong>− {{ $collectiveDays }} hari</strong></div></div><div class="col-md-4"><div class="border rounded-3 p-3"><small class="text-secondary d-block">Kuota efektif</small><strong>{{ $quota }} hari</strong></div></div></div><div class="border rounded-3 bg-light-subtle p-3 mt-3"><div class="d-flex justify-content-between align-items-center mb-2"><strong class="small">Tanggal cuti bersama pengurang kuota</strong><span class="badge rounded-pill text-bg-warning">{{ $collectiveDays }} hari</span></div>@if($collectiveHolidays->isNotEmpty())<div class="d-flex flex-wrap gap-2">@foreach($collectiveHolidays as $holiday)<span class="badge rounded-pill bg-white text-dark border px-3 py-2" title="{{ $holiday->name }}"><span class="text-primary fw-semibold">{{ $holiday->holiday_date->format('d M Y') }}</span><span class="text-secondary fw-normal ms-1">· {{ $holiday->name }}</span></span>@endforeach</div>@else<div class="small text-secondary">Tidak ada tanggal cuti bersama yang mengurangi kuota pada tahun {{ $metricYear }}.</div>@endif</div></section>
                <section data-kpi-detail="collective" class="d-none"><p class="text-secondary">Daftar cuti bersama aktif pada {{ $metricYear }} yang ditandai mengurangi jatah cuti tahunan.</p>@if($collectiveHolidays->isNotEmpty())<div class="list-group">@foreach($collectiveHolidays as $holiday)<div class="list-group-item d-flex justify-content-between align-items-center"><span>{{ $holiday->name }}</span><strong>{{ $holiday->holiday_date->format('d/m/Y') }}</strong></div>@endforeach</div>@else<div class="alert alert-light border mb-0">Tidak ada cuti bersama yang mengurangi kuota pada tahun ini.</div>@endif</section>
                <section data-kpi-detail="used" class="d-none"><p class="text-secondary">Cuti terpakai dihitung dari pengajuan bertipe cuti dengan status <strong>Disetujui</strong> pada {{ $metricYear }}.</p><div class="alert alert-primary mb-3"><strong>{{ $usedLeave }} hari</strong> cuti telah terpakai.</div>@if($usedLeaveRequests->isNotEmpty())<div class="d-flex flex-column gap-2">@foreach($usedLeaveRequests as $usedRequest)<div class="border rounded-3 p-3"><div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2"><strong>{{ $usedRequest->request_number ?: 'Pengajuan cuti' }}</strong><span class="badge rounded-pill text-bg-success">Disetujui</span></div><div class="d-flex flex-wrap gap-2 mb-2">@foreach($usedRequest->dates as $usedDate)<span class="badge rounded-pill bg-primary-subtle text-primary-emphasis border border-primary-subtle px-3 py-2">{{ $usedDate->leave_date->format('d M Y') }}</span>@endforeach</div><div class="small text-secondary"><span class="fw-semibold text-dark">Keperluan:</span> {{ $usedRequest->reason ?: 'Tidak ada keterangan.' }}</div></div>@endforeach</div>@else<div class="alert alert-light border mb-0">Belum ada cuti yang disetujui pada tahun {{ $metricYear }}.</div>@endif</section>
                <section data-kpi-detail="remaining" class="d-none"><p class="text-secondary mb-0">Sisa cuti = kuota efektif dikurangi cuti yang sudah disetujui.</p><div class="row g-2 mt-2"><div class="col-md-6"><div class="border rounded-3 p-3"><small class="text-secondary d-block">Kuota efektif</small><strong>{{ $quota }} hari</strong></div></div><div class="col-md-6"><div class="border rounded-3 p-3"><small class="text-secondary d-block">Cuti terpakai</small><strong>− {{ $usedLeave }} hari</strong></div></div></div><div class="alert alert-success mt-3 mb-0"><strong>{{ $remainingLeave }} hari</strong> sisa cuti tersedia.</div></section>
                <section data-kpi-detail="sick" class="d-none"><p class="text-secondary">Sakit tercatat dihitung dari pengajuan bertipe sakit dengan status <strong>Disetujui</strong> pada {{ $metricYear }}. Sakit tidak mengurangi kuota cuti tahunan.</p><div class="alert alert-light border mb-3"><strong>{{ $sickDays }} hari</strong> sakit tercatat.</div>@if($sickRequests->isNotEmpty())<div class="d-flex flex-column gap-2">@foreach($sickRequests as $sickRequest)<div class="border rounded-3 p-3"><div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2"><strong>{{ $sickRequest->request_number ?: 'Pengajuan sakit' }}</strong><span class="badge rounded-pill text-bg-success">Disetujui</span></div><div class="d-flex flex-wrap gap-2 mb-2">@foreach($sickRequest->dates as $sickDate)<span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2">{{ $sickDate->leave_date->format('d M Y') }}</span>@endforeach</div><div class="small text-secondary"><span class="fw-semibold text-dark">Alasan:</span> {{ $sickRequest->reason ?: 'Tidak ada keterangan.' }}</div></div>@endforeach</div>@else<div class="alert alert-light border mb-0">Belum ada pengajuan sakit yang disetujui pada tahun {{ $metricYear }}.</div>@endif</section>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelectorAll('[data-kpi]').forEach((button) => button.addEventListener('click', () => {
        const key = button.dataset.kpi;
        const titles = { quota: 'Kuota cuti', collective: 'Cuti bersama', used: 'Cuti terpakai', remaining: 'Sisa cuti', sick: 'Sakit tercatat' };
        document.getElementById('leaveKpiModalTitle').textContent = titles[key] || 'Rincian cuti';
        document.querySelectorAll('[data-kpi-detail]').forEach((detail) => detail.classList.toggle('d-none', detail.dataset.kpiDetail !== key));
    }));
</script>
@endsection
