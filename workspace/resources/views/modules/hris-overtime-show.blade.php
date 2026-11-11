@extends('layouts.app', ['title' => 'Detail Lembur'])
@section('content')
<style>
.overtime-detail .card { border: 1px solid #e5e7eb !important; border-radius: 1rem; box-shadow: 0 .25rem 1rem rgba(16, 24, 40, .05) !important; overflow: hidden; }
.overtime-detail { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 1rem; align-items: start; }
.overtime-detail > .d-flex, .overtime-detail > .alert { grid-column: 1 / -1; }
.overtime-detail > .detail-main-column { grid-column: 1; min-width: 0; }
.overtime-detail > .card:last-child { grid-column: 2; align-self: start; }
.overtime-detail .card-header { border-bottom: 1px solid #edf0f2; padding: .9rem 1.15rem; }
.overtime-detail .card-body { padding: 1.15rem; }
.overtime-detail .detail-label { color: #667085; font-size: .78rem; margin-bottom: .2rem; }
.overtime-detail .detail-value { color: #1d2939; font-weight: 600; }
.overtime-detail .col-lg-8 .card-body > .row > .col-12 { display: none; }
.overtime-detail .alert-light { border-color: #bfdbfe !important; background: #eff6ff; color: #1e3a8a; border-radius: .8rem; }
.overtime-detail .display-6 { font-size: 2.35rem; letter-spacing: -.04em; }
.overtime-detail .table thead th { background: #f8fafc; color: #667085; font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap; }
.overtime-detail .table td, .overtime-detail .table th { padding: .8rem 1rem; }
.overtime-detail .workflow-event { position: relative; padding-left: 1.35rem; }
.overtime-detail .workflow-event::before { content: ''; position: absolute; left: .15rem; top: .35rem; width: .55rem; height: .55rem; border-radius: 50%; background: #0d6efd; box-shadow: 0 0 0 .25rem #dbeafe; }
@media (max-width: 991px) { .overtime-detail { display: block; } .overtime-detail > .card:last-child { margin-top: 1rem; } }
@media (max-width: 767px) { .overtime-detail .card-body { padding: 1rem; } }
</style>
@php($statusLabels=['pending_manager'=>'Menunggu Atasan','pending_hr'=>'Menunggu HR','needs_revision'=>'Perlu revisi','approved'=>'Disetujui HR','rejected'=>'Ditolak'])
<div class="overtime-detail {{ $item->status === 'needs_revision' ? 'has-revision' : '' }} {{ $item->overtime_type === 'external_project' ? 'has-reference' : '' }}">
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><div class="text-uppercase text-secondary small fw-semibold mb-2">HRIS · Pengajuan Lembur</div><h1 class="h3 mb-1">{{ $item->request_number }}</h1><p class="text-secondary mb-0">{{ $item->employee?->full_name }} · {{ $statusLabels[$item->status] ?? $item->status }}</p></div><div class="d-flex gap-2">@can('update',$item)<a href="{{ route('workspace.hris.overtime.edit',$item) }}" class="btn btn-warning">Perbaiki pengajuan</a>@endcan<a href="{{ route('workspace.hris.overtime') }}" class="btn btn-outline-secondary">Kembali</a></div></div>
@if($item->status==='needs_revision')<div class="alert alert-warning"><strong>Revisi diminta:</strong> {{ $item->revision_reason }}</div>@endif
@if($item->overtime_type === 'external_project')<div class="alert alert-light border"><strong>Referensi pekerjaan:</strong> {{ $item->reference_name_snapshot ?: 'Tanpa referensi' }} @if($item->reference_status_snapshot)<span class="badge text-bg-light">{{ $item->reference_status_snapshot }}</span>@endif</div>@endif
<div class="detail-main-column">
<div class="row g-3 mb-3"><div class="col-lg-8"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-white fw-semibold">Informasi pengajuan</div><div class="card-body"><div class="row g-3"><div class="col-md-6"><small class="text-secondary">Perusahaan</small><div>{{ $item->employee?->company?->name ?? '-' }}</div></div><div class="col-md-6"><small class="text-secondary">Divisi / unit</small><div>{{ $item->organization_unit_name ?: '-' }}</div></div><div class="col-md-6"><small class="text-secondary">Jabatan saat pengajuan</small><div>{{ $item->job_title_snapshot ?: '-' }}</div></div><div class="col-md-6"><small class="text-secondary">Jenis dan lokasi</small><div>{{ $item->overtime_type==='external_project'?'External / Project':'Internal' }} · {{ $item->work_location ?: '-' }}</div></div><div class="col-12"><small class="text-secondary">Alasan / konteks</small><div>{{ $item->reason }}</div></div></div></div></div></div><div class="col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-white fw-semibold">Ringkasan</div><div class="card-body"><div class="display-6 fw-semibold">{{ intdiv($item->duration_minutes,60) }}j {{ $item->duration_minutes%60 }}m</div><div class="text-secondary">{{ $item->entries->count() }} rincian realisasi</div><hr><span class="badge text-bg-light">{{ $statusLabels[$item->status] ?? $item->status }}</span></div></div></div></div>
<div class="card border-0 shadow-sm mb-3"><div class="card-header bg-white fw-semibold">Rincian realisasi</div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>No</th><th>Tanggal</th><th>Jenis hari</th><th>Jam</th><th>Durasi diakui</th><th>Uraian pekerjaan</th></tr></thead><tbody>@foreach($item->entries as $entry)<tr><td>{{ $loop->iteration }}</td><td>{{ $entry->work_date?->format('d/m/Y') }}</td><td><span class="badge {{ $entry->day_type === 'holiday' ? 'text-bg-danger' : 'text-bg-light' }}">{{ $entry->day_type === 'holiday' ? 'Hari Libur' : 'Hari Kerja' }}</span></td><td>{{ substr($entry->start_time,0,5) }}–{{ substr($entry->end_time,0,5) }}</td><td>{{ intdiv($entry->duration_minutes,60) }}j {{ $entry->duration_minutes%60 }}m</td><td>{{ $entry->work_description }}</td></tr>@endforeach</tbody></table></div></div>
</div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white fw-semibold">Riwayat workflow</div><div class="card-body"><div class="mb-3"><strong>Pengajuan dibuat</strong><div class="text-secondary small">{{ $item->created_at?->format('d/m/Y H:i') }}</div></div>@forelse($item->approvalEvents as $event)<div class="border-start border-2 ps-3 mb-3"><strong>{{ ['manager'=>'Atasan','hr'=>'HR','employee'=>'Karyawan'][$event->step] ?? ucfirst($event->step) }} · {{ ['approve'=>'Disetujui','reject'=>'Ditolak','request_revision'=>'Meminta revisi','resubmit'=>'Dikirim ulang'][$event->action] ?? $event->action }}</strong><div class="text-secondary small">{{ $event->actor?->name ?? 'Pengguna' }} · {{ $event->created_at?->format('d/m/Y H:i') }}</div>@if($event->comment)<div>{{ $event->comment }}</div>@endif</div>@empty<div class="text-secondary">Belum ada keputusan approval.</div>@endforelse</div></div>
</div>
@endsection
