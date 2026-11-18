@extends('layouts.app', ['title' => 'Detail Lembur'])
@section('content')
<style>
.overtime-detail .card { border: 1px solid #e0e9f3 !important; border-radius: 1rem; box-shadow: 0 .45rem 1.2rem rgba(26, 52, 82, .07) !important; overflow: hidden; }
.overtime-detail { display: grid; grid-template-columns: minmax(0, 1fr) 320px; column-gap: 1rem; row-gap: .4rem; align-items: start; }
.overtime-detail > .d-flex, .overtime-detail > .alert { grid-column: 1 / -1; }
.overtime-detail > .detail-main-column { display: contents; }
.overtime-detail > .detail-main-column > .row:first-child { grid-column: 1; margin-bottom: 0 !important; min-width: 0; }
.overtime-detail > .detail-main-column > .row:first-child > [class*="col-"] { padding-bottom: 0; }
.overtime-detail > .detail-main-column > .row:has(.overtime-reference-card) { display: contents; }
.overtime-detail > .detail-main-column > .row:has(.overtime-reference-card) > .col-lg-6:first-child { grid-column: 1; padding: 0; width: 100%; }
.overtime-detail > .detail-main-column > .row:has(.overtime-workflow-card) > .col-lg-6:last-child { grid-column: 2; grid-row: 2 / span 3; padding: 0; width: 100%; }
.overtime-detail > .detail-main-column > .row:has(.overtime-workflow-card) > .col-lg-6:last-child { transform: translateY(-18px); }
.overtime-detail:has(> .alert) > .detail-main-column > .row:has(.overtime-workflow-card) > .col-lg-6:last-child { grid-row: 3 / span 3; }
.overtime-detail > .detail-main-column > .overtime-detail-table-card { grid-column: 1; min-width: 0; }
.overtime-detail .card-header { background: linear-gradient(105deg,#f4f9ff,#fbfaff); border-bottom: 1px solid #e7eef6; color: #29445f; padding: .85rem 1rem; }
.overtime-info-card > .card-header, .overtime-summary-card > .card-header, .overtime-detail-table-card > .card-header { min-height: 48px; padding: .5rem .85rem; }
.overtime-info-card > .card-header .overtime-detail-heading-icon, .overtime-summary-card > .card-header .overtime-detail-heading-icon, .overtime-detail-table-card > .card-header .overtime-detail-heading-icon { flex-basis: 30px; height: 30px; width: 30px; }
.overtime-detail .card-body { padding: 1.15rem; }
.overtime-detail .detail-label { color: #73869a; font-size: .7rem; margin-bottom: .22rem; }
.overtime-detail .detail-value { color: #233f5a; font-size: .85rem; font-weight: 700; }
.overtime-detail .col-lg-8 .card-body > .row > .col-12 { display: none; }
.overtime-detail .alert-light { border-color: #cfe2fb !important; background: linear-gradient(100deg,#edf6ff,#f5f3ff); color: #285783; border-radius: .85rem; }
.overtime-detail .display-6 { font-size: 2.15rem; letter-spacing: -.045em; }
.overtime-detail .table thead th { background: linear-gradient(100deg,#f1f7ff,#f8f7ff); border-bottom-color: #e4ecf5; color: #61788f; font-size: .68rem; text-transform: uppercase; letter-spacing: .055em; white-space: nowrap; }
.overtime-detail .table td, .overtime-detail .table th { padding: .75rem .9rem; }
.overtime-detail .table tbody tr:hover { background: #f8fbff; }
.overtime-detail-heading { align-items: center; display: flex; gap: 10px; }
.overtime-detail-heading-icon { align-items: center; background: #e7f1ff; border-radius: 9px; color: #1674bd; display: inline-flex; flex: 0 0 34px; height: 34px; justify-content: center; }
.overtime-detail-heading-icon svg { height: 18px; width: 18px; }
.overtime-detail-info-grid { display: grid; column-gap: 18px; grid-template-columns: repeat(3,minmax(0,1fr)); }
.overtime-detail-info-item { align-items: flex-start; background: transparent; border: 0; border-bottom: 1px solid #edf1f6; border-radius: 0; display: flex; gap: 8px; min-width: 0; padding: 8px 2px; }
.overtime-detail-info-item.is-violet,.overtime-detail-info-item.is-cyan,.overtime-detail-info-item.is-amber { background: transparent; border-color: #edf1f6; }
.overtime-detail-info-item:nth-last-child(-n+2) { border-bottom: 0; }
.overtime-detail-info-icon { align-items: center; background: #eaf3ff; border-radius: 8px; color: #3278b6; display: inline-flex; flex: 0 0 28px; height: 28px; justify-content: center; }
.is-violet .overtime-detail-info-icon { background: #f0ebff; color: #7358c7; }
.is-cyan .overtime-detail-info-icon { background: #e7f7f7; color: #27838a; }
.is-amber .overtime-detail-info-icon { background: #fff4dc; color: #a36c0b; }
.overtime-detail-info-icon svg { height: 15px; width: 15px; }
.overtime-detail-info-copy { min-width: 0; }
.overtime-summary-total { background: linear-gradient(135deg,#edf6ff,#f4f2ff); border: 1px solid #e0eafb; border-radius: 12px; padding: 13px 14px; }
.overtime-summary-total .display-6 { color: #1e5689; }
.overtime-summary-caption { color: #6e8195; font-size: .75rem; }
.overtime-summary-status { border-top: 1px solid #e7edf4; margin-top: 13px; padding-top: 12px; }
.overtime-status-chip { border: 1px solid transparent; border-radius: 999px; display: inline-flex; font-size: .7rem; font-weight: 750; padding: 5px 10px; }
.overtime-status-chip.is-pending { background: #fff4d8; border-color: #f3e1ad; color: #8b650b; }
.overtime-status-chip.is-approved { background: #e8f7ee; border-color: #cdebd8; color: #26774d; }
.overtime-status-chip.is-rejected { background: #ffedef; border-color: #f3d3d7; color: #b13d4a; }
.overtime-status-chip.is-revision { background: #f2edff; border-color: #e2d9fb; color: #6b50b4; }
.overtime-detail-table-card .card-header { display: flex; flex-direction: row; justify-content: space-between; }
.overtime-detail-table-card .overtime-detail-heading { flex: 0 0 auto; white-space: nowrap; }
.overtime-detail-table-card .overtime-detail-heading strong { white-space: nowrap; }
.overtime-detail-count { background: #e9f2ff; border-radius: 999px; color: #3173ad; font-size: .67rem; font-weight: 750; padding: 5px 9px; }
.overtime-day-chip { border-radius: 999px; display: inline-flex; font-size: .66rem; font-weight: 750; padding: 5px 9px; }
.overtime-day-chip.is-workday { background: #eaf4ff; color: #2b6da5; }
.overtime-day-chip.is-holiday { background: #fff0ee; color: #b94b42; }
.overtime-workflow-card { border-color: #dce7f4 !important; }
.overtime-workflow-toggle { align-items: center; background: linear-gradient(105deg,#eef7ff,#f5f2ff); border: 0; color: #284863; display: flex; gap: 10px; padding: .85rem 1rem; text-align: left; width: 100%; }
.overtime-workflow-toggle:hover { background: linear-gradient(105deg,#e8f3ff,#f0ebff); }
.overtime-workflow-toggle:focus-visible { box-shadow: inset 0 0 0 3px rgba(47,124,194,.2); outline: 0; }
.overtime-workflow-toggle-icon { align-items: center; background: #dfedff; border-radius: 9px; color: #2879bd; display: inline-flex; flex: 0 0 34px; height: 34px; justify-content: center; }
.overtime-workflow-toggle-icon svg { height: 18px; width: 18px; }
.overtime-workflow-toggle-copy { display: grid; flex: 1; gap: 2px; }
.overtime-workflow-toggle-copy strong { font-size: .82rem; }
.overtime-workflow-toggle-copy small { color: #7a8da1; font-size: .66rem; }
.overtime-workflow-chevron { color: #71869c; font-size: .8rem; transition: transform .18s ease; }
.overtime-workflow-toggle[aria-expanded="true"] .overtime-workflow-chevron { transform: rotate(180deg); }
.overtime-workflow-body { padding: 14px 15px 15px; }
.overtime-workflow-timeline { display: grid; gap: 0; }
.overtime-workflow-event { display: flex; gap: 10px; min-width: 0; padding-bottom: 15px; position: relative; }
.overtime-workflow-event:last-child { padding-bottom: 0; }
.overtime-workflow-event:not(:last-child)::after { background: linear-gradient(#d8e8f8,#e4def7); content: ""; left: 14px; position: absolute; top: 30px; bottom: 0; width: 2px; }
.overtime-workflow-event-icon { align-items: center; background: #e8f2ff; border: 1px solid #d7e8fb; border-radius: 50%; color: #2879bd; display: inline-flex; flex: 0 0 30px; height: 30px; justify-content: center; position: relative; z-index: 1; }
.overtime-workflow-event-icon svg { height: 15px; width: 15px; }
.overtime-workflow-event.is-approved .overtime-workflow-event-icon { background: #e6f7ed; border-color: #ccebd8; color: #238451; }
.overtime-workflow-event.is-rejected .overtime-workflow-event-icon { background: #ffebed; border-color: #f3d1d6; color: #b54250; }
.overtime-workflow-event.is-revision .overtime-workflow-event-icon { background: #fff4dd; border-color: #f2e2ba; color: #a16b0c; }
.overtime-workflow-event.is-resubmitted .overtime-workflow-event-icon { background: #f0eaff; border-color: #e0d7fb; color: #7252bf; }
.overtime-workflow-event-copy { min-width: 0; padding-top: 1px; }
.overtime-workflow-event-copy strong { color: #344d64; display: block; font-size: .74rem; line-height: 1.35; }
.overtime-workflow-event-copy small { color: #8392a3; display: block; font-size: .65rem; line-height: 1.45; margin-top: 3px; }
.overtime-workflow-comment { background: #f8faff; border-left: 2px solid #bfd8f2; border-radius: 0 7px 7px 0; color: #62768a; font-size: .69rem; margin-top: 6px; padding: 7px 9px; overflow-wrap: anywhere; }
.overtime-workflow-empty { background: #f7faff; border: 1px dashed #d6e4f2; border-radius: 9px; color: #7a8da1; font-size: .71rem; padding: 10px 11px; }
.overtime-reference-panel { background: linear-gradient(110deg, #eef7ff 0%, #f6f4ff 100%); border: 1px solid #cfe1ff; border-radius: 12px; padding: 14px 17px; }
.overtime-reference-title-row { align-items: center; display: flex; gap: 12px; justify-content: space-between; }
.overtime-reference-eyebrow { color: #647b95; font-size: .65rem; font-weight: 700; letter-spacing: .07em; margin-bottom: 3px; text-transform: uppercase; }
.overtime-reference-name { color: #193b62; font-size: .9rem; font-weight: 700; line-height: 1.4; overflow-wrap: anywhere; }
.overtime-reference-type { background: #fff; border: 1px solid #d9e5f5; border-radius: 999px; color: #42658c; flex: 0 0 auto; font-size: .65rem; font-weight: 700; padding: 4px 9px; }
.overtime-reference-toggle > .overtime-reference-type { margin-left: auto; }
.overtime-reference-meta { display: flex; flex-wrap: wrap; gap: 8px 30px; margin-top: 0; padding-top: 0; }
.overtime-reference-meta > div { min-width: min(220px, 100%); }
.overtime-reference-meta dt { color: #73859a; font-size: .63rem; font-weight: 500; }
.overtime-reference-meta dd, .overtime-reference-status { color: #334f6d; font-size: .7rem; font-weight: 600; margin: 2px 0 0; overflow-wrap: anywhere; }
.overtime-reference-card, .overtime-workflow-card { border: 1px solid #dce7f4 !important; border-radius: 1rem; box-shadow: 0 .45rem 1.2rem rgba(26, 52, 82, .07) !important; overflow: hidden; }
.overtime-reference-toggle { align-items: center; background: linear-gradient(105deg,#eef7ff,#f5f2ff); border: 0; color: #284863; display: flex; gap: 10px; padding: .85rem 1rem; text-align: left; width: 100%; }
.overtime-reference-toggle:hover { background: linear-gradient(105deg,#e8f3ff,#f0ebff); }
.overtime-reference-toggle:focus-visible { box-shadow: inset 0 0 0 3px rgba(47,124,194,.2); outline: 0; }
.overtime-reference-toggle-copy { display: grid; flex: 1; gap: 2px; min-width: 0; }
.overtime-reference-toggle-copy strong { font-size: .82rem; }
.overtime-reference-toggle-copy small { color: #6c8199; font-size: .66rem; overflow-wrap: anywhere; }
.overtime-reference-chevron { color: #71869c; font-size: .8rem; transition: transform .18s ease; }
.overtime-reference-toggle[aria-expanded="true"] .overtime-reference-chevron { transform: rotate(180deg); }
.overtime-reference-body { padding: 13px 15px 15px; }
@media (max-width: 991px) { .overtime-detail { display: block; } .overtime-detail > .detail-main-column { display: block; } .overtime-detail > .detail-main-column > .row:has(.overtime-reference-card) { display: flex; margin-bottom: .5rem !important; } .overtime-detail > .detail-main-column > .row:has(.overtime-reference-card) > .col-lg-6:first-child, .overtime-detail > .detail-main-column > .row:has(.overtime-workflow-card) > .col-lg-6:last-child { padding: .35rem .5rem; } .overtime-detail > .detail-main-column > .row:has(.overtime-workflow-card) > .col-lg-6:last-child { grid-column: auto; grid-row: auto; transform: none; width: 100%; } }
@media (max-width: 767px) { .overtime-reference-meta { display: grid; gap: 8px; } }
@media (max-width: 767px) { .overtime-detail .card-body { padding: 1rem; } .overtime-detail-info-grid { gap: 8px; } }
@media (max-width: 480px) { .overtime-reference-panel { padding: 12px; } .overtime-reference-meta { display: grid; gap: 8px; } }
@media (max-width: 1199px) { .overtime-detail-info-grid { grid-template-columns: repeat(2,minmax(0,1fr)); } }
@media (max-width: 480px) { .overtime-detail-info-grid { grid-template-columns: 1fr; } .overtime-detail-info-item:nth-last-child(-n+2) { border-bottom: 1px solid #edf1f6; } .overtime-detail-info-item:last-child { border-bottom: 0; } .overtime-detail .table td, .overtime-detail .table th { padding: .65rem .7rem; } }
</style>
@php($statusLabels=['pending_manager'=>'Menunggu Atasan','pending_hr'=>'Menunggu HR','needs_revision'=>'Perlu revisi','approved'=>'Disetujui HR','rejected'=>'Ditolak'])
<div class="overtime-detail {{ $item->status === 'needs_revision' ? 'has-revision' : '' }} {{ $item->overtime_type === 'external_project' ? 'has-reference' : '' }}">
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><div class="text-uppercase text-secondary small fw-semibold mb-2">HRIS · Pengajuan Lembur</div><h1 class="h3 mb-1">{{ $item->request_number }}</h1><p class="text-secondary mb-0">{{ $item->employee?->full_name }} · {{ $statusLabels[$item->status] ?? $item->status }}</p></div><div class="d-flex gap-2">@can('update',$item)<a href="{{ route('workspace.hris.overtime.edit',$item) }}" class="btn btn-warning">Perbaiki pengajuan</a>@endcan<a href="{{ $backUrl }}" class="btn btn-outline-secondary">Kembali</a></div></div>
@if($item->status==='needs_revision')<div class="alert alert-warning"><strong>Revisi diminta:</strong> {{ $item->revision_reason }}</div>@endif
<div class="detail-main-column">
<div class="row g-3 mb-3"><div class="col-lg-8"><div class="card overtime-info-card h-100"><div class="card-header"><div class="overtime-detail-heading"><span class="overtime-detail-heading-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 3.8h7l4 4V20H6a2 2 0 0 1-2-2V5.8a2 2 0 0 1 2-2h1Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 4v4h4M8 12h8M8 16h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><strong>Informasi pengajuan</strong></div></div><div class="card-body"><div class="overtime-detail-info-grid">
<div class="overtime-detail-info-item is-cyan"><span class="overtime-detail-info-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/><path d="M5 20c.7-3.2 3-5 7-5s6.3 1.8 7 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><div class="overtime-detail-info-copy"><div class="detail-label">Karyawan</div><div class="detail-value">{{ $item->employee?->full_name ?? '-' }}</div><small class="text-secondary">NIP {{ $item->employee?->employee_number ?: '-' }}</small></div></div>
<div class="overtime-detail-info-item"><span class="overtime-detail-info-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 20V6.5L12 3l8 3.5V20M2.5 20h19M8 9h.01M16 9h.01M8 13h.01M16 13h.01M10 20v-4h4v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><div class="overtime-detail-info-copy"><div class="detail-label">Terdaftar di perusahaan</div><div class="detail-value">{{ $item->employee?->company?->name ?? '-' }}</div></div></div>
<div class="overtime-detail-info-item is-violet"><span class="overtime-detail-info-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 20h16M6 20V8l6-4 6 4v12M9 11h.01M15 11h.01M9 15h.01M15 15h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><div class="overtime-detail-info-copy"><div class="detail-label">Divisi / unit</div><div class="detail-value">{{ $item->organization_unit_name ?: '-' }}</div></div></div>
<div class="overtime-detail-info-item is-cyan"><span class="overtime-detail-info-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/><path d="M5 20c.7-3.2 3-5 7-5s6.3 1.8 7 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><div class="overtime-detail-info-copy"><div class="detail-label">Jabatan saat pengajuan</div><div class="detail-value">{{ $item->job_title_snapshot ?: '-' }}</div></div></div>
<div class="overtime-detail-info-item is-amber"><span class="overtime-detail-info-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 7.5h16v12H4zM8 4v7m8-7v7M4 12h16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><div class="overtime-detail-info-copy"><div class="detail-label">Jenis dan lokasi</div><div class="detail-value">{{ $item->overtime_type==='external_project'?'External / Project':'Internal' }} · {{ $item->work_location ?: '-' }}</div></div></div>
</div></div></div></div><div class="col-lg-4"><div class="card overtime-summary-card h-100"><div class="card-header"><div class="overtime-detail-heading"><span class="overtime-detail-heading-icon" aria-hidden="true" style="background:#e8f7ef;color:#228351"><svg viewBox="0 0 24 24" fill="none"><path d="M4 19V5m0 14h16M8 15v-4m4 4V6m4 9V9m4 6V4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><strong>Ringkasan</strong></div></div><div class="card-body"><div class="overtime-summary-total"><div class="display-6 fw-semibold">{{ intdiv($item->duration_minutes,60) }}j {{ $item->duration_minutes%60 }}m</div><div class="overtime-summary-caption">{{ $item->entries->count() }} rincian realisasi</div></div><div class="overtime-summary-status"><span class="overtime-status-chip {{ in_array($item->status,['pending_manager','pending_hr'],true) ? 'is-pending' : (in_array($item->status,['approved'],true) ? 'is-approved' : (in_array($item->status,['rejected'],true) ? 'is-rejected' : (in_array($item->status,['needs_revision'],true) ? 'is-revision' : 'is-pending'))) }}">{{ $statusLabels[$item->status] ?? $item->status }}</span></div></div></div></div></div>
<div class="row g-3 mb-3">
<div class="col-lg-6"><section class="card overtime-reference-card h-100" aria-labelledby="overtime-reference-heading"><div class="card-header p-0"><button class="overtime-reference-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#overtime-reference-content" aria-expanded="false" aria-controls="overtime-reference-content"><span class="overtime-workflow-toggle-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 3.8h7l4 4V20H6a2 2 0 0 1-2-2V5.8a2 2 0 0 1 2-2h1Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 4v4h4M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="overtime-reference-toggle-copy"><strong id="overtime-reference-heading">Referensi pekerjaan</strong><small>{{ $referenceProject?->name ?? $referenceOpportunity?->name ?? $item->reference_name_snapshot ?? 'Tidak menggunakan referensi eksternal' }}</small></span><span class="overtime-reference-type">{{ ['project' => 'Project', 'opportunity' => 'Opportunity', 'none' => 'Internal / tanpa referensi'][$referenceType] ?? 'Referensi' }}</span><span class="overtime-reference-chevron" aria-hidden="true">⌄</span></button></div><div id="overtime-reference-content" class="collapse"><div class="overtime-reference-body"><dl class="overtime-reference-meta mb-0"><div><dt>Client</dt><dd>{{ $referenceClient }}</dd></div><div><dt>Pemilik perusahaan internal</dt><dd>{{ $referenceOwner }}</dd></div><div><dt>Status referensi</dt><dd>{{ $item->reference_status_snapshot ?: '—' }}</dd></div></dl></div></div></section></div>
<div class="col-lg-6"><section class="card overtime-workflow-card h-100" aria-labelledby="overtime-workflow-title"><div class="card-header p-0"><button class="overtime-workflow-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#overtime-workflow-content" aria-expanded="true" aria-controls="overtime-workflow-content"><span class="overtime-workflow-toggle-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 3.8h7l4 4V20H6a2 2 0 0 1-2-2V5.8a2 2 0 0 1 2-2h1Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 4v4h4M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="overtime-workflow-toggle-copy"><strong id="overtime-workflow-title">Riwayat workflow</strong><small>{{ $item->approvalEvents->count() + 1 }} aktivitas · status dan keputusan pengajuan</small></span><span class="overtime-workflow-chevron" aria-hidden="true">⌄</span></button></div><div id="overtime-workflow-content" class="collapse show"><div class="overtime-workflow-body"><div class="overtime-workflow-timeline"><div class="overtime-workflow-event"><span class="overtime-workflow-event-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 3.8h7l4 4V20H6a2 2 0 0 1-2-2V5.8a2 2 0 0 1 2-2h1Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 4v4h4M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><div class="overtime-workflow-event-copy"><strong>Pengajuan dibuat</strong><small>{{ $item->created_at?->format('d/m/Y H:i') }}</small></div></div>@forelse($item->approvalEvents as $event)@php($workflowStyle = ['approve' => 'is-approved', 'reject' => 'is-rejected', 'request_revision' => 'is-revision', 'resubmit' => 'is-resubmitted'][$event->action] ?? '')<div class="overtime-workflow-event {{ $workflowStyle }}"><span class="overtime-workflow-event-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">@if($event->action === 'approve')<path d="m5 12 4 4L19 6"/>@elseif($event->action === 'reject')<path d="m7 7 10 10M17 7 7 17"/>@elseif($event->action === 'request_revision')<path d="m14 5 5 5M4 20l4.2-.8L19 8.4 15.6 5 4.8 15.8 4 20Z"/>@elseif($event->action === 'resubmit')<path d="M4 7h13l-3-3m6 13H7l3 3"/><path d="M17 7h.01M7 17h.01"/>@else<circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/>@endif</svg></span><div class="overtime-workflow-event-copy"><strong>{{ ['manager'=>'Atasan','hr'=>'HR','employee'=>'Karyawan'][$event->step] ?? ucfirst($event->step) }} · {{ ['approve'=>'Disetujui','reject'=>'Ditolak','request_revision'=>'Meminta revisi','resubmit'=>'Dikirim ulang'][$event->action] ?? $event->action }}</strong><small>{{ $event->actor?->name ?? 'Pengguna' }} · {{ $event->created_at?->format('d/m/Y H:i') }}</small>@if($event->comment)<div class="overtime-workflow-comment">{{ $event->comment }}</div>@endif</div></div>@empty<div class="overtime-workflow-empty">Belum ada keputusan approval. Pengajuan akan dilanjutkan ke tahap Atasan.</div>@endforelse</div></div></div></section></div>
</div>
<div class="card overtime-detail-table-card mb-3"><div class="card-header d-flex align-items-center"><div class="overtime-detail-heading"><span class="overtime-detail-heading-icon" aria-hidden="true" style="background:#f0eaff;color:#7358c7"><svg viewBox="0 0 24 24" fill="none"><path d="M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg></span><strong>Rincian realisasi</strong></div><span class="overtime-detail-count">{{ $item->entries->count() }} rincian</span></div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>No</th><th>Tanggal</th><th>Jenis hari</th><th>Jam</th><th>Durasi diakui</th><th>Uraian pekerjaan</th></tr></thead><tbody>@foreach($item->entries as $entry)<tr><td>{{ $loop->iteration }}</td><td>{{ $entry->work_date?->format('d/m/Y') }}</td><td><span class="overtime-day-chip {{ $entry->day_type === 'holiday' ? 'is-holiday' : 'is-workday' }}">{{ $entry->day_type === 'holiday' ? 'Hari Libur' : 'Hari Kerja' }}</span></td><td class="text-nowrap">{{ substr($entry->start_time,0,5) }}–{{ substr($entry->end_time,0,5) }}</td><td class="text-nowrap fw-semibold">{{ intdiv($entry->duration_minutes,60) }}j {{ $entry->duration_minutes%60 }}m</td><td>{{ $entry->work_description }}</td></tr>@endforeach</tbody></table></div></div>
</div>
</div>
@endsection
