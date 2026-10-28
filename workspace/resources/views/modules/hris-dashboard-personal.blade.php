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
