@extends('layouts.app', ['title' => 'Reimbursement dan Cash Advance'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><div class="text-uppercase text-secondary small fw-semibold mb-2">Claim</div><h1 class="h3 mb-1">{{ $summaryView ? ($activeTabType === 'reimbursement' ? 'Ringkasan Reimbursement' : 'Ringkasan Cash Advance') : 'Reimbursement & Cash Advance' }}</h1><p class="text-secondary mb-0">{{ $summaryView ? 'Daftar pengajuan dalam scope perusahaan Anda.' : 'Satu daftar pengajuan dengan workflow dan formulir yang berbeda.' }}</p></div>@if(! $summaryView)<div class="dropdown claims-create-dropdown"><button class="btn claims-create-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="claims-create-plus" aria-hidden="true">+</span><span>Buat Pengajuan</span><svg viewBox="0 0 16 16" aria-hidden="true"><path d="m3.5 6 4.5 4 4.5-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></button><ul class="dropdown-menu dropdown-menu-end claims-create-menu"><li class="claims-create-heading">PILIH JENIS PENGAJUAN</li><li><a class="dropdown-item claims-create-option" href="{{ route('workspace.hris.claims.create', ['type' => 'reimbursement']) }}"><span class="claims-create-icon reimbursement" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 7.5h16v11H4zM4 9l8 5 8-5M8 4h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="claims-create-copy"><strong>Reimbursement</strong><small>Penggantian biaya yang sudah dibayarkan</small></span><span class="claims-create-arrow" aria-hidden="true">›</span></a></li><li><a class="dropdown-item claims-create-option" href="{{ route('workspace.hris.claims.create', ['type' => 'cash_advance']) }}"><span class="claims-create-icon cash-advance" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v18m5-13.5C17 5.6 14.9 4.5 12 4.5s-5 1.1-5 3 1.7 2.7 5 3.5 5 1.7 5 3.5-2 3-5 3-5-1.2-5-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="claims-create-copy"><strong>Cash Advance</strong><small>Dana awal untuk kebutuhan pekerjaan</small></span><span class="claims-create-arrow" aria-hidden="true">›</span></a></li></ul></div>@endif</div>
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
.workspace-claim-summary-filter{container-type:inline-size;border:1px solid #e7edf5!important;border-radius:.9rem!important;background:linear-gradient(120deg,#fff,#fbfcff)!important}.workspace-claim-summary-filter>.row{display:grid;grid-template-columns:minmax(130px,1.2fr) minmax(130px,1.2fr) minmax(130px,1.2fr) minmax(120px,1fr) minmax(120px,1fr) minmax(75px,.6fr) minmax(145px,auto);gap:.65rem .75rem;align-items:end;margin:0;--bs-gutter-x:0;--bs-gutter-y:0}.workspace-claim-summary-filter>.row>[class*="col-"]{width:auto;max-width:none;min-width:0;flex:none}.workspace-claim-summary-filter .workspace-filter-actions{display:flex;width:100%;justify-content:flex-start;gap:.5rem}.workspace-claim-summary-filter .workspace-filter-actions .btn{min-width:76px;margin:0}.workspace-claim-summary-filter .workspace-floating-field .form-control,.workspace-claim-summary-filter .workspace-floating-field .form-select{min-width:0}
.workspace-claim-summary-filter .workspace-filter-actions{gap:0}.workspace-claim-summary-filter .workspace-filter-actions .btn:first-child{border-radius:.45rem 0 0 .45rem}.workspace-claim-summary-filter .workspace-filter-actions .btn:last-child{margin-left:-1px;border-radius:0 .45rem .45rem 0}
.workspace-claim-summary-filter>.row{grid-template-columns:repeat(12,minmax(0,1fr))}.workspace-claim-summary-filter>.row>:nth-child(3){grid-column:1/span 4;grid-row:1}.workspace-claim-summary-filter>.row>:nth-child(4){grid-column:5/span 2;grid-row:1}.workspace-claim-summary-filter>.row>:nth-child(5){grid-column:7/span 2;grid-row:1}.workspace-claim-summary-filter>.row>:nth-child(6){grid-column:9/span 2;grid-row:1}.workspace-claim-summary-filter>.row>:nth-child(11){grid-column:11/span 2;grid-row:1}.workspace-claim-summary-filter>.row>:nth-child(7){grid-column:1/span 2;grid-row:2}.workspace-claim-summary-filter>.row>:nth-child(8){grid-column:3/span 2;grid-row:2}.workspace-claim-summary-filter>.row>:nth-child(9){grid-column:5/span 2;grid-row:2}.workspace-claim-summary-filter>.row>:nth-child(10){grid-column:7/span 2;grid-row:2}.workspace-claim-summary-filter>.row>:nth-child(12){grid-column:9/span 2;grid-row:2}
@media(max-width:1100px){.workspace-claim-summary-filter>.row{grid-template-columns:repeat(6,minmax(0,1fr))}.workspace-claim-summary-filter>.row>:nth-child(n+3){grid-row:auto}.workspace-claim-summary-filter>.row>:nth-child(3){grid-column:span 3}.workspace-claim-summary-filter>.row>:nth-child(4),.workspace-claim-summary-filter>.row>:nth-child(5),.workspace-claim-summary-filter>.row>:nth-child(6),.workspace-claim-summary-filter>.row>:nth-child(7),.workspace-claim-summary-filter>.row>:nth-child(8),.workspace-claim-summary-filter>.row>:nth-child(9),.workspace-claim-summary-filter>.row>:nth-child(10),.workspace-claim-summary-filter>.row>:nth-child(11){grid-column:span 2}.workspace-claim-summary-filter>.row>.col-md-4:last-child{grid-column:1/-1}}
@media(max-width:767.98px){.workspace-claim-summary-filter>.row{grid-template-columns:repeat(2,minmax(0,1fr))}.workspace-claim-summary-filter>.row>:nth-child(3),.workspace-claim-summary-filter>.row>:nth-child(12){grid-column:span 2}.workspace-claim-summary-filter>.row>:nth-child(4),.workspace-claim-summary-filter>.row>:nth-child(5),.workspace-claim-summary-filter>.row>:nth-child(6),.workspace-claim-summary-filter>.row>:nth-child(7),.workspace-claim-summary-filter>.row>:nth-child(8),.workspace-claim-summary-filter>.row>:nth-child(9),.workspace-claim-summary-filter>.row>:nth-child(10),.workspace-claim-summary-filter>.row>:nth-child(11){grid-column:span 1}}
@media(max-width:480px){.workspace-claim-summary-filter>.row{grid-template-columns:minmax(0,1fr)}.workspace-claim-summary-filter>.row>:nth-child(3),.workspace-claim-summary-filter>.row>:nth-child(12){grid-column:span 1}}
@container (max-width:1100px){.workspace-claim-summary-filter>.row{grid-template-columns:repeat(8,minmax(0,1fr))}.workspace-claim-summary-filter>.row>:nth-child(3){grid-column:span 8!important;grid-row:1!important}.workspace-claim-summary-filter>.row>:nth-child(4){grid-column:span 4!important;grid-row:2!important}.workspace-claim-summary-filter>.row>:nth-child(5){grid-column:span 4!important;grid-row:2!important}.workspace-claim-summary-filter>.row>:nth-child(6){grid-column:span 4!important;grid-row:3!important}.workspace-claim-summary-filter>.row>:nth-child(7){grid-column:span 2!important;grid-row:3!important}.workspace-claim-summary-filter>.row>:nth-child(8){grid-column:span 2!important;grid-row:3!important}.workspace-claim-summary-filter>.row>:nth-child(11){grid-column:1/span 2!important;grid-row:4!important}.workspace-claim-summary-filter>.row>:nth-child(9){grid-column:3/span 2!important;grid-row:4!important}.workspace-claim-summary-filter>.row>:nth-child(10){grid-column:5/span 2!important;grid-row:4!important}.workspace-claim-summary-filter>.row>:nth-child(12){grid-column:7/span 2!important;grid-row:4!important}}
@container (max-width:640px){.workspace-claim-summary-filter>.row{grid-template-columns:repeat(4,minmax(0,1fr))}.workspace-claim-summary-filter>.row>:nth-child(3){grid-column:span 4!important;grid-row:1!important}.workspace-claim-summary-filter>.row>:nth-child(4){grid-column:span 2!important;grid-row:2!important}.workspace-claim-summary-filter>.row>:nth-child(5){grid-column:span 2!important;grid-row:2!important}.workspace-claim-summary-filter>.row>:nth-child(6){grid-column:span 2!important;grid-row:3!important}.workspace-claim-summary-filter>.row>:nth-child(11){grid-column:span 2!important;grid-row:3!important}.workspace-claim-summary-filter>.row>:nth-child(7){grid-column:span 2!important;grid-row:4!important}.workspace-claim-summary-filter>.row>:nth-child(8){grid-column:span 2!important;grid-row:4!important}.workspace-claim-summary-filter>.row>:nth-child(9){grid-column:span 4!important;grid-row:5!important}.workspace-claim-summary-filter>.row>:nth-child(10){grid-column:span 2!important;grid-row:6!important}.workspace-claim-summary-filter>.row>:nth-child(12){grid-column:span 2!important;grid-row:6!important}}
.claim-summary-action-toggle{display:inline-grid;width:32px;height:32px;padding:0;place-items:center;border:1px solid #dce5f0;border-radius:.6rem;background:linear-gradient(145deg,#fff,#f5f8fc);color:#526178;box-shadow:0 2px 5px rgba(15,23,42,.04);transition:all .18s ease}.claim-summary-action-toggle:hover,.claim-summary-action-toggle[aria-expanded="true"]{border-color:#93c5fd;background:linear-gradient(145deg,#eff6ff,#f5f3ff);color:#1769aa;box-shadow:0 5px 14px rgba(37,99,235,.14)}.claim-summary-action-toggle svg{display:block;width:17px;height:17px}
.claim-summary-action-menu{position:fixed!important;z-index:1090;min-width:210px;width:min(220px,calc(100vw - 16px));margin:0!important;padding:.3rem;border:1px solid #e4eaf2;border-radius:.75rem;background:rgba(255,255,255,.99);box-shadow:0 14px 32px rgba(15,23,42,.18);animation:claimMenuIn .14s ease-out}.claim-summary-menu-heading{padding:.35rem .55rem .4rem;color:#8491a5;font-size:.6rem;font-weight:800;letter-spacing:.1em}.claim-summary-action-menu .dropdown-item{display:flex;align-items:center;gap:.55rem;padding:.45rem .55rem;border-radius:.55rem;color:#263449;white-space:normal;transition:background .15s ease,color .15s ease}.claim-summary-action-menu .dropdown-item:hover{background:linear-gradient(100deg,#eff6ff,#f5f3ff);color:#075da5}.claim-summary-action-menu .dropdown-item>span:last-child{display:grid;gap:.08rem}.claim-summary-action-menu .dropdown-item strong{font-size:.78rem;font-weight:700}.claim-summary-action-menu .dropdown-item small{color:#7b8798;font-size:.67rem}.claim-summary-menu-icon{display:grid;width:1.75rem;height:1.75rem;flex:0 0 1.75rem;place-items:center;border:1px solid #dbeafe;border-radius:.55rem;background:linear-gradient(145deg,#eff6ff,#f5f3ff);color:#2563eb}.claim-summary-menu-icon.print{border-color:#d1fae5;background:linear-gradient(145deg,#ecfdf5,#eff6ff);color:#07845b}.claim-summary-menu-icon svg{width:.95rem;height:.95rem}@keyframes claimMenuIn{from{opacity:0;transform:translateY(-4px) scale(.98)}to{opacity:1;transform:translateY(0) scale(1)}}
</style>
<style>
.claim-summary-hero{display:none}
.claim-summary-metrics{border-color:#cfe3ff;background:linear-gradient(110deg,#eaf4ff 0%,#fff 48%,#f1efff 100%)}
</style>
<section class="claim-summary-hero mb-3"><div class="d-flex flex-wrap align-items-center justify-content-between gap-3"><div><div class="claim-summary-kicker">Ikhtisar pengajuan · {{ $periodFrom === $periodTo ? \Illuminate\Support\Carbon::parse($periodFrom)->translatedFormat('d M Y') : \Illuminate\Support\Carbon::parse($periodFrom)->translatedFormat('d M Y').' – '.\Illuminate\Support\Carbon::parse($periodTo)->translatedFormat('d M Y') }}</div><h2 class="h5 mt-1 mb-1">{{ $activeTabType === 'reimbursement' ? 'Aktivitas reimbursement' : 'Aktivitas cash advance' }}</h2><div class="small text-secondary">Data dibatasi pada perusahaan yang dapat Anda akses. Pengajuan draft tidak termasuk.</div></div><div class="d-flex flex-wrap gap-2">@foreach($items->getCollection()->pluck('employee.company.name')->filter()->unique()->take(3) as $companyName)<span class="claim-summary-company"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 21V5.5L12 2l8 3.5V21M2 21h20M8 8h1m6 0h1M8 12h1m6 0h1M10 21v-4h4v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>{{ $companyName }}</span>@endforeach</div></div></section>
<section class="workspace-mobile-panel workspace-mobile-kpi-panel mb-3" data-mobile-panel>
<button type="button" class="workspace-mobile-panel-toggle" data-mobile-toggle aria-expanded="false" aria-controls="claim-summary-kpis">Ringkasan {{ $activeTabType === 'reimbursement' ? 'reimbursement' : 'cash advance' }} <span data-mobile-toggle-icon>+</span></button>
<div class="workspace-mobile-panel-content" id="claim-summary-kpis">
<div class="workspace-overtime-summary-panel workspace-overtime-summary-panel--four workspace-mobile-kpi-panel-grid" aria-label="Metrik pengajuan">
<div class="workspace-overtime-summary-card"><span>Total pengajuan</span><strong>{{ number_format($summaryMetrics['count'], 0, ',', '.') }}</strong><small>Pengajuan dalam filter ringkasan</small></div>
<div class="workspace-overtime-summary-card"><span>Total nominal</span><strong class="workspace-claim-summary-amount">@forelse($summaryMetrics['amounts'] as $amount)<span class="d-block">{{ $amount->currency }} {{ number_format((float) $amount->total, 0, ',', '.') }}</span>@empty<span>—</span>@endforelse</strong></div>
<div class="workspace-overtime-summary-card is-pending"><span>Dalam proses</span><strong>{{ number_format($summaryMetrics['in_progress'], 0, ',', '.') }}</strong><small>Pengajuan menunggu proses</small></div>
<div class="workspace-overtime-summary-card is-approved"><span>{{ $activeTabType === 'reimbursement' ? 'Disetujui' : 'Selesai' }}</span><strong>{{ number_format($summaryMetrics['completed'], 0, ',', '.') }}</strong><small>{{ $activeTabType === 'reimbursement' ? 'Pengajuan telah disetujui' : 'Pertanggungjawaban selesai' }}</small></div>
</div>
</section>
@endif
<style>@unless($summaryView)
.claim-summary-action-toggle{display:inline-grid;width:32px;height:32px;padding:0;place-items:center;border:1px solid #dce5f0;border-radius:.6rem;background:linear-gradient(145deg,#fff,#f5f8fc);color:#526178;box-shadow:0 2px 5px rgba(15,23,42,.04);transition:all .18s ease}.claim-summary-action-toggle:hover,.claim-summary-action-toggle[aria-expanded="true"]{border-color:#93c5fd;background:linear-gradient(145deg,#eff6ff,#f5f3ff);color:#1769aa;box-shadow:0 5px 14px rgba(37,99,235,.14)}.claim-summary-action-toggle svg{display:block;width:17px;height:17px}
.claim-summary-action-menu{position:fixed!important;z-index:1090;min-width:210px;width:min(220px,calc(100vw - 16px));margin:0!important;padding:.3rem;border:1px solid #e4eaf2;border-radius:.75rem;background:rgba(255,255,255,.99);box-shadow:0 14px 32px rgba(15,23,42,.18);animation:claimMenuIn .14s ease-out}.claim-summary-menu-heading{padding:.35rem .55rem .4rem;color:#8491a5;font-size:.6rem;font-weight:800;letter-spacing:.1em}.claim-summary-action-menu .dropdown-item{display:flex;align-items:center;gap:.55rem;padding:.45rem .55rem;border:0;border-radius:.55rem;background:transparent;color:#263449;text-align:left;white-space:normal;transition:background .15s ease,color .15s ease}.claim-summary-action-menu .dropdown-item:hover{background:linear-gradient(100deg,#eff6ff,#f5f3ff);color:#075da5}.claim-summary-action-menu .dropdown-item>span:last-child{display:grid;gap:.08rem}.claim-summary-action-menu .dropdown-item strong{font-size:.78rem;font-weight:700}.claim-summary-action-menu .dropdown-item small{color:#7b8798;font-size:.67rem}.claim-summary-menu-icon{display:grid;width:1.75rem;height:1.75rem;flex:0 0 1.75rem;place-items:center;border:1px solid #dbeafe;border-radius:.55rem;background:linear-gradient(145deg,#eff6ff,#f5f3ff);color:#2563eb}.claim-summary-menu-icon.print{border-color:#d1fae5;background:linear-gradient(145deg,#ecfdf5,#eff6ff);color:#07845b}.claim-summary-menu-icon svg{width:.95rem;height:.95rem}.claim-summary-menu-form{margin:0}.claim-summary-menu-form .dropdown-item{width:100%}
.claims-create-trigger{display:inline-flex;align-items:center;gap:.55rem;padding:.65rem .9rem;border:1px solid #075da5;border-radius:.7rem;background:linear-gradient(120deg,#0877c9,#1558b0 65%,#5a55d6);color:#fff;font-weight:700;box-shadow:0 5px 14px rgba(18,102,190,.2);transition:transform .18s ease,box-shadow .18s ease,filter .18s ease}.claims-create-trigger:hover,.claims-create-trigger[aria-expanded="true"]{transform:translateY(-1px);filter:saturate(1.12);color:#fff;box-shadow:0 8px 20px rgba(18,102,190,.28)}.claims-create-trigger:focus-visible{outline:3px solid rgba(59,130,246,.25);outline-offset:2px}.claims-create-trigger>svg{width:14px;height:14px;opacity:.85;transition:transform .18s ease}.claims-create-trigger[aria-expanded="true"]>svg{transform:rotate(180deg)}.claims-create-plus{display:grid;width:1.35rem;height:1.35rem;place-items:center;border:1px solid rgba(255,255,255,.35);border-radius:.45rem;background:rgba(255,255,255,.15);font-size:1.1rem;font-weight:500;line-height:1}.claims-create-menu{width:min(340px,calc(100vw - 24px));margin-top:.55rem!important;padding:.45rem;border:1px solid #e2eaf4;border-radius:.9rem;background:rgba(255,255,255,.98);box-shadow:0 18px 45px rgba(21,45,78,.18);backdrop-filter:blur(12px);animation:claimMenuIn .15s ease-out}.claims-create-heading{padding:.45rem .65rem .5rem;color:#8491a5;font-size:.62rem;font-weight:800;letter-spacing:.1em}.claims-create-option{display:flex;align-items:center;gap:.7rem;padding:.65rem;border:1px solid transparent;border-radius:.7rem;color:#263449;white-space:normal;transition:background .16s ease,border-color .16s ease,transform .16s ease}.claims-create-option:hover,.claims-create-option:focus{transform:translateX(2px);border-color:#dbeafe;background:linear-gradient(105deg,#eff6ff,#f7f5ff);color:#164e8a}.claims-create-icon{display:grid;width:2.4rem;height:2.4rem;flex:0 0 2.4rem;place-items:center;border:1px solid #bfdbfe;border-radius:.75rem;background:linear-gradient(145deg,#eaf4ff,#dbeafe);color:#1671c5}.claims-create-icon.cash-advance{border-color:#c4b5fd;background:linear-gradient(145deg,#f2edff,#e7e4ff);color:#6955cb}.claims-create-icon svg{width:1.2rem;height:1.2rem}.claims-create-copy{display:grid;min-width:0;gap:.15rem}.claims-create-copy strong{font-size:.84rem;font-weight:750}.claims-create-copy small{color:#77859a;font-size:.7rem;line-height:1.35}.claims-create-arrow{margin-left:auto;color:#9aa8ba;font-size:1.25rem;line-height:1;transition:transform .16s ease,color .16s ease}.claims-create-option:hover .claims-create-arrow{transform:translateX(2px);color:#1671c5}@media(max-width:575.98px){.claims-create-menu{width:min(320px,calc(100vw - 24px))}.claims-create-trigger{padding:.58rem .75rem}}
.workspace-claim-list-filter + .card .table-responsive>table{width:100%!important;min-width:900px;table-layout:fixed!important}.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(1){width:19%!important}.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(2){width:15%!important}.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(3){width:24%!important}.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(4){width:17%!important;white-space:nowrap}.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(5){width:15%!important}.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(6){width:10%!important}.workspace-claim-list-filter + .card .table-responsive>table td:nth-child(4){white-space:nowrap}.workspace-claim-list-filter + .card .table-responsive>table td:nth-child(5) .badge{max-width:100%;white-space:normal;line-height:1.2}.workspace-claim-list-filter + .card .table-responsive>table td:last-child{white-space:nowrap}
@endunless</style>
<style>
.workspace-claim-list-filter + .card .table-responsive>table{min-width:1050px!important}
.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(1){width:18%!important}
.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(2){width:14%!important}
.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(3){width:22%!important}
.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(4){width:17%!important}
.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(5){width:22%!important}
.workspace-claim-list-filter + .card .table-responsive>table thead th:nth-child(6){width:7%!important}
.workspace-claim-list-filter + .card .table-responsive>table td:nth-child(5){white-space:nowrap}
.workspace-claim-list-filter + .card .table-responsive>table td:nth-child(5) .badge{max-width:none;white-space:nowrap}
</style>
<style>
.claim-summary-menu-icon.edit{border-color:#ddd6fe;background:linear-gradient(145deg,#f5f3ff,#ede9fe);color:#7047c5}
.claim-summary-menu-icon.delete{border-color:#fecdd3;background:linear-gradient(145deg,#fff1f2,#ffe4e6);color:#d12e45}
</style>
@unless($summaryView)<style>
.workspace-claim-list-filter + .card .table-responsive>table:not(.claim-summary-table){min-width:1200px!important}
.workspace-claim-list-filter + .card .table-responsive>table:not(.claim-summary-table) thead th:nth-child(1){width:14%!important}
.workspace-claim-list-filter + .card .table-responsive>table:not(.claim-summary-table) thead th:nth-child(2){width:13%!important}
.workspace-claim-list-filter + .card .table-responsive>table:not(.claim-summary-table) thead th:nth-child(3){width:11%!important}
.workspace-claim-list-filter + .card .table-responsive>table:not(.claim-summary-table) thead th:nth-child(4){width:17%!important}
.workspace-claim-list-filter + .card .table-responsive>table:not(.claim-summary-table) thead th:nth-child(5){width:13%!important}
.workspace-claim-list-filter + .card .table-responsive>table:not(.claim-summary-table) thead th:nth-child(6){width:12%!important}
.workspace-claim-list-filter + .card .table-responsive>table:not(.claim-summary-table) thead th:nth-child(7){width:12%!important}
.workspace-claim-list-filter + .card .table-responsive>table:not(.claim-summary-table) thead th:nth-child(8){width:8%!important}
.workspace-claim-list-filter + .card .table-responsive>table:not(.claim-summary-table) tbody td:first-child code{overflow-wrap:anywhere}
</style>@endunless
@if($summaryView && $activeTabType === 'reimbursement')
<style>
@container (min-width:1101px){.workspace-claim-summary-filter.workspace-claim-has-payment-filter>.row>:nth-child(3){grid-column:1/span 4!important;grid-row:1!important}.workspace-claim-summary-filter.workspace-claim-has-payment-filter>.row>:nth-child(4){grid-column:5/span 2!important;grid-row:1!important}.workspace-claim-summary-filter.workspace-claim-has-payment-filter>.row>:nth-child(5){grid-column:7/span 2!important;grid-row:1!important}.workspace-claim-summary-filter.workspace-claim-has-payment-filter>.row>:nth-child(6){grid-column:9/span 2!important;grid-row:1!important}.workspace-claim-summary-filter.workspace-claim-has-payment-filter>.row>:nth-child(7){grid-column:11/span 2!important;grid-row:1!important}.workspace-claim-summary-filter.workspace-claim-has-payment-filter>.row>:nth-child(8){grid-column:1/span 2!important;grid-row:2!important}.workspace-claim-summary-filter.workspace-claim-has-payment-filter>.row>:nth-child(9){grid-column:3/span 2!important;grid-row:2!important}.workspace-claim-summary-filter.workspace-claim-has-payment-filter>.row>:nth-child(10){grid-column:5/span 2!important;grid-row:2!important}.workspace-claim-summary-filter.workspace-claim-has-payment-filter>.row>:nth-child(11){grid-column:7/span 2!important;grid-row:2!important}.workspace-claim-summary-filter.workspace-claim-has-payment-filter>.row>:nth-child(12){grid-column:9/span 1!important;grid-row:2!important}.workspace-claim-summary-filter.workspace-claim-has-payment-filter>.row>:nth-child(13){grid-column:10/span 3!important;grid-row:2!important}}
</style>
@endif
@unless($summaryView)<div class="workspace-tab-nav mb-3"><a class="{{ $activeTabType === null ? 'active' : '' }}" href="{{ route('workspace.hris.claims') }}">Semua</a><a class="{{ $activeTabType === 'reimbursement' ? 'active' : '' }}" href="{{ route('workspace.hris.claims', ['type' => 'reimbursement']) }}">Reimbursement</a><a class="{{ $activeTabType === 'cash_advance' ? 'active' : '' }}" href="{{ route('workspace.hris.claims', ['type' => 'cash_advance']) }}">Cash Advance</a></div>@endunless
@if(! $summaryView && $activeTabType === 'reimbursement')
<style>
.claim-list-kpi-panel{margin-bottom:.8rem}
.claim-list-reimbursement-kpis{background:linear-gradient(108deg,#e9f3ff 0%,#f8fbff 32%,#fff 53%,#f5f7ff 76%,#eeeaff 100%);border:1px solid #dfe8f5;border-radius:18px;box-shadow:0 8px 24px rgba(42,91,145,.09);display:grid;grid-template-columns:repeat(5,minmax(0,1fr));overflow:hidden;padding:8px 6px;position:relative}
.claim-list-reimbursement-kpis::before{background:linear-gradient(100deg,#328fe0 0%,#67bde0 48%,#a18be4 100%);content:"";height:3px;inset:0 0 auto;opacity:.84;position:absolute}
.claim-list-reimbursement-kpis::after{background:radial-gradient(circle at 100% 0%,rgba(172,151,237,.22) 0 54px,transparent 56px),radial-gradient(circle at 100% 0%,transparent 0 72px,rgba(116,155,226,.16) 73px 74px,transparent 75px 94px,rgba(116,155,226,.11) 95px 111px,transparent 112px),radial-gradient(ellipse at 5% 100%,rgba(75,164,220,.08),transparent 38%);content:"";inset:0;pointer-events:none;position:absolute}
.claim-list-reimbursement-kpis>.workspace-overtime-summary-card{background:transparent;border:0;border-radius:0;box-shadow:none;display:flex;flex-direction:column;gap:2px;justify-content:center;min-height:64px;padding:6px 14px;position:relative;transition:background-color .18s ease;z-index:1}
.claim-list-reimbursement-kpis>.workspace-overtime-summary-card+.workspace-overtime-summary-card{border-left:1px solid #e3e9f1}
.claim-list-reimbursement-kpis>.workspace-overtime-summary-card span{color:#617087;font-size:.74rem;font-weight:600;letter-spacing:.01em}
.claim-list-reimbursement-kpis>.workspace-overtime-summary-card strong{color:#1c2d46;font-size:1.3rem;letter-spacing:-.025em;line-height:1.15}
.claim-list-reimbursement-kpis>.workspace-overtime-summary-card small{color:#718198;font-size:.68rem;line-height:1.35}
.claim-list-reimbursement-kpis>.workspace-overtime-summary-card.is-approved strong{color:#178052}.claim-list-reimbursement-kpis>.workspace-overtime-summary-card.is-pending strong{color:#a36a0a}.claim-list-reimbursement-kpis>.workspace-overtime-summary-card.is-rejected strong{color:#bb4650}.claim-list-reimbursement-kpis>.workspace-overtime-summary-card.is-draft strong{color:#6c5bb5}
@media(max-width:991.98px){.claim-list-reimbursement-kpis{grid-template-columns:repeat(3,minmax(0,1fr))}.claim-list-reimbursement-kpis>.workspace-overtime-summary-card:nth-child(4){border-left:0}}
@media(max-width:575.98px){.claim-list-reimbursement-kpis{grid-template-columns:repeat(2,minmax(0,1fr));padding:7px}.claim-list-reimbursement-kpis>.workspace-overtime-summary-card{min-height:68px;padding:8px 10px}.claim-list-reimbursement-kpis>.workspace-overtime-summary-card:nth-child(odd){border-left:0}.claim-list-reimbursement-kpis>.workspace-overtime-summary-card:nth-child(4){border-left:1px solid rgba(105,130,164,.25)}.claim-list-reimbursement-kpis>.workspace-overtime-summary-card:nth-child(n+3){border-top:1px solid rgba(105,130,164,.25)}.claim-list-reimbursement-kpis>.workspace-overtime-summary-card:last-child{grid-column:span 2;border-left:0}.claim-list-reimbursement-kpis::after{opacity:.55}.claim-list-reimbursement-kpis>.workspace-overtime-summary-card strong{font-size:1.2rem}}
</style>
<section class="workspace-mobile-panel workspace-mobile-kpi-panel claim-list-kpi-panel" data-mobile-panel>
<button type="button" class="workspace-mobile-panel-toggle" data-mobile-toggle aria-expanded="false" aria-controls="claim-list-reimbursement-kpis">Ringkasan reimbursement <span data-mobile-toggle-icon>+</span></button>
<div class="workspace-mobile-panel-content" id="claim-list-reimbursement-kpis">
<div class="claim-list-reimbursement-kpis workspace-mobile-kpi-panel-grid" aria-label="KPI reimbursement">
<div class="workspace-overtime-summary-card"><span>Total pengajuan</span><strong>{{ number_format($claimListMetrics['total'], 0, ',', '.') }}</strong><small>pengajuan pada hasil filter</small></div>
<div class="workspace-overtime-summary-card is-approved"><span>Disetujui</span><strong>{{ number_format($claimListMetrics['approved'], 0, ',', '.') }}</strong><small>@forelse($claimListMetrics['approved_amounts'] as $amount){{ $amount->currency }} {{ number_format((float) $amount->total, 2, ',', '.') }}@if(! $loop->last) · @endif @empty Belum ada nominal disetujui @endforelse</small></div>
<div class="workspace-overtime-summary-card is-pending"><span>Dalam proses</span><strong>{{ number_format($claimListMetrics['in_progress'], 0, ',', '.') }}</strong><small>pengajuan menunggu proses</small></div>
<div class="workspace-overtime-summary-card is-rejected"><span>Ditolak</span><strong>{{ number_format($claimListMetrics['rejected'], 0, ',', '.') }}</strong><small>pengajuan ditolak</small></div>
<div class="workspace-overtime-summary-card is-draft"><span>Draft</span><strong>{{ number_format($claimListMetrics['draft'], 0, ',', '.') }}</strong><small>belum dikirim</small></div>
</div>
</div>
</section>
@endif
<form method="GET" class="card card-body border-0 shadow-sm mb-3 workspace-list-filter-form workspace-claim-list-filter {{ $summaryView ? 'workspace-claim-summary-filter' : '' }} {{ $activeTabType !== 'cash_advance' ? 'workspace-claim-has-payment-filter' : '' }}"><div class="row g-3 align-items-end">
@if($summaryView)
<input type="hidden" name="view" value="summary"><input type="hidden" name="type" value="{{ $activeTabType }}">
<div class="col-md-12 claim-summary-filter-keyword"><div class="workspace-floating-field"><input type="search" name="q" value="{{ $searchKeyword }}" class="form-control" placeholder="Nama karyawan, nomor pengajuan, status, kategori"><label class="form-label">Kata kunci</label></div></div>
<div class="col-md-4 col-xl-3"><div class="workspace-floating-field"><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-summary="count" data-dropdown-empty-label="Semua perusahaan"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>{{ count($selectedCompanyIds) ? count($selectedCompanyIds).' dipilih' : 'Semua perusahaan' }}</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><input type="search" class="form-control form-control-sm mb-2" placeholder="Cari perusahaan..." data-dropdown-search><div class="workspace-dropdown-options">@forelse($summaryCompanies as $company)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="companies[]" value="{{ $company->id }}" data-dropdown-item data-dropdown-value="{{ $company->name }}" @checked(in_array((string) $company->id, $selectedCompanyIds, true))><span><strong>{{ $company->name }}</strong><small>{{ $company->code }}</small></span></label>@empty<div class="text-secondary small p-2">Tidak ada perusahaan dalam scope akses.</div>@endforelse</div></div></div><label class="form-label">Perusahaan</label></div></div>
@else
<div class="col-md-4 col-xl-3 claim-list-filter-keyword"><div class="workspace-floating-field"><input type="search" name="q" value="{{ $searchKeyword }}" class="form-control" placeholder="Cari nama atau nomor"><label class="form-label">Nama / nomor</label></div></div>
<div class="col-md-4 col-xl-3"><div class="workspace-floating-field"><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-summary="count" data-dropdown-empty-label="Semua jenis"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>{{ count($selectedTypes) ? count($selectedTypes).' dipilih' : 'Semua jenis' }}</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><div class="workspace-dropdown-options">@foreach($typeOptions as $key => $label)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="types[]" value="{{ $key }}" data-dropdown-item data-dropdown-value="{{ $label }}" @checked(in_array($key, $selectedTypes, true))><span>{{ $label }}</span></label>@endforeach</div></div></div><label class="form-label">Jenis pengajuan</label></div></div>
@endif
<div class="col-md-4 col-xl-3"><div class="workspace-floating-field"><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-summary="count" data-dropdown-empty-label="Semua status"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>{{ count($selectedStatuses) ? count($selectedStatuses).' dipilih' : 'Semua status' }}</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><input type="search" class="form-control form-control-sm mb-2" placeholder="Cari status pengajuan..." data-dropdown-search><div class="workspace-dropdown-options">@foreach($statusOptions as $key => $label)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="statuses[]" value="{{ $key }}" data-dropdown-item data-dropdown-value="{{ $label }}" @checked(in_array($key, $selectedStatuses, true))><span>{{ $label }}</span></label>@endforeach</div></div></div><label class="form-label">Status pengajuan</label></div></div>
@if($activeTabType !== 'cash_advance')<div class="col-md-4 col-xl-3"><div class="workspace-floating-field"><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-summary="count" data-dropdown-empty-label="Semua status pembayaran"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>{{ count($selectedPaymentStatuses) ? count($selectedPaymentStatuses).' dipilih' : 'Semua status pembayaran' }}</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><div class="workspace-dropdown-options">@foreach($paymentStatusOptions as $key => $label)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="payment_statuses[]" value="{{ $key }}" data-dropdown-item data-dropdown-value="{{ $label }}" @checked(in_array($key, $selectedPaymentStatuses, true))><span>{{ $label }}</span></label>@endforeach</div></div></div><label class="form-label">Status pembayaran</label></div></div>@endif
<div class="col-md-4 col-xl-3"><div class="workspace-floating-field"><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-summary="count" data-dropdown-empty-label="Semua kategori"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>{{ count($selectedCategories) ? count($selectedCategories).' dipilih' : 'Semua kategori' }}</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><input type="search" class="form-control form-control-sm mb-2" placeholder="Cari kategori..." data-dropdown-search><div class="workspace-dropdown-options">@foreach($categoryOptions as $categoryOption)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="categories[]" value="{{ $categoryOption }}" data-dropdown-item data-dropdown-value="{{ $categoryOption }}" @checked(in_array($categoryOption, $selectedCategories, true))><span>{{ $categoryOption }}</span></label>@endforeach</div></div></div><label class="form-label">Kategori</label></div></div>
<div class="col-md-3 col-xl-2 claim-summary-filter-period"><div class="workspace-floating-field"><input type="date" name="from" value="{{ $periodFrom }}" class="form-control" data-workspace-date-enhanced="true"><label class="form-label">Periode dari</label></div></div><div class="col-md-3 col-xl-2 claim-summary-filter-period claim-list-filter-period-to"><div class="workspace-floating-field"><input type="date" name="to" value="{{ $periodTo }}" class="form-control" data-workspace-date-enhanced="true"><label class="form-label">Periode sampai</label></div></div>
@if($summaryView)<div class="col-md-3 col-xl-2 claim-summary-filter-amount"><div class="workspace-floating-field"><input type="number" name="min_amount" min="0" step="0.01" value="{{ $minimumAmount ?? '' }}" class="form-control" placeholder="0"><label class="form-label">Nominal minimum</label></div></div><div class="col-md-3 col-xl-2 claim-summary-filter-amount"><div class="workspace-floating-field"><input type="number" name="max_amount" min="0" step="0.01" value="{{ $maximumAmount ?? '' }}" class="form-control" placeholder="Tanpa batas"><label class="form-label">Nominal maksimum</label></div></div>@endif
<div class="col-md-2 col-xl-1"><div class="workspace-floating-field"><select name="limit" class="form-select">@foreach([10,25,50,100] as $limit)<option value="{{ $limit }}" @selected($perPage === $limit)>{{ $limit }}</option>@endforeach</select><label class="form-label">Baris</label></div></div><div class="col-md-4 col-xl-3"><div class="workspace-filter-actions"><a href="{{ route('workspace.hris.claims', $summaryView ? ['view' => 'summary', 'type' => $activeTabType] : ($activeTabType ? ['type' => $activeTabType] : [])) }}" class="btn btn-sm btn-outline-secondary">Reset</a><button class="btn btn-sm btn-primary" type="submit">Terapkan</button></div></div>
</div></form>
@if($summaryView) @include('modules.partials.hris-claim-summary-list') @endif
@if($summaryView && $activeTabType === 'reimbursement' && (auth()->user()->hasRole('super_admin') || auth()->user()->can('process_hris_payment')))
    @include('modules.partials.hris-claim-payment-modal')
@endif
@unless($summaryView)
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Nomor</th><th>Karyawan</th><th>Jenis</th><th>Kategori</th><th><a class="workspace-sort-link" href="{{ request()->fullUrlWithQuery(['sort' => 'amount', 'direction' => $sort === 'amount' && $direction === 'asc' ? 'desc' : 'asc']) }}">Nominal</a></th><th>Status pengajuan</th><th>Status pembayaran</th><th class="text-end">Aksi</th></tr></thead><tbody>@forelse($items as $item)<tr><td><code>{{ $item->request_number ?: '—' }}</code></td><td><div class="fw-semibold">{{ $item->employee?->full_name ?? '-' }}</div></td><td>{{ $item->type === 'cash_advance' ? 'Cash Advance' : 'Reimbursement' }}</td><td><div class="d-flex flex-wrap gap-1">@php($claimCategories = $item->expenseItems->pluck('category')->filter()->unique()->values()) @forelse($claimCategories->take(3) as $claimCategory)<span class="badge rounded-pill text-bg-light border">{{ $claimCategory }}</span>@empty<span>{{ $item->category ?: '—' }}</span>@endforelse</div></td><td>{{ $item->currency }} {{ number_format((float) $item->amount, 2, ',', '.') }}</td><td><span class="badge text-bg-{{ in_array($item->status, ['approved','completed'], true) ? 'success' : ($item->status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->status_label }}</span></td><td>@if($item->type === 'reimbursement')<span class="badge text-bg-{{ $item->payment_status === 'paid' ? 'success' : ($item->payment_status === 'pending_payment' ? 'warning' : 'secondary') }}">{{ $item->payment_status_label ?? 'Belum ada' }}</span>@else<span class="text-secondary">—</span>@endif</td><td class="text-end"><div class="d-flex justify-content-end flex-wrap gap-1">@can('view', $item)<a class="btn btn-sm btn-outline-secondary" href="{{ route('workspace.hris.claims.show', $item) }}">Detail</a>@endcan @if($item->status === 'needs_revision' && auth()->user()->can('update', $item))<a class="btn btn-sm btn-primary" href="{{ route('workspace.hris.claims.edit', $item) }}">Revisi</a>@endif</div></td></tr>@empty<tr><td colspan="8" class="text-center text-secondary py-5">Belum ada pengajuan.</td></tr>@endforelse</tbody></table></div><div class="card-footer bg-white workspace-list-pagination"><div class="workspace-pagination-summary">Menampilkan <strong>{{ $items->firstItem() ?? 0 }}</strong>–<strong>{{ $items->lastItem() ?? 0 }}</strong> dari <strong>{{ $items->total() }}</strong> pengajuan</div>{{ $items->onEachSide(1)->links() }}</div></div>
@endunless
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
    const paymentModalElement = document.getElementById('claimPaymentModal');
    const paymentForm = document.getElementById('claimPaymentForm');
    const paymentModal = paymentModalElement && window.bootstrap ? bootstrap.Modal.getOrCreateInstance(paymentModalElement) : null;
    const paymentFileInput = paymentForm?.elements.namedItem('payment_proof_file');
    let paymentFilePreviewUrl = null;
    const renderPaymentFilePreview = (file) => {
        const zone = paymentForm?.querySelector('[data-payment-upload-zone]');
        const preview = paymentForm?.querySelector('[data-payment-file-preview]');
        const thumbnail = paymentForm?.querySelector('[data-payment-file-thumbnail]');
        const pdfIcon = paymentForm?.querySelector('[data-payment-file-pdf]');
        const name = paymentForm?.querySelector('[data-payment-file-name]');
        const meta = paymentForm?.querySelector('[data-payment-file-meta]');
        if (paymentFilePreviewUrl) URL.revokeObjectURL(paymentFilePreviewUrl);
        paymentFilePreviewUrl = null;
        if (!file) {
            zone?.classList.remove('d-none');
            preview?.classList.add('d-none');
            preview?.classList.remove('d-flex');
            thumbnail?.classList.add('d-none');
            if (thumbnail) thumbnail.removeAttribute('src');
            pdfIcon?.classList.add('d-none');
            return;
        }
        zone?.classList.add('d-none');
        preview?.classList.remove('d-none');
        preview?.classList.add('d-flex');
        if (name) name.textContent = file.name;
        if (meta) meta.textContent = `${(file.size / 1024).toLocaleString('id-ID', { maximumFractionDigits: 0 })} KB · ${file.type.startsWith('image/') ? 'Gambar akan dikompresi sebelum dikirim' : 'PDF'}`;
        if (file.type.startsWith('image/')) {
            paymentFilePreviewUrl = URL.createObjectURL(file);
            if (thumbnail) { thumbnail.src = paymentFilePreviewUrl; thumbnail.classList.remove('d-none'); }
            pdfIcon?.classList.add('d-none');
        } else {
            thumbnail?.classList.add('d-none');
            if (thumbnail) thumbnail.removeAttribute('src');
            pdfIcon?.classList.remove('d-none');
        }
    };
    paymentForm?.querySelectorAll('[data-payment-file-pick]').forEach((button) => button.addEventListener('click', () => paymentFileInput?.click()));
    paymentFileInput?.addEventListener('change', () => {
        const file = paymentFileInput.files?.[0] || null;
        renderPaymentFilePreview(file);
        paymentForm.querySelector('[data-payment-error="payment_proof_file"]')?.replaceChildren();
        paymentFileInput.classList.remove('is-invalid');
        const status = paymentForm.querySelector('[data-payment-file-status]');
        if (status) status.textContent = file ? 'Pratinjau file siap. Gambar dikompresi sebelum dikirim.' : 'Format yang didukung: JPEG, PNG, WebP, atau PDF.';
    });
    paymentForm?.querySelector('[data-payment-file-remove]')?.addEventListener('click', () => {
        if (paymentFileInput) paymentFileInput.value = '';
        renderPaymentFilePreview(null);
        const status = paymentForm.querySelector('[data-payment-file-status]');
        if (status) status.textContent = 'Format yang didukung: JPEG, PNG, WebP, atau PDF.';
    });
    const showPaymentMessage = (message, type = 'danger') => {
        const box = document.getElementById('claimPaymentMessage');
        if (!box) return;
        box.className = `alert alert-${type} py-2`;
        box.textContent = message;
        box.classList.remove('d-none');
    };
    const clearPaymentMessage = () => {
        const box = document.getElementById('claimPaymentMessage');
        if (box) { box.classList.add('d-none'); box.textContent = ''; }
        paymentForm?.querySelectorAll('[data-payment-error]').forEach((node) => { node.textContent = ''; });
        paymentForm?.querySelectorAll('.is-invalid').forEach((node) => node.classList.remove('is-invalid'));
    };
    const setPaymentFieldError = (name, message) => {
        const field = name === 'paid_amount' ? paymentForm?.querySelector('#claimPaymentAmount') : paymentForm?.elements.namedItem(name);
        const error = paymentForm?.querySelector(`[data-payment-error="${name}"]`);
        if (field instanceof HTMLElement) field.classList.add('is-invalid');
        if (error) error.textContent = message;
    };
    const paymentAmountInput = document.getElementById('claimPaymentAmount');
    const paymentAmountValue = document.getElementById('claimPaymentAmountValue');
    const paymentAmountDifference = document.getElementById('claimPaymentAmountDifference');
    const paymentAmountFormatter = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const parsePaymentAmount = (value) => {
        let normalized = String(value || '').trim().replace(/\s/g, '');
        if (!normalized) return NaN;
        if (normalized.includes(',')) normalized = normalized.replace(/\./g, '').replace(',', '.');
        else if ((normalized.match(/\./g) || []).length > 1 || /\.\d{3}$/.test(normalized)) normalized = normalized.replace(/\./g, '');
        const amount = Number(normalized);
        return Number.isFinite(amount) ? amount : NaN;
    };
    const updatePaymentAmount = (format = false) => {
        const amount = parsePaymentAmount(paymentAmountInput?.value);
        if (paymentAmountValue) paymentAmountValue.value = Number.isFinite(amount) ? amount.toFixed(2) : '';
        const expected = Number(document.getElementById('claimPaymentExpectedAmount')?.dataset.amount || NaN);
        const differs = Number.isFinite(amount) && Number.isFinite(expected) && Math.round(amount * 100) !== Math.round(expected * 100);
        if (paymentAmountDifference) {
            paymentAmountDifference.textContent = differs
                ? `Nominal dibayar ${paymentAmountFormatter.format(amount)} berbeda dari total pengajuan ${paymentAmountFormatter.format(expected)}.`
                : '';
            paymentAmountDifference.classList.toggle('d-none', !differs);
        }
        if (format && Number.isFinite(amount) && paymentAmountInput) paymentAmountInput.value = paymentAmountFormatter.format(amount);
        return { amount, expected, differs };
    };
    const sanitizePaymentAmountInput = () => {
        if (!paymentAmountInput) return;
        const caret = paymentAmountInput.selectionStart ?? paymentAmountInput.value.length;
        const prefix = paymentAmountInput.value.slice(0, caret);
        const sanitize = (value) => {
            let result = value.replace(/[^0-9,]/g, '');
            const comma = result.indexOf(',');
            if (comma >= 0) result = `${result.slice(0, comma)},${result.slice(comma + 1).replace(/,/g, '').slice(0, 2)}`;
            return result;
        };
        const value = sanitize(paymentAmountInput.value);
        const nextCaret = sanitize(prefix).length;
        if (value !== paymentAmountInput.value) {
            paymentAmountInput.value = value;
            paymentAmountInput.setSelectionRange(nextCaret, nextCaret);
        }
    };
    paymentAmountInput?.addEventListener('focus', () => {
        const { amount } = updatePaymentAmount();
        if (Number.isFinite(amount)) paymentAmountInput.value = `${Math.floor(amount)},${String(Math.round(amount * 100) % 100).padStart(2, '0')}`;
    });
    paymentAmountInput?.addEventListener('input', () => {
        sanitizePaymentAmountInput();
        updatePaymentAmount();
    });
    paymentAmountInput?.addEventListener('blur', () => updatePaymentAmount(true));
    const syncPaymentMethodFields = () => {
        if (!paymentForm) return;
        const method = paymentForm.elements.namedItem('payment_method')?.value;
        const isTransfer = method === 'transfer';
        const hasActiveAccount = paymentForm.dataset.paymentAccountAvailable === '1';
        const paidTo = paymentForm.elements.namedItem('paid_to');
        const bankName = paymentForm.elements.namedItem('bank_name');
        const bankAccount = paymentForm.elements.namedItem('bank_account');
        const pettyCashSource = paymentForm.elements.namedItem('petty_cash_source');
        if (paidTo) paidTo.value = isTransfer ? (paymentForm.dataset.accountHolder || paymentForm.dataset.employeeName || '') : (paymentForm.dataset.employeeName || '');
        if (bankName) bankName.value = isTransfer && hasActiveAccount ? paymentForm.dataset.bankName || '' : '';
        if (bankAccount) bankAccount.value = isTransfer && hasActiveAccount ? paymentForm.dataset.accountNumber || '' : '';
        if (pettyCashSource && method !== 'petty_cash') pettyCashSource.value = '';
        paymentForm.querySelectorAll('[data-payment-bank-fields]').forEach((field) => field.classList.toggle('d-none', !isTransfer));
        paymentForm.querySelector('[data-payment-petty-cash-source-field]')?.classList.toggle('d-none', method !== 'petty_cash');
        paymentForm.querySelector('[data-payment-transfer-notice]')?.classList.toggle('d-none', !isTransfer || !hasActiveAccount);
        paymentForm.querySelector('[data-payment-transfer-unavailable]')?.classList.toggle('d-none', !isTransfer || hasActiveAccount);
        const submit = paymentForm.querySelector('[type="submit"]');
        if (submit) submit.disabled = isTransfer && !hasActiveAccount;
        if (isTransfer && !hasActiveAccount) showPaymentMessage('Rekening reimbursement aktif belum tersedia. Pilih metode tunai/kas kecil atau lengkapi rekening karyawan terlebih dahulu.', 'warning');
    };
    const validatePaymentForm = () => {
        const values = paymentForm.elements;
        const errors = {};
        const required = (key, label) => { if (!String(values.namedItem(key)?.value || '').trim()) errors[key] = `${label} wajib diisi.`; };
        required('paid_to', 'Penerima pembayaran');
        required('paid_at', 'Tanggal pembayaran');
        if (!String(paymentAmountInput?.value || '').trim()) errors.paid_amount = 'Nominal pembayaran wajib diisi.';
        const method = values.namedItem('payment_method')?.value;
        if (!['transfer', 'cash', 'petty_cash'].includes(method)) errors.payment_method = 'Pilih metode pembayaran yang tersedia.';
        if (method === 'transfer') {
            if (paymentForm.dataset.paymentAccountAvailable !== '1') errors.payment_method = 'Rekening reimbursement aktif karyawan belum tersedia.';
        }
        if (method === 'petty_cash') required('petty_cash_source', 'Sumber kas kecil');
        const { amount } = updatePaymentAmount();
        if (!Number.isFinite(amount) || amount <= 0) errors.paid_amount = 'Nominal harus lebih besar dari nol.';
        const date = values.namedItem('paid_at')?.value;
        if (date) {
            const parts = date.match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (!parts) errors.paid_at = 'Tanggal pembayaran tidak valid.';
            else {
                const parsedDate = new Date(Date.UTC(Number(parts[1]), Number(parts[2]) - 1, Number(parts[3])));
                if (parsedDate.getUTCFullYear() !== Number(parts[1]) || parsedDate.getUTCMonth() !== Number(parts[2]) - 1 || parsedDate.getUTCDate() !== Number(parts[3])) errors.paid_at = 'Tanggal pembayaran tidak valid.';
            }
        }
        ['paid_to', 'bank_name', 'bank_account', 'payment_reference', 'payment_proof_id', 'petty_cash_source'].forEach((key) => {
            const field = values.namedItem(key);
            if (field && field.maxLength > 0 && field.value.length > field.maxLength) errors[key] = `Maksimal ${field.maxLength} karakter.`;
        });
        const file = values.namedItem('payment_proof_file')?.files?.[0];
        if (!file) errors.payment_proof_file = 'Bukti pembayaran wajib diunggah.';
        if (file && !['image/jpeg', 'image/png', 'image/webp', 'application/pdf'].includes(file.type)) errors.payment_proof_file = 'Pilih file gambar JPEG, PNG, WebP, atau PDF.';
        if (file && file.size > 5 * 1024 * 1024) errors.payment_proof_file = 'Ukuran file maksimal 5 MB.';
        Object.entries(errors).forEach(([name, message]) => setPaymentFieldError(name, message));
        if (Object.keys(errors).length) showPaymentMessage('Periksa kembali data pembayaran yang ditandai.', 'warning');
        return Object.keys(errors).length === 0;
    };
    const compressPaymentImage = async (file) => {
        if (!file?.type.startsWith('image/')) return file;
        if (typeof createImageBitmap !== 'function') throw new Error('Browser ini belum mendukung kompresi gambar. Gunakan browser terbaru atau unggah PDF.');
        let bitmap;
        try { bitmap = await createImageBitmap(file); } catch { throw new Error('Gambar tidak dapat dibaca. Pilih file gambar lain.'); }
        let maxDimension = 2000;
        let quality = 0.82;
        let blob = null;
        for (let attempt = 0; attempt < 8; attempt++) {
            const scale = Math.min(1, maxDimension / Math.max(bitmap.width, bitmap.height));
            const canvas = document.createElement('canvas');
            canvas.width = Math.max(1, Math.round(bitmap.width * scale));
            canvas.height = Math.max(1, Math.round(bitmap.height * scale));
            canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
            blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));
            if (!blob) { bitmap.close?.(); throw new Error('Gambar gagal dikompresi. Coba pilih file lain.'); }
            if (blob.size <= 5 * 1024 * 1024) break;
            maxDimension = Math.max(640, Math.round(maxDimension * 0.82));
            quality = Math.max(0.45, quality - 0.07);
        }
        bitmap.close?.();
        if (!blob || blob.size > 5 * 1024 * 1024) throw new Error('Gambar tetap lebih dari 5 MB setelah kompresi. Pilih gambar yang lebih kecil.');
        return blob.size < file.size ? new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg', lastModified: Date.now() }) : file;
    };
    const preparePayment = (button) => {
        if (!button || !paymentForm) return;
        clearPaymentMessage();
        paymentForm.querySelectorAll('input:not([name="_token"]), select').forEach((field) => {
            if (field.name === 'payment_method') field.value = 'transfer';
            else if (field.name === 'paid_at') field.value = @json(now(config('app.timezone'))->toDateString());
            else field.value = '';
        });
        renderPaymentFilePreview(null);
        const fileStatus = paymentForm.querySelector('[data-payment-file-status]');
        if (fileStatus) fileStatus.textContent = 'Format yang didukung: JPEG, PNG, WebP, atau PDF.';
        paymentForm.action = button.dataset.paymentUrl;
        paymentForm.dataset.claimId = button.dataset.claimPaymentId;
        document.getElementById('claimPaymentRequestNumber').textContent = button.dataset.paymentNumber || 'Nomor belum tersedia';
        document.getElementById('claimPaymentEmployee').textContent = `${button.dataset.paymentEmployee || 'Karyawan'} · ${button.dataset.paymentCompany || 'Perusahaan'}`;
        document.getElementById('claimPaymentExpectedAmount').textContent = `${button.dataset.paymentCurrency || 'IDR'} ${Number(button.dataset.paymentAmount || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        const amount = paymentAmountInput;
        if (amount) amount.value = paymentAmountFormatter.format(Number(button.dataset.paymentAmount || 0));
        if (paymentAmountValue) paymentAmountValue.value = Number(button.dataset.paymentAmount || 0).toFixed(2);
        document.getElementById('claimPaymentExpectedAmount').dataset.amount = button.dataset.paymentAmount || '0';
        updatePaymentAmount();
        paymentForm.dataset.paymentAccountAvailable = button.dataset.paymentAccountAvailable || '0';
        paymentForm.dataset.accountHolder = button.dataset.paymentAccountHolder || '';
        paymentForm.dataset.bankName = button.dataset.paymentBankName || '';
        paymentForm.dataset.accountNumber = button.dataset.paymentAccountNumber || '';
        paymentForm.dataset.employeeName = button.dataset.paymentEmployee || '';
        syncPaymentMethodFields();
        paymentForm.dataset.activeButtonId = button.dataset.claimPaymentId;
        paymentModalElement?.querySelector('[data-payment-next]')?.classList.add('d-none');
        paymentModal?.show();
    };
    paymentForm?.elements.namedItem('payment_method')?.addEventListener('change', () => {
        clearPaymentMessage();
        syncPaymentMethodFields();
    });
    document.addEventListener('click', (event) => {
        const paymentButton = event.target.closest('[data-claim-payment-open]');
        if (paymentButton) {
            event.preventDefault();
            closeClaimAction();
            preparePayment(paymentButton);
            return;
        }
        if (event.target.closest('[data-payment-next]')) {
            const currentRow = document.querySelector(`.claim-summary-table tr[data-claim-id="${CSS.escape(paymentForm?.dataset.claimId || '')}"]`);
            const rows = [...document.querySelectorAll('.claim-summary-table tbody tr[data-claim-id]')];
            const start = rows.indexOf(currentRow);
            const candidates = rows.slice(start + 1).concat(rows.slice(0, Math.max(start, 0)));
            const nextButton = candidates.map((row) => row.querySelector('[data-claim-payment-open]')).find(Boolean);
            if (nextButton) preparePayment(nextButton);
            else { paymentModal?.hide(); }
            return;
        }
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
    paymentForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearPaymentMessage();
        if (!validatePaymentForm()) return;
        const amountState = updatePaymentAmount();
        const submit = paymentForm.querySelector('[type="submit"]');
        if (amountState.differs) {
            submit.disabled = true;
            const confirmed = await window.workspaceConfirm?.(
                `Nominal yang dibayar (${paymentAmountFormatter.format(amountState.amount)}) berbeda dengan total pengajuan (${paymentAmountFormatter.format(amountState.expected)}). Tetap simpan pencatatan pembayaran?`,
                { action: 'Tetap simpan' },
            );
            if (!confirmed) {
                submit.disabled = false;
                return;
            }
        }
        const amountDifferenceConfirmed = amountState.differs;
        window.dispatchEvent(new CustomEvent('workspace:show-loading', { detail: 'Menyimpan pencatatan pembayaran...' }));
        submit.disabled = true;
        try {
            const formData = new FormData(paymentForm);
            formData.set('amount_difference_confirmed', amountDifferenceConfirmed ? '1' : '0');
            const fileInput = paymentForm.elements.namedItem('payment_proof_file');
            const selectedFile = fileInput?.files?.[0];
            if (selectedFile?.type.startsWith('image/')) {
                const status = paymentForm.querySelector('[data-payment-file-status]');
                if (status) status.textContent = 'Mengompresi gambar...';
                const compressedFile = await compressPaymentImage(selectedFile);
                formData.set('payment_proof_file', compressedFile, compressedFile.name);
                if (status) status.textContent = compressedFile.size < selectedFile.size ? `Gambar dikompresi: ${(selectedFile.size / 1024).toFixed(0)} KB menjadi ${(compressedFile.size / 1024).toFixed(0)} KB.` : 'Ukuran gambar sudah optimal.';
                if (compressedFile.size > 5 * 1024 * 1024) { setPaymentFieldError('payment_proof_file', 'Ukuran gambar setelah diproses maksimal 5 MB.'); showPaymentMessage('File bukti pembayaran terlalu besar.', 'warning'); return; }
            }
            const response = await fetch(paymentForm.action, { method: 'POST', body: formData, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            const payload = await response.json();
            if (!response.ok) {
                if (response.status === 422 && payload.errors) {
                    Object.entries(payload.errors).forEach(([field, messages]) => {
                        const error = paymentForm.querySelector(`[data-payment-error="${field}"]`);
                        if (error) error.textContent = messages[0];
                    });
                    showPaymentMessage(payload.message || 'Periksa kembali data pembayaran.', 'warning');
                } else showPaymentMessage(payload.message || 'Pembayaran gagal disimpan. Silakan coba lagi.');
                return;
            }
            const claimId = paymentForm.dataset.claimId;
            const row = document.querySelector(`.claim-summary-table tr[data-claim-id="${CSS.escape(claimId)}"]`);
            const paymentCell = row?.querySelector('td:nth-child(7)');
            if (paymentCell) paymentCell.innerHTML = '<span class="badge rounded-pill text-bg-success">Dibayar</span>';
            document.querySelectorAll(`[data-claim-payment-id="${CSS.escape(claimId)}"]`).forEach((button) => button.remove());
            showPaymentMessage(payload.message || 'Pembayaran berhasil dicatat.', 'success');
            const nextControl = paymentModalElement?.querySelector('[data-payment-next]');
            if (nextControl) nextControl.classList.toggle('d-none', !document.querySelector('[data-claim-payment-open]'));
        } catch (error) {
            showPaymentMessage(error instanceof Error ? error.message : 'Koneksi terganggu. Data pembayaran belum tersimpan. Silakan coba lagi.');
        } finally {
            submit.disabled = false;
            window.dispatchEvent(new Event('workspace:hide-loading'));
        }
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
        @if($claimRow->type === 'reimbursement' && $claimRow->finance_approved_at && $claimRow->status === 'approved' && auth()->user()->can('print', $claimRow))
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
            const normalizedLabel = label.toLowerCase();
            const isDelete = /hapus|delete/.test(normalizedLabel);
            const isEdit = /edit|revisi/.test(normalizedLabel);
            const isView = /detail|lihat/.test(normalizedLabel);
            const icon = document.createElement('span');
            icon.className = `claim-summary-menu-icon${isDelete ? ' delete' : (isEdit ? ' edit' : (/pembayaran|cetak|selesai/i.test(label) ? ' print' : ''))}`;
            icon.innerHTML = isDelete
                ? '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m3 0-.8 13H6.8L6 7m4 4v5m4-5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                : (isEdit
                    ? '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14 5 5 5M4 20l4.3-.9L19 8.4 15.6 5 4.9 15.7 4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                    : (isView
                        ? '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.3-6 9.5-6 9.5 6 9.5 6-3.3 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/></svg>'
                        : '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 5h16v14H4zM8 9h8M8 13h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>'));
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
