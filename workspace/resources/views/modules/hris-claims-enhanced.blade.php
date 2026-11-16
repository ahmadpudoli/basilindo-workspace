@extends('layouts.app', ['title' => 'Reimbursement dan Cash Advance'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><div class="text-uppercase text-secondary small fw-semibold mb-2">Claim</div><h1 class="h3 mb-1">{{ $summaryView ? ($activeTabType === 'reimbursement' ? 'Ringkasan Reimbursement' : 'Ringkasan Cash Advance') : 'Reimbursement & Cash Advance' }}</h1><p class="text-secondary mb-0">{{ $summaryView ? 'Daftar pengajuan dalam scope perusahaan Anda.' : 'Satu daftar pengajuan dengan workflow dan formulir yang berbeda.' }}</p></div>@if(! $summaryView)<div class="dropdown"><button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Buat Pengajuan</button><ul class="dropdown-menu dropdown-menu-end shadow-sm"><li><a class="dropdown-item" href="{{ route('workspace.hris.claims.create', ['type' => 'reimbursement']) }}">Pengajuan Reimbursement</a></li><li><a class="dropdown-item" href="{{ route('workspace.hris.claims.create', ['type' => 'cash_advance']) }}">Pengajuan Cash Advance</a></li></ul></div>@endif</div>
@if($summaryView)
<style>
.claim-summary-hero{position:relative;overflow:hidden;padding:.85rem 1.15rem;border:1px solid #cfe3ff;border-radius:.9rem;background:linear-gradient(110deg,#eaf4ff 0%,#fff 52%,#f1efff 100%);box-shadow:0 8px 24px rgba(27,71,125,.08)}
.claim-summary-hero:after{position:absolute;right:-3rem;top:-5rem;width:13rem;height:13rem;border-radius:50%;background:radial-gradient(circle,rgba(59,130,246,.14),transparent 68%);content:"";pointer-events:none}
body:has(.claim-summary-hero) h1.h3{color:#17365d;font-size:1.55rem;font-weight:750;letter-spacing:-.025em}body:has(.claim-summary-hero) h1.h3:before{display:inline-block;width:4px;height:1.15em;margin-right:.6rem;border-radius:9px;background:linear-gradient(180deg,#1683df,#8b5cf6);content:"";vertical-align:-.14em}body:has(.claim-summary-hero) h1.h3+p{font-size:.88rem}
.claim-summary-hero>*{position:relative;z-index:1}.claim-summary-kicker{color:#2563eb;font-size:.72rem;font-weight:800;letter-spacing:.11em;text-transform:uppercase}.claim-summary-company{display:inline-flex;align-items:center;gap:.4rem;padding:.4rem .65rem;border:1px solid #dbeafe;border-radius:999px;background:#ffffffc9;color:#334155;font-size:.8rem;font-weight:650}
body:has(.claim-summary-hero) .claim-summary-company{display:none}
.claim-summary-metrics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:0;margin-bottom:.9rem;overflow:hidden;border:1px solid #e1e8f2;border-radius:.9rem;background:linear-gradient(110deg,#fff,#fbfcff);box-shadow:0 6px 18px rgba(15,23,42,.045)}.claim-summary-metric{min-width:0;padding:.75rem 1rem;border:0;border-right:1px solid #e8edf5;border-radius:0;background:transparent;box-shadow:none}.claim-summary-metric:last-child{border-right:0}.claim-summary-metric-label{color:#64748b;font-size:.75rem;font-weight:600}.claim-summary-metric-value{margin-top:.2rem;color:#172033;font-size:1.15rem;font-weight:750;line-height:1.2}.claim-summary-metric-icon{display:none}
.claim-summary-list{display:grid;gap:.85rem}.claim-summary-list ~ .card{display:none}.claim-summary-card{overflow:hidden;border:1px solid #e5eaf2;border-radius:1rem;background:#fff;box-shadow:0 6px 22px rgba(15,23,42,.045);transition:box-shadow .18s ease,transform .18s ease}.claim-summary-card:hover{transform:translateY(-1px);box-shadow:0 12px 28px rgba(15,23,42,.08)}.claim-summary-card-main{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:1rem;padding:1.1rem 1.25rem}.claim-summary-person{display:flex;align-items:center;gap:.8rem;min-width:0}.claim-summary-avatar{display:grid;width:2.8rem;height:2.8rem;flex:0 0 2.8rem;place-items:center;border-radius:.85rem;background:linear-gradient(145deg,#dbeafe,#ede9fe);color:#1d4ed8;font-weight:800}.claim-summary-person-name{overflow:hidden;color:#172033;font-weight:750;text-overflow:ellipsis;white-space:nowrap}.claim-summary-subtitle{color:#64748b;font-size:.8rem}.claim-summary-amount{color:#172033;font-size:1.05rem;font-weight:750;text-align:right;white-space:nowrap}.claim-summary-meta{display:flex;flex-wrap:wrap;gap:.45rem .6rem;padding:.8rem 1.25rem;border-top:1px solid #f0f3f8;background:#fbfcfe}.claim-summary-meta-item{display:inline-flex;align-items:center;gap:.35rem;max-width:100%;color:#526178;font-size:.8rem}.claim-summary-meta-item span:last-child{overflow-wrap:anywhere}.claim-summary-purpose{padding:.75rem 1.25rem 0;color:#526178;font-size:.86rem}.claim-summary-empty{padding:3rem 1rem;border:1px dashed #cbd5e1;border-radius:1rem;background:#fff;text-align:center}.claim-summary-empty-icon{display:grid;width:3rem;height:3rem;margin:0 auto .75rem;place-items:center;border-radius:1rem;background:#eff6ff;color:#2563eb;font-size:1.35rem}
@media(max-width:1100px){.workspace-claim-summary-filter>.row{grid-template-columns:repeat(3,minmax(0,1fr))}.workspace-claim-summary-filter>.row>.col-md-4:last-child{grid-column:1/-1}.workspace-claim-summary-filter .workspace-filter-actions{justify-content:flex-end}}
@media(max-width:767.98px){.claim-summary-hero{padding:.8rem .9rem}.claim-summary-metrics{grid-template-columns:repeat(2,minmax(0,1fr))}.claim-summary-metric{padding:.7rem .85rem;border-bottom:1px solid #e8edf5}.claim-summary-metric:nth-child(odd){border-right:1px solid #e8edf5}.claim-summary-metric:nth-child(even){border-right:0}.claim-summary-metric:nth-last-child(-n+2){border-bottom:0}.claim-summary-metric-value{font-size:1.05rem}.workspace-claim-summary-filter>.row{grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem}.workspace-claim-summary-filter>.row>.col-md-4:last-child{grid-column:1/-1}.workspace-claim-summary-filter .workspace-filter-actions{justify-content:flex-end}.claim-summary-card-main{grid-template-columns:minmax(0,1fr);gap:.65rem;padding:.95rem}.claim-summary-amount{text-align:left}.claim-summary-purpose{padding:.5rem .95rem 0}.claim-summary-meta{padding:.75rem .95rem}.claim-summary-meta-item{flex-basis:calc(50% - .4rem)}}
@media(max-width:480px){.workspace-claim-summary-filter>.row{grid-template-columns:minmax(0,1fr)}.workspace-claim-summary-filter>.row>.col-md-4:last-child{grid-column:auto}.workspace-claim-summary-filter .workspace-filter-actions .btn{flex:1}.claim-summary-metric{padding:.65rem}.claim-summary-metric-label{font-size:.7rem}.claim-summary-metric-value{font-size:.95rem}}
@media(max-width:380px){.claim-summary-meta-item{flex-basis:100%}}
.claim-summary-table-container + .card{display:none}
.claim-summary-table{width:100%;min-width:1120px;table-layout:fixed}
.claim-summary-table th:nth-child(6),.claim-summary-table td:nth-child(6){white-space:normal}
.claim-summary-table td:nth-child(6) .badge{display:inline-block;max-width:100%;white-space:normal;line-height:1.2}
.claim-summary-table td:last-child{white-space:normal}
@media(max-width:767.98px){.claim-summary-table{min-width:1240px;table-layout:auto}.claim-summary-table th,.claim-summary-table td{vertical-align:middle}.claim-summary-table th:nth-child(4),.claim-summary-table td:nth-child(4){min-width:200px}.claim-summary-table th:nth-child(5),.claim-summary-table td:nth-child(5){min-width:170px;white-space:nowrap}.claim-summary-table th:nth-child(6),.claim-summary-table td:nth-child(6){min-width:145px;white-space:nowrap}.claim-summary-table td:nth-child(6) .badge{max-width:none;white-space:nowrap}.claim-summary-table th:nth-child(7),.claim-summary-table td:nth-child(7){min-width:76px}}
.workspace-claim-summary-filter{padding:.85rem 1rem!important;border:1px solid #e7edf5!important;border-radius:.9rem!important;background:linear-gradient(120deg,#fff,#fbfcff)!important}.workspace-claim-summary-filter>.row{display:grid;grid-template-columns:minmax(130px,1.2fr) minmax(130px,1.2fr) minmax(130px,1.2fr) minmax(120px,1fr) minmax(120px,1fr) minmax(75px,.6fr) minmax(145px,auto);gap:.65rem .75rem;align-items:end;margin:0;--bs-gutter-x:0;--bs-gutter-y:0}.workspace-claim-summary-filter>.row>[class*="col-"]{width:auto;max-width:none;min-width:0;flex:none;padding:0}.workspace-claim-summary-filter .workspace-filter-actions{display:flex;width:100%;justify-content:flex-end;gap:.5rem}.workspace-claim-summary-filter .workspace-filter-actions .btn{min-width:76px;margin:0}.workspace-claim-summary-filter .workspace-floating-field .form-control,.workspace-claim-summary-filter .workspace-floating-field .form-select{min-width:0}
.workspace-claim-summary-filter .workspace-filter-actions{gap:0}.workspace-claim-summary-filter .workspace-filter-actions .btn:first-child{border-radius:.45rem 0 0 .45rem}.workspace-claim-summary-filter .workspace-filter-actions .btn:last-child{margin-left:-1px;border-radius:0 .45rem .45rem 0}
.workspace-claim-summary-filter>.row{grid-template-columns:repeat(12,minmax(0,1fr))}.workspace-claim-summary-filter>.row>:nth-child(3){grid-column:1/span 4;grid-row:1}.workspace-claim-summary-filter>.row>:nth-child(4){grid-column:5/span 2;grid-row:1}.workspace-claim-summary-filter>.row>:nth-child(5){grid-column:7/span 2;grid-row:1}.workspace-claim-summary-filter>.row>:nth-child(6){grid-column:9/span 2;grid-row:1}.workspace-claim-summary-filter>.row>:nth-child(11){grid-column:11/span 2;grid-row:1}.workspace-claim-summary-filter>.row>:nth-child(7){grid-column:1/span 2;grid-row:2}.workspace-claim-summary-filter>.row>:nth-child(8){grid-column:3/span 2;grid-row:2}.workspace-claim-summary-filter>.row>:nth-child(9){grid-column:5/span 2;grid-row:2}.workspace-claim-summary-filter>.row>:nth-child(10){grid-column:7/span 2;grid-row:2}.workspace-claim-summary-filter>.row>:nth-child(12){grid-column:9/span 2;grid-row:2}
@media(max-width:1100px){.workspace-claim-summary-filter>.row{grid-template-columns:repeat(6,minmax(0,1fr))}.workspace-claim-summary-filter>.row>:nth-child(n+3){grid-row:auto}.workspace-claim-summary-filter>.row>:nth-child(3){grid-column:span 3}.workspace-claim-summary-filter>.row>:nth-child(4),.workspace-claim-summary-filter>.row>:nth-child(5),.workspace-claim-summary-filter>.row>:nth-child(6),.workspace-claim-summary-filter>.row>:nth-child(7),.workspace-claim-summary-filter>.row>:nth-child(8),.workspace-claim-summary-filter>.row>:nth-child(9),.workspace-claim-summary-filter>.row>:nth-child(10),.workspace-claim-summary-filter>.row>:nth-child(11){grid-column:span 2}.workspace-claim-summary-filter>.row>.col-md-4:last-child{grid-column:1/-1}}
@media(max-width:767.98px){.workspace-claim-summary-filter>.row{grid-template-columns:repeat(2,minmax(0,1fr))}.workspace-claim-summary-filter>.row>:nth-child(3),.workspace-claim-summary-filter>.row>:nth-child(12){grid-column:span 2}.workspace-claim-summary-filter>.row>:nth-child(4),.workspace-claim-summary-filter>.row>:nth-child(5),.workspace-claim-summary-filter>.row>:nth-child(6),.workspace-claim-summary-filter>.row>:nth-child(7),.workspace-claim-summary-filter>.row>:nth-child(8),.workspace-claim-summary-filter>.row>:nth-child(9),.workspace-claim-summary-filter>.row>:nth-child(10),.workspace-claim-summary-filter>.row>:nth-child(11){grid-column:span 1}}
@media(max-width:480px){.workspace-claim-summary-filter>.row{grid-template-columns:minmax(0,1fr)}.workspace-claim-summary-filter>.row>:nth-child(3),.workspace-claim-summary-filter>.row>:nth-child(12){grid-column:span 1}}
.claim-summary-action-toggle{display:inline-grid;width:32px;height:32px;padding:0;place-items:center;border:1px solid #dce5f0;border-radius:.6rem;background:linear-gradient(145deg,#fff,#f5f8fc);color:#526178;box-shadow:0 2px 5px rgba(15,23,42,.04);transition:all .18s ease}.claim-summary-action-toggle:hover,.claim-summary-action-toggle[aria-expanded="true"]{border-color:#93c5fd;background:linear-gradient(145deg,#eff6ff,#f5f3ff);color:#1769aa;box-shadow:0 5px 14px rgba(37,99,235,.14)}.claim-summary-action-toggle svg{display:block;width:17px;height:17px}
.claim-summary-action-menu{position:fixed!important;z-index:1090;min-width:210px;width:min(220px,calc(100vw - 16px));margin:0!important;padding:.3rem;border:1px solid #e4eaf2;border-radius:.75rem;background:rgba(255,255,255,.99);box-shadow:0 14px 32px rgba(15,23,42,.18);animation:claimMenuIn .14s ease-out}.claim-summary-menu-heading{padding:.35rem .55rem .4rem;color:#8491a5;font-size:.6rem;font-weight:800;letter-spacing:.1em}.claim-summary-action-menu .dropdown-item{display:flex;align-items:center;gap:.55rem;padding:.45rem .55rem;border-radius:.55rem;color:#263449;white-space:normal;transition:background .15s ease,color .15s ease}.claim-summary-action-menu .dropdown-item:hover{background:linear-gradient(100deg,#eff6ff,#f5f3ff);color:#075da5}.claim-summary-action-menu .dropdown-item>span:last-child{display:grid;gap:.08rem}.claim-summary-action-menu .dropdown-item strong{font-size:.78rem;font-weight:700}.claim-summary-action-menu .dropdown-item small{color:#7b8798;font-size:.67rem}.claim-summary-menu-icon{display:grid;width:1.75rem;height:1.75rem;flex:0 0 1.75rem;place-items:center;border:1px solid #dbeafe;border-radius:.55rem;background:linear-gradient(145deg,#eff6ff,#f5f3ff);color:#2563eb}.claim-summary-menu-icon.print{border-color:#d1fae5;background:linear-gradient(145deg,#ecfdf5,#eff6ff);color:#07845b}.claim-summary-menu-icon svg{width:.95rem;height:.95rem}@keyframes claimMenuIn{from{opacity:0;transform:translateY(-4px) scale(.98)}to{opacity:1;transform:translateY(0) scale(1)}}
</style>
<section class="claim-summary-hero mb-3"><div class="d-flex flex-wrap align-items-center justify-content-between gap-3"><div><div class="claim-summary-kicker">Ikhtisar pengajuan · {{ $periodFrom === $periodTo ? \Illuminate\Support\Carbon::parse($periodFrom)->translatedFormat('d M Y') : \Illuminate\Support\Carbon::parse($periodFrom)->translatedFormat('d M Y').' – '.\Illuminate\Support\Carbon::parse($periodTo)->translatedFormat('d M Y') }}</div><h2 class="h5 mt-1 mb-1">{{ $activeTabType === 'reimbursement' ? 'Aktivitas reimbursement' : 'Aktivitas cash advance' }}</h2><div class="small text-secondary">Data dibatasi pada perusahaan yang dapat Anda akses. Pengajuan draft tidak termasuk.</div></div><div class="d-flex flex-wrap gap-2">@foreach($items->getCollection()->pluck('employee.company.name')->filter()->unique()->take(3) as $companyName)<span class="claim-summary-company"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 21V5.5L12 2l8 3.5V21M2 21h20M8 8h1m6 0h1M8 12h1m6 0h1M10 21v-4h4v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>{{ $companyName }}</span>@endforeach</div></div></section>
<section class="claim-summary-metrics" aria-label="Metrik pengajuan"><article class="claim-summary-metric"><div class="d-flex justify-content-between align-items-start"><div><div class="claim-summary-metric-label">Total pengajuan</div><div class="claim-summary-metric-value">{{ number_format($summaryMetrics['count'], 0, ',', '.') }}</div></div><span class="claim-summary-metric-icon"><i class="bi bi-files" aria-hidden="true"></i></span></div></article><article class="claim-summary-metric"><div class="d-flex justify-content-between align-items-start"><div><div class="claim-summary-metric-label">Total nominal</div><div class="claim-summary-metric-value small">@forelse($summaryMetrics['amounts'] as $amount)<span class="d-block">{{ $amount->currency }} {{ number_format((float) $amount->total, 0, ',', '.') }}</span>@empty<span class="text-secondary">—</span>@endforelse</div></div><span class="claim-summary-metric-icon"><i class="bi bi-wallet2" aria-hidden="true"></i></span></div></article><article class="claim-summary-metric"><div class="d-flex justify-content-between align-items-start"><div><div class="claim-summary-metric-label">Dalam proses</div><div class="claim-summary-metric-value">{{ number_format($summaryMetrics['in_progress'], 0, ',', '.') }}</div></div><span class="claim-summary-metric-icon"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span></div></article><article class="claim-summary-metric"><div class="d-flex justify-content-between align-items-start"><div><div class="claim-summary-metric-label">Disetujui / dibayar</div><div class="claim-summary-metric-value">{{ number_format($summaryMetrics['completed'], 0, ',', '.') }}</div></div><span class="claim-summary-metric-icon"><i class="bi bi-check2-circle" aria-hidden="true"></i></span></div></article></section>
@endif
<style>@unless($summaryView)
.claim-summary-action-toggle{display:inline-grid;width:32px;height:32px;padding:0;place-items:center;border:1px solid #dce5f0;border-radius:.6rem;background:linear-gradient(145deg,#fff,#f5f8fc);color:#526178;box-shadow:0 2px 5px rgba(15,23,42,.04);transition:all .18s ease}.claim-summary-action-toggle:hover,.claim-summary-action-toggle[aria-expanded="true"]{border-color:#93c5fd;background:linear-gradient(145deg,#eff6ff,#f5f3ff);color:#1769aa;box-shadow:0 5px 14px rgba(37,99,235,.14)}.claim-summary-action-toggle svg{display:block;width:17px;height:17px}
.claim-summary-action-menu{position:fixed!important;z-index:1090;min-width:210px;width:min(220px,calc(100vw - 16px));margin:0!important;padding:.3rem;border:1px solid #e4eaf2;border-radius:.75rem;background:rgba(255,255,255,.99);box-shadow:0 14px 32px rgba(15,23,42,.18);animation:claimMenuIn .14s ease-out}.claim-summary-menu-heading{padding:.35rem .55rem .4rem;color:#8491a5;font-size:.6rem;font-weight:800;letter-spacing:.1em}.claim-summary-action-menu .dropdown-item{display:flex;align-items:center;gap:.55rem;padding:.45rem .55rem;border:0;border-radius:.55rem;background:transparent;color:#263449;text-align:left;white-space:normal;transition:background .15s ease,color .15s ease}.claim-summary-action-menu .dropdown-item:hover{background:linear-gradient(100deg,#eff6ff,#f5f3ff);color:#075da5}.claim-summary-action-menu .dropdown-item>span:last-child{display:grid;gap:.08rem}.claim-summary-action-menu .dropdown-item strong{font-size:.78rem;font-weight:700}.claim-summary-action-menu .dropdown-item small{color:#7b8798;font-size:.67rem}.claim-summary-menu-icon{display:grid;width:1.75rem;height:1.75rem;flex:0 0 1.75rem;place-items:center;border:1px solid #dbeafe;border-radius:.55rem;background:linear-gradient(145deg,#eff6ff,#f5f3ff);color:#2563eb}.claim-summary-menu-icon.print{border-color:#d1fae5;background:linear-gradient(145deg,#ecfdf5,#eff6ff);color:#07845b}.claim-summary-menu-icon svg{width:.95rem;height:.95rem}.claim-summary-menu-form{margin:0}.claim-summary-menu-form .dropdown-item{width:100%}
.workspace-claim-list-filter + .card .table-responsive>table{width:100%!important;min-width:900px;table-layout:fixed!important}.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(1){width:19%!important}.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(2){width:15%!important}.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(3){width:24%!important}.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(4){width:17%!important;white-space:nowrap}.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(5){width:15%!important}.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(6){width:10%!important}.workspace-claim-list-filter + .card .table-responsive>table td:nth-child(4){white-space:nowrap}.workspace-claim-list-filter + .card .table-responsive>table td:nth-child(5) .badge{max-width:100%;white-space:normal;line-height:1.2}.workspace-claim-list-filter + .card .table-responsive>table td:last-child{white-space:nowrap}
@endunless</style>
@unless($summaryView)<div class="workspace-tab-nav mb-3"><a class="{{ $activeTabType === null ? 'active' : '' }}" href="{{ route('workspace.hris.claims') }}">Semua</a><a class="{{ $activeTabType === 'reimbursement' ? 'active' : '' }}" href="{{ route('workspace.hris.claims', ['type' => 'reimbursement']) }}">Reimbursement</a><a class="{{ $activeTabType === 'cash_advance' ? 'active' : '' }}" href="{{ route('workspace.hris.claims', ['type' => 'cash_advance']) }}">Cash Advance</a></div>@endunless
<form method="GET" class="card card-body border-0 shadow-sm mb-3 workspace-list-filter-form workspace-claim-list-filter {{ $summaryView ? 'workspace-claim-summary-filter' : '' }}"><div class="row g-3 align-items-end">
@if($summaryView)
<input type="hidden" name="view" value="summary"><input type="hidden" name="type" value="{{ $activeTabType }}">
<div class="col-md-12 claim-summary-filter-keyword"><div class="workspace-floating-field"><input type="search" name="q" value="{{ $searchKeyword }}" class="form-control" placeholder="Nama karyawan, nomor pengajuan, status, kategori"><label class="form-label">Kata kunci</label></div></div>
<div class="col-md-4 col-xl-3"><div class="workspace-floating-field"><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-summary="count" data-dropdown-empty-label="Semua perusahaan"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>{{ count($selectedCompanyIds) ? count($selectedCompanyIds).' dipilih' : 'Semua perusahaan' }}</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><input type="search" class="form-control form-control-sm mb-2" placeholder="Cari perusahaan..." data-dropdown-search><div class="workspace-dropdown-options">@forelse($summaryCompanies as $company)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="companies[]" value="{{ $company->id }}" data-dropdown-item data-dropdown-value="{{ $company->name }}" @checked(in_array((string) $company->id, $selectedCompanyIds, true))><span><strong>{{ $company->name }}</strong><small>{{ $company->code }}</small></span></label>@empty<div class="text-secondary small p-2">Tidak ada perusahaan dalam scope akses.</div>@endforelse</div></div></div><label class="form-label">Perusahaan</label></div></div>
@else
<div class="col-md-4 col-xl-3"><div class="workspace-floating-field"><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-summary="count" data-dropdown-empty-label="Semua jenis"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>{{ count($selectedTypes) ? count($selectedTypes).' dipilih' : 'Semua jenis' }}</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><div class="workspace-dropdown-options">@foreach($typeOptions as $key => $label)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="types[]" value="{{ $key }}" data-dropdown-item data-dropdown-value="{{ $label }}" @checked(in_array($key, $selectedTypes, true))><span>{{ $label }}</span></label>@endforeach</div></div></div><label class="form-label">Jenis pengajuan</label></div></div>
@endif
<div class="col-md-4 col-xl-3"><div class="workspace-floating-field"><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-summary="count" data-dropdown-empty-label="Semua status"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>{{ count($selectedStatuses) ? count($selectedStatuses).' dipilih' : 'Semua status' }}</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><input type="search" class="form-control form-control-sm mb-2" placeholder="Cari status..." data-dropdown-search><div class="workspace-dropdown-options">@foreach($statusOptions as $key => $label)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="statuses[]" value="{{ $key }}" data-dropdown-item data-dropdown-value="{{ $label }}" @checked(in_array($key, $selectedStatuses, true))><span>{{ $label }}</span></label>@endforeach</div></div></div><label class="form-label">Status</label></div></div>
<div class="col-md-4 col-xl-3"><div class="workspace-floating-field"><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-summary="count" data-dropdown-empty-label="Semua kategori"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>{{ count($selectedCategories) ? count($selectedCategories).' dipilih' : 'Semua kategori' }}</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><input type="search" class="form-control form-control-sm mb-2" placeholder="Cari kategori..." data-dropdown-search><div class="workspace-dropdown-options">@foreach($categoryOptions as $categoryOption)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="categories[]" value="{{ $categoryOption }}" data-dropdown-item data-dropdown-value="{{ $categoryOption }}" @checked(in_array($categoryOption, $selectedCategories, true))><span>{{ $categoryOption }}</span></label>@endforeach</div></div></div><label class="form-label">Kategori</label></div></div>
<div class="col-md-3 col-xl-2 claim-summary-filter-period"><div class="workspace-floating-field"><input type="date" name="from" value="{{ $periodFrom }}" class="form-control" data-workspace-date-enhanced="true"><label class="form-label">Periode dari</label></div></div><div class="col-md-3 col-xl-2 claim-summary-filter-period"><div class="workspace-floating-field"><input type="date" name="to" value="{{ $periodTo }}" class="form-control" data-workspace-date-enhanced="true"><label class="form-label">Periode sampai</label></div></div>
@if($summaryView)<div class="col-md-3 col-xl-2 claim-summary-filter-amount"><div class="workspace-floating-field"><input type="number" name="min_amount" min="0" step="0.01" value="{{ $minimumAmount ?? '' }}" class="form-control" placeholder="0"><label class="form-label">Nominal minimum</label></div></div><div class="col-md-3 col-xl-2 claim-summary-filter-amount"><div class="workspace-floating-field"><input type="number" name="max_amount" min="0" step="0.01" value="{{ $maximumAmount ?? '' }}" class="form-control" placeholder="Tanpa batas"><label class="form-label">Nominal maksimum</label></div></div>@endif
<div class="col-md-2 col-xl-1"><div class="workspace-floating-field"><select name="limit" class="form-select">@foreach([10,25,50,100] as $limit)<option value="{{ $limit }}" @selected($perPage === $limit)>{{ $limit }}</option>@endforeach</select><label class="form-label">Baris</label></div></div><div class="col-md-4 col-xl-3"><div class="workspace-filter-actions"><a href="{{ route('workspace.hris.claims', $summaryView ? ['view' => 'summary', 'type' => $activeTabType] : []) }}" class="btn btn-sm btn-outline-secondary">Reset</a><button class="btn btn-sm btn-primary" type="submit">Terapkan</button></div></div>
</div></form>
@if($summaryView) @include('modules.partials.hris-claim-summary-list') @endif
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Karyawan</th><th>Jenis</th><th>Kategori</th><th><a class="workspace-sort-link" href="{{ request()->fullUrlWithQuery(['sort' => 'amount', 'direction' => $sort === 'amount' && $direction === 'asc' ? 'desc' : 'asc']) }}">Nominal</a></th><th>Status</th><th class="text-end">Aksi</th></tr></thead><tbody>@forelse($items as $item)<tr><td class="fw-semibold">{{ $item->employee?->full_name ?? '-' }}</td><td>{{ $item->type === 'cash_advance' ? 'Cash Advance' : 'Reimbursement' }}</td><td><div class="d-flex flex-wrap gap-1">@php($claimCategories = $item->expenseItems->pluck('category')->filter()->unique()->values()) @forelse($claimCategories as $claimCategory)<span class="badge rounded-pill text-bg-light border">{{ $claimCategory }}</span>@empty<span>{{ $item->category ?: '—' }}</span>@endforelse</div></td><td>{{ $item->currency }} {{ number_format((float) $item->amount, 2, ',', '.') }}</td><td><span class="badge text-bg-{{ in_array($item->status, ['approved','completed','paid'], true) ? 'success' : ($item->status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->status_label }}</span></td><td class="text-end"><div class="d-flex justify-content-end flex-wrap gap-1">@can('view', $item)<a class="btn btn-sm btn-outline-secondary" href="{{ route('workspace.hris.claims.show', $item) }}">Detail</a>@endcan @if($item->status === 'needs_revision' && auth()->user()->can('update', $item))<a class="btn btn-sm btn-primary" href="{{ route('workspace.hris.claims.edit', $item) }}">Revisi</a>@endif @if($item->status === 'approved' && (auth()->user()->hasRole('super_admin') || auth()->user()->can('process_hris_payment')))<form method="POST" action="{{ route('workspace.hris.claims.payment.start', $item->id) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-primary">Mulai pembayaran</button></form>@elseif($item->status === 'pending_payment' && (auth()->user()->hasRole('super_admin') || auth()->user()->can('process_hris_payment')))<a class="btn btn-sm btn-outline-primary" href="{{ route('workspace.hris.claims.payment', $item->id) }}">Proses pembayaran</a>@elseif($item->status === 'paid' && (auth()->user()->hasRole('super_admin') || auth()->user()->can('process_hris_payment')))<form method="POST" action="{{ route('workspace.hris.claims.complete', $item->id) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-success">Tandai selesai</button></form>@endif</div></td></tr>@empty<tr><td colspan="6" class="text-center text-secondary py-5">Belum ada pengajuan.</td></tr>@endforelse</tbody></table></div><div class="card-footer bg-white workspace-list-pagination"><div class="workspace-pagination-summary">Menampilkan <strong>{{ $items->firstItem() ?? 0 }}</strong>–<strong>{{ $items->lastItem() ?? 0 }}</strong> dari <strong>{{ $items->total() }}</strong> pengajuan</div>{{ $items->onEachSide(1)->links() }}</div></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    let activeClaimAction = null;
    const closeClaimAction = (restoreFocus = false) => {
        if (!activeClaimAction) return;
        activeClaimAction.menu.classList.remove('show');
        activeClaimAction.toggle.setAttribute('aria-expanded', 'false');
        if (restoreFocus) activeClaimAction.toggle.focus();
        activeClaimAction = null;
    };
    const placeClaimAction = () => {
        if (!activeClaimAction) return;
        const { menu, toggle } = activeClaimAction;
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
    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-claim-action-toggle]');
        if (toggle) {
            event.preventDefault();
            if (activeClaimAction?.toggle === toggle) {
                closeClaimAction();
                return;
            }
            closeClaimAction();
            const menu = document.getElementById(toggle.dataset.menuId);
            if (!menu) return;
            document.body.append(menu);
            menu.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
            activeClaimAction = { toggle, menu };
            placeClaimAction();
            return;
        }
        if (activeClaimAction?.menu.contains(event.target)) {
            if (event.target.closest('a')) closeClaimAction();
            return;
        }
        closeClaimAction();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeClaimAction(true);
    });
    window.addEventListener('resize', placeClaimAction);
    window.addEventListener('scroll', placeClaimAction, true);

    const drafts = [];
    const printActions = [];
    @if(! $summaryView)
    @foreach($items->getCollection() as $claimRow)
        @if($claimRow->type === 'reimbursement' && $claimRow->finance_approved_at && in_array($claimRow->status, ['approved', 'pending_payment', 'paid', 'completed'], true) && auth()->user()->can('view', $claimRow))
            printActions.push({ id: @json($claimRow->id), url: @json(route('workspace.hris.claims.print', $claimRow)) });
        @endif
    @endforeach
    @endif
    printActions.forEach((entry) => {
        const row = [...document.querySelectorAll('table tbody tr')].find((candidate) => candidate.querySelector('a[href$="/' + entry.id + '"]'));
        if (!row || row.querySelector('[data-claim-print]')) return;
        const action = row.lastElementChild;
        if (!action) return;
        const link = document.createElement('a');
        link.className = 'btn btn-sm btn-outline-primary';
        link.href = entry.url;
        link.target = '_blank';
        link.rel = 'noopener';
        link.textContent = 'Cetak';
        link.dataset.claimPrint = 'true';
        action.querySelector('div')?.append(link);
    });
    @foreach($items->getCollection()->where('status', 'draft') as $draft)
        drafts.push({ edit: @json(route('workspace.hris.claims.edit', $draft)), delete: @json(route('workspace.hris.claims.destroy', $draft)) });
    @endforeach
    [...document.querySelectorAll('table tbody tr')].filter((row) => row.querySelector('td:nth-child(5)')?.textContent.trim().toLowerCase().includes('draft')).forEach((row, index) => {
        const action = row.lastElementChild;
        const draft = drafts[index];
        if (!action || !draft || action.dataset.draftActions) return;
        action.dataset.draftActions = 'true';
        const actionContainer = action.querySelector('div') || action;
        actionContainer.insertAdjacentHTML('beforeend', `<a href="${draft.edit}" class="btn btn-sm btn-outline-primary">Edit draft</a><form method="POST" action="${draft.delete}" class="d-inline" data-confirm="Hapus draft reimbursement ini? Draft tidak dapat dipulihkan."><input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE"><button class="btn btn-sm btn-outline-danger">Hapus draft</button></form>`);
    });
    @if(! $summaryView)
    document.querySelectorAll('.card:not(.claim-summary-table-container) table tbody tr').forEach((row, index) => {
        const actionCell = row.lastElementChild;
        const actionContainer = actionCell?.querySelector('div') || actionCell;
        if (!actionCell || actionCell.querySelector('[data-claim-action-toggle]') || !actionContainer) return;
        const controls = [...actionContainer.children];
        if (!controls.length) return;

        const menuId = `claim-list-action-menu-${index}`;
        const toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'btn claim-summary-action-toggle';
        toggle.dataset.claimActionToggle = '';
        toggle.dataset.menuId = menuId;
        toggle.setAttribute('aria-controls', menuId);
        toggle.setAttribute('aria-haspopup', 'true');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Aksi pengajuan');
        toggle.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="5" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="19" r="1.8"/></svg>';

        const menu = document.createElement('div');
        menu.id = menuId;
        menu.className = 'dropdown-menu dropdown-menu-end claim-summary-action-menu';
        menu.setAttribute('role', 'menu');
        const heading = document.createElement('div');
        heading.className = 'claim-summary-menu-heading';
        heading.textContent = 'AKSI PENGAJUAN';
        menu.append(heading);

        controls.forEach((control) => {
            const target = control.matches('form') ? control.querySelector('button[type="submit"],button:not([type])') : control;
            if (!target) return;
            const label = target.textContent.trim();
            const icon = document.createElement('span');
            icon.className = `claim-summary-menu-icon${/pembayaran|cetak|selesai/i.test(label) ? ' print' : ''}`;
            icon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 5h16v14H4zM8 9h8M8 13h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            const text = document.createElement('span');
            const strong = document.createElement('strong');
            strong.textContent = label;
            text.append(strong);
            if (control.matches('a')) {
                control.className = 'dropdown-item';
                control.setAttribute('role', 'menuitem');
                control.replaceChildren(icon, text);
                menu.append(control);
            } else {
                control.className = 'claim-summary-menu-form';
                target.className = 'dropdown-item';
                target.replaceChildren(icon, text);
                menu.append(control);
            }
        });

        actionContainer.replaceChildren(toggle, menu);
        actionContainer.className = 'd-flex justify-content-end';
    });
    @endif
});
</script>
@endsection
