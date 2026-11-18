@extends('layouts.app', ['title' => 'Cuti dan Sakit'])

@section('content')
<style>
.workspace-leave-request-table tbody td.workspace-leave-request-number{color:var(--bs-code-color);font-family:var(--bs-font-monospace);font-size:.875em;overflow-wrap:anywhere;}
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.workspace-leave-request-table tr').forEach((row) => {
        const cells = row.children;
        if (cells.length < 2 || cells[0].hasAttribute('colspan')) return;
        row.insertBefore(cells[1], cells[0]);
        if (row.parentElement.tagName === 'TBODY') cells[0].classList.add('workspace-leave-request-number');
    });
});
</script>
<style>
.workspace-leave-request-table{min-width:1120px;table-layout:fixed}.workspace-leave-request-table th,.workspace-leave-request-table td{vertical-align:middle}.workspace-leave-request-table td:nth-child(1),.workspace-leave-request-table td:nth-child(2){overflow-wrap:anywhere}.workspace-leave-request-table td:nth-child(6) .badge{white-space:normal;line-height:1.2}.leave-request-action-toggle{display:inline-grid;width:32px;height:32px;padding:0;place-items:center;border:1px solid #dce5f0;border-radius:.6rem;background:linear-gradient(145deg,#fff,#f5f8fc);color:#526178;box-shadow:0 2px 5px rgba(15,23,42,.04);transition:all .18s ease}.leave-request-action-toggle:hover,.leave-request-action-toggle[aria-expanded=true]{border-color:#93c5fd;background:linear-gradient(145deg,#eff6ff,#f5f3ff);color:#1769aa;box-shadow:0 5px 14px rgba(37,99,235,.14)}.leave-request-action-menu{position:fixed!important;z-index:1090;min-width:210px;width:min(220px,calc(100vw - 16px));margin:0!important;padding:.3rem;border:1px solid #e4eaf2;border-radius:.75rem;background:rgba(255,255,255,.99);box-shadow:0 14px 32px rgba(15,23,42,.18)}.leave-request-menu-heading{padding:.35rem .55rem .4rem;color:#8491a5;font-size:.6rem;font-weight:800;letter-spacing:.1em}.leave-request-action-menu .dropdown-item{display:flex;align-items:center;gap:.55rem;padding:.45rem .55rem;border:0;border-radius:.55rem;color:#263449;text-align:left;white-space:normal}.leave-request-action-menu .dropdown-item:hover{background:linear-gradient(100deg,#eff6ff,#f5f3ff);color:#075da5}.leave-request-action-menu .dropdown-item>span:last-child{display:grid;gap:.08rem}.leave-request-action-menu .dropdown-item strong{font-size:.78rem;font-weight:700}.leave-request-action-menu .dropdown-item small{color:#7b8798;font-size:.67rem}.leave-request-menu-icon{display:grid;width:1.75rem;height:1.75rem;flex:0 0 1.75rem;place-items:center;border:1px solid #dbeafe;border-radius:.55rem;background:linear-gradient(145deg,#eff6ff,#f5f3ff);color:#2563eb}.leave-request-menu-icon svg{width:.95rem;height:.95rem}
.workspace-leave-request-filter{container-type:inline-size}.workspace-leave-request-filter>.row{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:.65rem;margin:0}.workspace-leave-request-filter>.row>[class*="col-"]{grid-column:auto;max-width:none;padding:0;width:auto;flex:none}.workspace-leave-request-filter>.row>:nth-child(1){grid-column:span 4;grid-row:1}.workspace-leave-request-filter>.row>:nth-child(2){grid-column:span 2;grid-row:1}.workspace-leave-request-filter>.row>:nth-child(3){grid-column:span 2;grid-row:1}.workspace-leave-request-filter>.row>:nth-child(4){grid-column:span 2;grid-row:1}.workspace-leave-request-filter>.row>:nth-child(5){grid-column:span 2;grid-row:1}.workspace-leave-request-filter>.row>:nth-child(6){grid-column:1/span 2;grid-row:2}.workspace-leave-request-filter>.row>:nth-child(7){grid-column:3/span 3;grid-row:2}.workspace-leave-request-filter .workspace-filter-actions{justify-content:flex-start;width:100%}
@container (max-width:900px){.workspace-leave-request-filter>.row{grid-template-columns:repeat(6,minmax(0,1fr))}.workspace-leave-request-filter>.row>:nth-child(1){grid-column:span 6;grid-row:1}.workspace-leave-request-filter>.row>:nth-child(2){grid-column:span 3;grid-row:2}.workspace-leave-request-filter>.row>:nth-child(3){grid-column:span 3;grid-row:2}.workspace-leave-request-filter>.row>:nth-child(4){grid-column:span 3;grid-row:3}.workspace-leave-request-filter>.row>:nth-child(5){grid-column:span 3;grid-row:3}.workspace-leave-request-filter>.row>:nth-child(6){grid-column:1/span 2;grid-row:4}.workspace-leave-request-filter>.row>:nth-child(7){grid-column:3/span 3;grid-row:4}}
@container (max-width:520px){.workspace-leave-request-filter>.row{grid-template-columns:repeat(2,minmax(0,1fr));gap:.55rem}.workspace-leave-request-filter>.row>:nth-child(1){grid-column:span 2;grid-row:1}.workspace-leave-request-filter>.row>:nth-child(2){grid-column:1;grid-row:2}.workspace-leave-request-filter>.row>:nth-child(3){grid-column:2;grid-row:2}.workspace-leave-request-filter>.row>:nth-child(4){grid-column:1;grid-row:3}.workspace-leave-request-filter>.row>:nth-child(5){grid-column:2;grid-row:3}.workspace-leave-request-filter>.row>:nth-child(6){grid-column:1;grid-row:4}.workspace-leave-request-filter>.row>:nth-child(7){grid-column:2;grid-row:4}}
</style>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><div class="text-uppercase text-secondary small fw-semibold mb-2">Kehadiran</div><h1 class="h3 mb-1">{{ $summaryView ? 'Ringkasan Cuti & Sakit Karyawan' : 'Cuti & sakit' }}</h1><p class="text-secondary mb-0">{{ $canViewAll ? 'Daftar pengajuan dalam scope perusahaan Anda.' : 'Riwayat pengajuan cuti dan sakit Anda.' }}</p></div>
    @if($canCreate && ! $summaryView)<a href="{{ route('workspace.hris.leave-requests.create') }}" class="btn leave-request-create-button"><span class="leave-request-create-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span><span>Ajukan cuti/sakit</span></a>@endif
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
<form method="GET" class="card card-body border-0 shadow-sm workspace-mobile-panel-content workspace-list-filter-form workspace-leave-request-filter">
    <div class="row g-2 align-items-end">
        <div class="col-md-3"><div class="workspace-floating-field"><input type="search" name="q" value="{{ $searchKeyword }}" class="form-control" placeholder="Nama karyawan, NIP, atau nomor pengajuan"><label class="form-label">Kata kunci</label></div></div>
        <div class="col-md-2"><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-empty-label="Semua jenis"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Filter jenis"><span data-dropdown-label>{{ count($type) ? count($type).' jenis dipilih' : 'Semua jenis' }}</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><div class="workspace-dropdown-options"><label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="type[]" value="leave" data-dropdown-item data-dropdown-value="Cuti" @checked(in_array('leave', $type, true))><span>Cuti</span></label><label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="type[]" value="sick" data-dropdown-item data-dropdown-value="Sakit" @checked(in_array('sick', $type, true))><span>Sakit</span></label></div></div></div><label class="form-label">Jenis</label></div>
        <div class="col-md-2"><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-empty-label="Semua status"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Filter status"><span data-dropdown-label>{{ count($status) ? count($status).' status dipilih' : 'Semua status' }}</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><div class="workspace-dropdown-options">@foreach(['draft'=>'Draft','pending_manager'=>'Menunggu atasan','pending_hr'=>'Menunggu HR','needs_revision'=>'Revisi','approved'=>'Disetujui','rejected'=>'Ditolak'] as $key => $label)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="status[]" value="{{ $key }}" data-dropdown-item data-dropdown-value="{{ $label }}" @checked(in_array($key, $status, true))><span>{{ $label }}</span></label>@endforeach</div></div></div><label class="form-label">Status</label></div>
        <div class="col-md-2"><label class="form-label">Tahun</label><select name="year" class="form-select" data-year-filter><option value="all" @selected($year === null)>Semua tahun</option>@foreach(range($activeYear - 2, $activeYear + 1) as $optionYear)<option value="{{ $optionYear }}" @selected($year === $optionYear)>{{ $optionYear }}{{ $optionYear === $activeYear ? ' (aktif)' : '' }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Bulan</label><select name="month" class="form-select" @disabled($year === null)><option value="">Semua bulan</option>@foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $monthNumber => $monthName)<option value="{{ $monthNumber }}" @selected($month === $monthNumber)>{{ $monthName }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Baris</label><select name="limit" class="form-select">@foreach([10,25,50,100] as $limit)<option value="{{ $limit }}" @selected($perPage === $limit)>{{ $limit }}</option>@endforeach</select></div>
        <div class="col-md-2"><div class="workspace-filter-actions"><a href="{{ route('workspace.hris.leave-requests') }}" class="btn btn-sm btn-outline-secondary">Reset</a><button class="btn btn-sm btn-primary" type="submit">Terapkan</button></div></div>
    </div>
</form>
</section>

<script>
    document.querySelector('[data-year-filter]')?.addEventListener('change', (event) => {
        const month = document.querySelector('select[name="month"]');
        if (!month) return;
        const allYears = event.target.value === 'all';
        month.disabled = allYears;
        if (allYears) month.value = '';
    });
</script>

<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0 workspace-leave-request-table"><colgroup><col style="width:16%"><col style="width:18%"><col style="width:8%"><col style="width:25%"><col style="width:7%"><col style="width:16%"><col style="width:10%"></colgroup><thead><tr><th>Karyawan</th><th><a class="workspace-sort-link {{ $sort === 'request_number' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'request_number', 'direction' => $sort === 'request_number' && $direction === 'asc' ? 'desc' : 'asc']) }}">Nomor pengajuan</a></th><th>Jenis</th><th><a class="workspace-sort-link {{ $sort === 'start_date' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'start_date', 'direction' => $sort === 'start_date' && $direction === 'asc' ? 'desc' : 'asc']) }}">Tanggal pengajuan</a></th><th>Hari</th><th><a class="workspace-sort-link {{ $sort === 'status' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => $sort === 'status' && $direction === 'asc' ? 'desc' : 'asc']) }}">Status</a></th><th class="text-end">Aksi</th></tr></thead><tbody>@forelse($requests as $item)<tr><td><div class="fw-semibold">{{ $item->employee?->full_name ?? '-' }}</div><small class="text-secondary">NIP: {{ $item->employee?->employee_number ?? '-' }}</small></td><td class="text-break">{{ $item->request_number ?: '—' }}</td><td>{{ $item->type === 'leave' ? 'Cuti' : 'Sakit' }}</td><td>{{ $item->date_list }}</td><td>{{ $item->total_days }}</td><td><span class="badge text-bg-{{ in_array($item->status, ['approved'], true) ? 'success' : (in_array($item->status, ['rejected'], true) ? 'danger' : 'warning') }}">{{ $item->status_label }}</span></td><td class="text-end"><button class="leave-request-action-toggle" type="button" data-leave-request-action-toggle data-menu-id="leave-request-action-menu-{{ $item->getKey() }}" aria-controls="leave-request-action-menu-{{ $item->getKey() }}" aria-haspopup="true" aria-expanded="false" aria-label="Aksi pengajuan {{ $item->request_number ?: $item->employee?->full_name }}"><svg viewBox="0 0 24 24" width="17" height="17" fill="currentColor" aria-hidden="true"><circle cx="12" cy="5" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="19" r="1.8"/></svg></button><div id="leave-request-action-menu-{{ $item->getKey() }}" class="dropdown-menu leave-request-action-menu" role="menu"><div class="leave-request-menu-heading">AKSI PENGAJUAN</div><a class="dropdown-item" role="menuitem" href="{{ route('workspace.hris.leave-requests.show', ['leaveRequest' => $item, 'from' => 'leave-requests']) }}"><span class="leave-request-menu-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.3-6 9.5-6 9.5 6 9.5 6-3.3 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/></svg></span><span><strong>Lihat detail</strong><small>Buka informasi pengajuan</small></span></a>@if(filled($item->supporting_document_id))@php($attachmentExtension = strtolower(pathinfo((string) $item->supporting_document_id, PATHINFO_EXTENSION)))@php($attachmentPreviewType = match ($attachmentExtension) { 'pdf' => 'application/pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp' => 'image/'.$attachmentExtension, default => 'application/octet-stream' })<a class="dropdown-item" role="menuitem" href="{{ route('workspace.hris.leave-requests.supporting-document', ['leaveRequest' => $item, 'preview' => 1]) }}" data-workspace-preview data-preview-url="{{ route('workspace.hris.leave-requests.supporting-document', ['leaveRequest' => $item, 'preview' => 1]) }}" data-preview-fallback-url="{{ route('workspace.hris.leave-requests.supporting-document', $item) }}" data-preview-type="{{ $attachmentPreviewType }}" data-preview-title="Dokumen pendukung {{ $item->request_number }}"><span class="leave-request-menu-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 8V3.5h10V8M7 17H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2M7 14h10v6.5H7V14Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span><span><strong>Lihat dokumen</strong><small>Pratinjau bukti pendukung</small></span></a>@endif@if($item->status === 'needs_revision')<a class="dropdown-item" role="menuitem" href="{{ route('workspace.hris.leave-requests.edit', $item) }}"><span class="leave-request-menu-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14 5 5 5M4 20l4.2-.8L19 8.4 15.6 5 4.8 15.8 4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span><strong>Perbaiki pengajuan</strong><small>Tanggapi permintaan revisi</small></span></a>@endif@if($item->status === 'draft')<a class="dropdown-item" role="menuitem" href="{{ route('workspace.hris.leave-requests.edit', $item) }}"><span class="leave-request-menu-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20h4l10.8-10.8a2.1 2.1 0 0 0-4-4L4 16v4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span><span><strong>Lanjutkan draft</strong><small>Edit dan kirim pengajuan</small></span></a><form method="POST" action="{{ route('workspace.hris.leave-requests.destroy', $item) }}" class="dropdown-item p-0" data-confirm="Hapus draft cuti/sakit ini? Draft tidak dapat dipulihkan.">@csrf @method('DELETE')<button type="submit" class="dropdown-item text-danger"><span class="leave-request-menu-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M6 7l1 14h10l1-14M9 7V4h6v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span><strong>Hapus draft</strong><small>Draft tidak dapat dipulihkan</small></span></button></form>@endif</div></td></tr>@empty<tr><td colspan="7" class="text-center text-secondary py-5">Belum ada pengajuan.</td></tr>@endforelse</tbody></table></div><div class="card-footer bg-white workspace-list-pagination"><div class="workspace-pagination-summary">Menampilkan <strong>{{ $requests->firstItem() ?? 0 }}</strong>–<strong>{{ $requests->lastItem() ?? 0 }}</strong> dari <strong>{{ $requests->total() }}</strong> pengajuan</div>{{ $requests->onEachSide(1)->links() }}</div></div>

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
<script>
document.addEventListener('DOMContentLoaded', () => {
    let activeMenu = null;
    const closeMenu = (restoreFocus = false) => {
        if (!activeMenu) return;
        activeMenu.menu.classList.remove('show');
        activeMenu.toggle.setAttribute('aria-expanded', 'false');
        if (restoreFocus) activeMenu.toggle.focus();
        activeMenu = null;
    };
    const placeMenu = () => {
        if (!activeMenu) return;
        const { menu, toggle } = activeMenu;
        const anchor = toggle.getBoundingClientRect();
        menu.style.visibility = 'hidden';
        menu.style.left = '0px';
        menu.style.top = '0px';
        const box = menu.getBoundingClientRect();
        menu.style.left = `${Math.max(8, Math.min(window.innerWidth - box.width - 8, anchor.right - box.width))}px`;
        const below = anchor.bottom + 8;
        menu.style.top = `${below + box.height <= window.innerHeight - 8 ? below : Math.max(8, anchor.top - box.height - 8)}px`;
        menu.style.visibility = 'visible';
    };
    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-leave-request-action-toggle]');
        if (toggle) {
            event.preventDefault();
            if (activeMenu?.toggle === toggle) return closeMenu();
            closeMenu();
            const menu = document.getElementById(toggle.dataset.menuId);
            if (!menu) return;
            document.body.append(menu);
            menu.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
            activeMenu = { menu, toggle };
            placeMenu();
            return;
        }
        if (activeMenu?.menu.contains(event.target)) {
            if (event.target.closest('a')) closeMenu();
            return;
        }
        closeMenu();
    });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeMenu(true); });
    window.addEventListener('resize', placeMenu);
    window.addEventListener('scroll', placeMenu, true);
});
</script>
@endsection
