@extends('layouts.app', ['title' => 'Ringkasan HRIS'])

@section('content')
<style>
.hris-home-hero{padding:1.15rem 1.5rem;border-radius:1rem;color:#fff;background:linear-gradient(112deg,#12345d,#176381 65%,#27877f);box-shadow:0 10px 26px rgba(22,59,91,.12)}
.hris-home-kicker{font-size:.65rem;font-weight:750;letter-spacing:.13em;color:#d5eaf2}
.hris-home-title{margin:.35rem 0 .2rem;color:#fff!important;font-size:1.55rem;font-weight:750;letter-spacing:-.03em}
.hris-home-copy{margin:0;color:#e0edf2;font-size:.87rem}
.hris-request-card{display:flex;height:100%;flex-direction:column;overflow:hidden;border:1px solid #e3eaf1;border-radius:.9rem;background:linear-gradient(145deg,#fff 20%,#f2f7ff 100%);box-shadow:0 5px 16px rgba(25,48,73,.045)}
.hris-request-card.overtime{background:linear-gradient(145deg,#fff 20%,#fff8ea 100%)}.hris-request-card.claim{background:linear-gradient(145deg,#fff 20%,#eff9f5 100%)}
.hris-request-main{display:block;flex:1;padding:.9rem 1rem .65rem;color:inherit;text-decoration:none}.hris-request-main:hover .hris-request-count{text-decoration:underline;text-underline-offset:3px}
.hris-request-head{display:flex;align-items:center;justify-content:space-between;gap:.5rem}.hris-request-name{margin:0;color:#50627a;font-size:.81rem;font-weight:650}.hris-request-icon{display:grid;width:30px;height:30px;place-items:center;border-radius:10px;background:#e6efff;color:#376fc4}.overtime .hris-request-icon{background:#fff0d2;color:#a76c13}.claim .hris-request-icon{background:#ddf3ea;color:#287e66}.hris-request-icon svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.hris-request-count{display:block;margin-top:.35rem;color:#172c47;font-size:1.8rem;font-weight:760;line-height:1.05;letter-spacing:-.04em}.hris-request-hint{display:block;margin-top:.2rem;color:#75869b;font-size:.7rem}.hris-request-period{display:inline-flex;align-items:center;gap:.25rem;margin-top:.3rem;color:#7c8a9c;font-size:.64rem;font-weight:600}.hris-request-period:before{content:"";width:5px;height:5px;border-radius:50%;background:#91a2b5}
.hris-request-details{display:flex;flex-direction:column;gap:.18rem;margin-top:.5rem;padding-top:.45rem;border-top:1px solid rgba(109,130,154,.15)}.hris-request-details span{color:#536981;font-size:.7rem;line-height:1.3}
.hris-request-footer{display:flex;justify-content:flex-end;padding:0 .85rem .75rem}.hris-request-action{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .58rem;border:1px solid rgba(56,105,174,.16);border-radius:.55rem;background:rgba(255,255,255,.72);color:#315f9d;font-size:.73rem;font-weight:700;text-decoration:none}.hris-request-action:hover{background:#fff;color:#174e87}.hris-request-action svg{width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.hris-early-warning{overflow:hidden;border:1px solid #f0e4c8!important;border-left:4px solid #e7ae45!important;border-radius:.9rem!important;box-shadow:0 6px 18px rgba(60,48,20,.04)}.hris-early-warning .card-header{padding:.8rem 1rem;border-bottom:1px solid #f2ead9}.hris-early-warning .list-group-item{padding:.75rem 1rem}
@media(max-width:767.98px){.hris-home-hero{padding:.95rem 1.1rem;border-radius:.85rem}.hris-home-title{font-size:1.35rem}.hris-home-copy{font-size:.82rem}.hris-request-main{padding:.65rem .65rem .45rem}.hris-request-name{max-width:calc(100% - 34px);font-size:.7rem;line-height:1.2;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;overflow:hidden}.hris-request-icon{width:27px;height:27px;border-radius:9px}.hris-request-count{font-size:1.4rem}.hris-request-hint{font-size:.64rem}.hris-request-details{gap:.16rem;margin-top:.4rem;padding-top:.35rem}.hris-request-details span{font-size:.66rem}.hris-request-footer{padding:0 .55rem .55rem}.hris-request-action{gap:.2rem;padding:.32rem .42rem;font-size:.68rem}}
</style>

<section class="hris-home-hero mb-3">
    <div class="hris-home-kicker">PEOPLE &amp; OPERATIONS · RUANG PRIBADI</div>
    <h1 class="hris-home-title">Ringkasan HRIS</h1>
    <p class="hris-home-copy">Pantau pengajuan pribadi dan mulai pengajuan baru.</p>
</section>

<div class="row g-2 mb-3">
    @foreach($personalStats as $index => $stat)
        @php($kind = ['leave', 'overtime', 'claim'][$index] ?? 'leave')
        <div class="{{ $kind === 'leave' ? 'col-12' : 'col-6' }} col-sm-4">
            <article class="hris-request-card {{ $kind }}">
                @if($stat['view_url'])<a class="hris-request-main" href="{{ $stat['view_url'] }}" aria-label="Lihat daftar {{ $stat['label'] }}">@else<div class="hris-request-main">@endif
                    <div class="hris-request-head">
                        <h2 class="hris-request-name">{{ $stat['label'] }}</h2>
                        <span class="hris-request-icon" aria-hidden="true">
                            @if($kind === 'leave')<svg viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M8 3v4M16 3v4M3.5 10h17M8 14h3M8 17h6"/></svg>
                            @elseif($kind === 'overtime')<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>
                            @else<svg viewBox="0 0 24 24"><path d="M6 3.5h9l4 4V21H6zM14.5 3.5V8H19M9 12h7M9 16h7"/></svg>@endif
                        </span>
                    </div>
                    <span class="hris-request-count">{{ number_format($stat['value']) }}</span>
                    <span class="hris-request-hint">{{ $stat['view_url'] ? 'pengajuan · klik untuk daftar' : 'pengajuan pada periode ini' }}</span>
                    <span class="hris-request-period">{{ $stat['period'] }}</span>
                    <span class="hris-request-details">@foreach($stat['details'] as $detail)<span>{{ $detail }}</span>@endforeach</span>
                @if($stat['view_url'])</a>@else</div>@endif
                @if($stat['create_url'])
                    <div class="hris-request-footer"><a class="hris-request-action" href="{{ $stat['create_url'] }}"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Ajukan</a></div>
                @endif
            </article>
        </div>
    @endforeach
</div>

@if($canViewApprovals && $approvalRows->isNotEmpty())
    <div class="card mb-3 hris-early-warning">
        <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div><span class="text-uppercase text-secondary small fw-semibold">Early warning</span><h2 class="h6 mb-0">Approval pengajuan</h2></div>
            <div class="d-flex align-items-center gap-2"><span class="badge text-bg-warning">{{ $approvalRows->count() }} menunggu</span><a href="{{ route('workspace.hris.approvals') }}" class="btn btn-sm btn-outline-primary">Buka semua</a></div>
        </div>
        <div class="list-group list-group-flush">@foreach($approvalRows->take(5) as $row)<a href="{{ route('workspace.hris.approval-detail', ['type' => $row['type'], 'id' => $row['item']->getKey(), 'step' => $row['step']]) }}" class="list-group-item list-group-item-action d-flex flex-wrap justify-content-between align-items-center gap-2"><div><strong>{{ $row['label'] }} · {{ $row['item']->employee?->full_name ?? 'Pemohon' }}</strong><small class="d-block text-secondary">{{ $row['item']->request_number ?? '-' }} · Tahap {{ $row['stage'] }}</small></div><span class="badge text-bg-light border">Tinjau</span></a>@endforeach</div>
    </div>
@endif
@endsection
