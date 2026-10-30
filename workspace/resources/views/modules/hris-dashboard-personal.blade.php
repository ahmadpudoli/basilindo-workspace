@extends('layouts.app', ['title' => 'Ringkasan HRIS'])

@section('content')
<div class="mb-4">
    <div class="text-uppercase text-secondary small fw-semibold mb-2">Ringkasan pribadi</div>
    <h1 class="h3 mb-1">Ringkasan HRIS</h1>
    <p class="text-secondary mb-0">Pantau pengajuan HRIS yang Anda buat atau terkait dengan akun Anda.</p>
</div>

<div class="row g-3 mb-4">
    @foreach($personalStats as $stat)
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary small">{{ $stat['label'] }}</div>
                    <div class="display-6 fw-semibold text-primary">{{ number_format($stat['value']) }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($canViewApprovals)
    <div class="card border-0 shadow-sm mb-4 workspace-hris-approval-warning">
        <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2"><div><span class="text-uppercase text-secondary small fw-semibold">Early warning</span><h2 class="h5 mb-0">Approval pengajuan</h2></div><div class="d-flex align-items-center gap-2"><span class="badge text-bg-warning">{{ $approvalRows->count() }} menunggu</span><a href="{{ route('workspace.hris.approvals') }}" class="btn btn-sm btn-outline-primary">Buka semua</a></div></div>
        <div class="list-group list-group-flush">@forelse($approvalRows->take(5) as $row)<a href="{{ route('workspace.hris.approval-detail', ['type' => $row['type'], 'id' => $row['item']->getKey(), 'step' => $row['step']]) }}" class="list-group-item list-group-item-action d-flex flex-wrap justify-content-between align-items-center gap-3"><div><strong>{{ $row['label'] }} · {{ $row['item']->employee?->full_name ?? 'Pemohon' }}</strong><small class="d-block text-secondary">{{ $row['item']->request_number ?? '-' }} · Tahap {{ $row['stage'] }}</small></div><span class="badge text-bg-light border">Tinjau</span></a>@empty<div class="card-body text-secondary">Tidak ada pengajuan yang menunggu persetujuan Anda.</div>@endforelse</div>
    </div>
@endif

<div class="card">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h2 class="h6 mb-1">Pengajuan pribadi</h2>
            <p class="text-secondary mb-0">Akses daftar pengajuan diberikan sesuai permission yang Anda miliki.</p>
        </div>
        @if($canCreateLeave)
            <a href="{{ route('workspace.hris.leave-requests.create') }}" class="btn btn-sm btn-primary">Ajukan cuti/sakit</a>
        @endif
    </div>
</div>
@endsection
