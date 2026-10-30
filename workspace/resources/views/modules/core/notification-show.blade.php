@extends('layouts.app', ['title' => 'Detail notifikasi'])

@section('content')
    <div class="workspace-notification-detail-heading">
        <div>
            <div class="text-uppercase text-secondary small fw-semibold mb-2">Workspace</div>
            <h1 class="h3 mb-1">Detail notifikasi</h1>
            <p class="text-secondary mb-0">Informasi lengkap dari notifikasi yang Anda terima.</p>
        </div>
        <a href="{{ route('workspace.notifications') }}" class="workspace-back-button">
            <span class="workspace-back-button-icon" aria-hidden="true">←</span>
            <span>Kembali ke notifikasi</span>
        </a>
    </div>

    <article class="workspace-notification-detail-card">
        <div class="workspace-notification-detail-accent"></div>
        <div class="workspace-notification-detail-body">
            <div class="d-flex align-items-start gap-3">
                <div class="workspace-notification-detail-icon" aria-hidden="true">i</div>
                <div class="min-w-0">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge workspace-notification-type">{{ $notification->type }}</span>
                        @if($notification->read_at)
                            <span class="workspace-notification-read-label">Sudah dibaca</span>
                        @else
                            <span class="workspace-notification-unread-label">Belum dibaca</span>
                        @endif
                    </div>
                    <h2 class="workspace-notification-detail-title">{{ $notification->title }}</h2>
                    <div class="workspace-notification-detail-time">
                        <span aria-hidden="true">◷</span>
                        {{ $notification->created_at?->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>

            <div class="workspace-notification-detail-message">{{ $notification->message }}</div>
            @if(data_get($notification->data, 'request_number') || data_get($notification->data, 'requester_name'))
                <div class="alert alert-light border mt-3 mb-0"><strong>Identitas pengajuan</strong><div class="small mt-1">Nomor: {{ data_get($notification->data, 'request_number', '—') }} · Pemohon: {{ data_get($notification->data, 'requester_name', '—') }}</div></div>
            @endif

            <div class="workspace-notification-detail-footer">
                @if($targetUrl)
                    <a href="{{ $targetUrl }}" class="btn btn-primary workspace-notification-target-button">
                        {{ $targetLabel }} <span aria-hidden="true">→</span>
                    </a>
                @else
                    <div class="workspace-notification-no-target">Tidak ada halaman terkait untuk notifikasi ini.</div>
                @endif
                <a href="{{ route('workspace.notifications') }}" class="workspace-back-button workspace-back-button-secondary">
                    <span class="workspace-back-button-icon" aria-hidden="true">←</span>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </article>
@endsection
