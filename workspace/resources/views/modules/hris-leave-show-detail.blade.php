@extends('layouts.app', ['title' => 'Detail Cuti dan Sakit'])

@section('content')
    @php($statusClass = match ($leaveRequest->status) {
        'approved' => 'success',
        'rejected' => 'danger',
        'needs_revision' => 'warning',
        default => 'primary',
    })

    <div class="workspace-leave-detail-hero">
        <div>
            <div class="workspace-form-kicker">Kehadiran</div>
            <h1 class="h3 mb-1">Detail pengajuan</h1>
            <p class="text-secondary mb-0">
                {{ $leaveRequest->type === 'leave' ? 'Cuti' : 'Sakit' }} · {{ $leaveRequest->employee?->full_name ?? '-' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if ($showRequesterActions && $leaveRequest->status === 'needs_revision')
                <a href="{{ route('workspace.hris.leave-requests.edit', $leaveRequest) }}" class="btn btn-primary btn-sm">Revisi pengajuan</a>
            @endif
            <a href="{{ $detailContext === 'summary' ? route('workspace.hris.leave-summary') : route('workspace.hris.leave-requests') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
        </div>
    </div>

    <div class="workspace-leave-detail-summary">
        <div class="workspace-leave-detail-stat"><span>Jumlah hari</span><strong>{{ $leaveRequest->total_days }}</strong><small>hari diajukan</small></div>
        <div class="workspace-leave-detail-stat"><span>Status</span><strong><span class="badge text-bg-{{ $statusClass }}">{{ $leaveRequest->status_label }}</span></strong><small>status saat ini</small></div>
        <div class="workspace-leave-detail-stat"><span>Diajukan</span><strong>{{ ($leaveRequest->created_at ?? $leaveRequest->submitted_at)?->format('d/m/Y') }}</strong><small>{{ ($leaveRequest->created_at ?? $leaveRequest->submitted_at)?->format('H:i') }} WIB</small></div>
    </div>

    <div class="row g-3 workspace-leave-detail-panels">
        <div class="col-lg-5">
            <section class="card workspace-leave-detail-card h-100">
                <div class="card-header"><span class="workspace-detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="3.5" y="5" width="17" height="16" rx="3" stroke="currentColor" stroke-width="1.7"/><path d="M7.5 3v4M16.5 3v4M4 9.5h16M8 13h2m4 0h2m-8 4h2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><div><strong>Tanggal pengajuan</strong><small>Daftar tanggal yang dipilih</small></div></div>
                <div class="card-body">
                    <div class="workspace-leave-date-list">
                        @forelse ($leaveRequest->dates as $index => $date)
                            <div class="workspace-leave-date-item"><span>{{ $index + 1 }}</span><strong>{{ $date->leave_date?->format('d/m/Y') }}</strong><small>{{ $date->leave_date?->translatedFormat('l') }}</small></div>
                        @empty
                            <p class="text-secondary mb-0">Belum ada tanggal.</p>
                        @endforelse
                    </div>
                    <hr>
                    <div class="workspace-detail-reason"><span>Alasan pengajuan</span><p>{{ $leaveRequest->reason ?: 'Tidak ada alasan.' }}</p></div>
                    @if ($hasSupportingDocument)
                        <div class="workspace-leave-supporting-document">
                            <div class="workspace-leave-supporting-copy"><span class="workspace-detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 3.75h7l4 4v12.5H6a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2h1Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 4v4h4M8 13h8m-8 3.5h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><div><strong>Dokumen pendukung</strong><small>Surat pendukung pengajuan tersedia.</small></div></div>
                            @if ($canPreviewSupportingDocument)
                                <button type="button" class="btn btn-sm btn-outline-primary workspace-leave-document-action" data-bs-toggle="modal" data-bs-target="#supporting-document-modal"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.3-6 9.5-6 9.5 6 9.5 6-3.3 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/></svg>Lihat dokumen</button>
                            @else
                                <a href="{{ route('workspace.hris.leave-requests.supporting-document', $leaveRequest) }}" class="btn btn-sm btn-outline-primary workspace-leave-document-action"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3.5v11m0 0 4-4m-4 4-4-4M5 16v4h14v-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>Unduh dokumen</a>
                            @endif
                        </div>
                    @endif
                </div>
            </section>
        </div>
        <div class="col-lg-7">
            @php($timelineId = 'leave-approval-timeline-'.$leaveRequest->getKey())
            <section class="card workspace-leave-detail-card workspace-leave-timeline-card h-100">
                <h2 class="card-header p-0 mb-0">
                    <button class="workspace-leave-timeline-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $timelineId }}" aria-expanded="true" aria-controls="{{ $timelineId }}">
                        <span class="workspace-detail-icon is-timeline" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 8v4l2.5 1.5M20 12a8 8 0 1 1-2.34-5.66M20 4v5h-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <span class="workspace-leave-timeline-title"><strong>Timeline approval</strong><small>Riwayat proses pengajuan</small></span>
                        <span class="workspace-leave-timeline-count">{{ $leaveRequest->approvalEvents->count() + 1 }} aktivitas</span>
                        <svg class="workspace-leave-timeline-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </h2>
                <div class="collapse show" id="{{ $timelineId }}">
                <div class="card-body workspace-leave-timeline-body">
                    <div class="workspace-detail-timeline">
                        <div class="workspace-detail-timeline-item is-done">
                            <span class="workspace-detail-timeline-dot">✓</span>
                            <div><strong>Pengajuan dibuat</strong><small>{{ ($leaveRequest->created_at ?? $leaveRequest->submitted_at)?->format('d/m/Y H:i') }} WIB{{ $leaveRequest->reason ? ' · Alasan pengajuan: '.$leaveRequest->reason : '' }}</small></div>
                        </div>
                        @forelse ($leaveRequest->approvalEvents as $event)
                            <div class="workspace-detail-timeline-item {{ $event->action === 'reject' ? 'is-danger' : ($event->action === 'request_revision' ? 'is-warning' : 'is-done') }}">
                                <span class="workspace-detail-timeline-dot">{{ $event->action === 'approve' ? '✓' : ($event->action === 'reject' ? '×' : '↻') }}</span>
                                <div><strong>{{ $event->action === 'request_revision' ? 'Meminta revisi' : ($event->action === 'resubmitted' ? 'Pengajuan dikirim ulang' : ($event->step === 'manager' ? 'Approval atasan' : 'Approval HR')) }}</strong><small>{{ $event->created_at?->format('d/m/Y H:i') }} WIB{{ $event->comment ? ' · Keterangan: '.$event->comment : '' }}</small></div>
                            </div>
                        @empty
                            <div class="text-secondary small">Belum ada aktivitas approval.</div>
                        @endforelse
                    </div>
                </div>
                </div>
            </section>
        </div>
    </div>

    @if ($hasSupportingDocument && $canPreviewSupportingDocument)
            <div class="modal fade" id="supporting-document-modal" tabindex="-1" aria-labelledby="supporting-document-title" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 class="modal-title h5" id="supporting-document-title">Dokumen pendukung</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body p-0" style="min-height:70vh">
                    @if ($supportingMimeType === 'application/pdf')
                        <iframe src="{{ route('workspace.hris.leave-requests.supporting-document', [$leaveRequest, 'preview' => 1]) }}" title="Dokumen pendukung" style="width:100%;height:70vh;border:0"></iframe>
                    @else
                        @include('modules.partials.image-zoom-viewer', ['src' => route('workspace.hris.leave-requests.supporting-document', [$leaveRequest, 'preview' => 1]), 'title' => 'Dokumen pendukung'])
                    @endif
                </div></div></div>
            </div>
    @endif
@endsection
