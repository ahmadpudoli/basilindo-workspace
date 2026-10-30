@extends('layouts.app', ['title' => 'Detail persetujuan HRIS'])

@section('content')
    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="text-uppercase text-secondary small fw-semibold mb-2">Kehadiran &amp; claim</div>
            <h1 class="h3 mb-1">Detail persetujuan</h1>
            <p class="text-secondary mb-0">Tinjau detail pengajuan sebelum memberikan keputusan.</p>
        </div>
        <a href="{{ route('workspace.hris.approvals') }}" class="btn btn-outline-secondary">Kembali ke persetujuan</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-warning workspace-alert" role="alert">
            <ul class="mb-0 ps-3">
                @foreach (array_unique($errors->all()) as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <section class="card border-0 shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase text-secondary small fw-semibold">Pengajuan {{ $typeLabel }}</div>
                        <h2 class="h5 mb-0">{{ $model->employee?->full_name ?? '-' }}</h2>
                        <div class="small text-primary mt-1">Nomor pengajuan: {{ $model->request_number }}</div>
                    </div>
                    <span class="badge rounded-pill {{ $step === 'hr' ? 'text-bg-info' : 'text-bg-warning' }}">Tahap {{ $stage }}</span>
                </div>
                <div class="card-body py-3">
                    <div class="row g-3">
                        <div class="col-md-6"><div class="small text-secondary">Pemohon</div><div class="fw-semibold">{{ $model->employee?->full_name ?? '-' }}</div><div class="small text-secondary">{{ $model->employee?->employee_number ?? '-' }}</div></div>
                        <div class="col-md-6"><div class="small text-secondary">Nomor pengajuan</div><div class="fw-semibold text-primary">{{ $model->request_number }}</div></div>
                        <div class="col-md-6"><div class="small text-secondary">Perusahaan</div><div class="fw-semibold">{{ $model->employee?->company?->name ?? '-' }}</div></div>
                        <div class="col-md-6"><div class="small text-secondary">Status</div><div class="fw-semibold">{{ $model->status_label ?? ucfirst(str_replace('_', ' ', $model->status ?? '-')) }}</div></div>
                        <div class="col-md-6"><div class="small text-secondary">Diajukan</div><div class="fw-semibold">{{ ($model->created_at ?? $model->submitted_at)?->format('d/m/Y H:i') ?? '-' }}</div></div>

                        @if ($type === 'leave')
                            <div class="col-12"><div class="small text-secondary mb-2">Tanggal pengajuan</div><div class="workspace-leave-date-badges">@foreach ($model->dates as $date)<span class="badge rounded-pill">{{ $date->leave_date?->format('d/m/Y') ?? '-' }}</span>@endforeach</div></div>
                            <div class="col-md-6"><div class="small text-secondary">Jumlah hari</div><div class="fw-semibold">{{ $model->total_days ?? '-' }} hari</div></div>
                            <div class="col-12"><div class="small text-secondary">Alasan</div><div class="fw-semibold">{{ $model->reason ?: '-' }}</div></div>
                        @elseif ($type === 'overtime')
                            <div class="col-md-6"><div class="small text-secondary">Tanggal kerja</div><div class="fw-semibold">{{ $model->work_date?->format('d/m/Y') ?? '-' }}</div></div>
                            <div class="col-md-6"><div class="small text-secondary">Jam</div><div class="fw-semibold">{{ $model->start_time ?? '-' }} - {{ $model->end_time ?? '-' }}</div></div>
                            <div class="col-md-6"><div class="small text-secondary">Durasi</div><div class="fw-semibold">{{ $model->duration_minutes ? $model->duration_minutes.' menit' : '-' }}</div></div>
                            <div class="col-12"><div class="small text-secondary">Alasan</div><div class="fw-semibold">{{ $model->reason ?: '-' }}</div></div>
                        @else
                            <div class="col-md-6"><div class="small text-secondary">Kategori</div><div class="fw-semibold">{{ $model->category ?: '-' }}</div></div>
                            <div class="col-md-6"><div class="small text-secondary">Nominal</div><div class="fw-semibold">{{ $model->currency ?? 'IDR' }} {{ $model->amount !== null ? number_format((float) $model->amount, 2, ',', '.') : '-' }}</div></div>
                            <div class="col-12"><div class="small text-secondary">Keperluan</div><div class="fw-semibold">{{ $model->purpose ?: '-' }}</div></div>
                        @endif

                        <div class="col-12">
                            <div class="small text-secondary">Dokumen pendukung</div>
                            @if ($hasSupportingDocument)
                                @if ($canPreviewSupportingDocument)
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-1" data-bs-toggle="modal" data-bs-target="#approval-supporting-document-modal">Buka dokumen pendukung</button>
                                @else
                                    <a href="{{ route('workspace.hris.supporting-document', ['type' => $type, 'id' => $model->getKey(), 'step' => $step, 'download' => 1]) }}" class="btn btn-sm btn-outline-primary mt-1">Unduh dokumen pendukung</a>
                                @endif
                            @else
                                <div class="text-secondary mt-1">Dokumen pendukung tidak tersedia atau belum diupload.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
            @if ($type === 'leave')
                <section class="card border-0 shadow-sm">
                    <div class="card-header"><strong>Timeline pengajuan</strong><div class="small text-secondary">Riwayat proses pengajuan.</div></div>
                    <div class="card-body">
                        <div class="workspace-detail-timeline">
                            <div class="workspace-detail-timeline-item is-done"><span class="workspace-detail-timeline-dot">✓</span><div><strong>Pengajuan dibuat</strong><small>{{ ($model->created_at ?? $model->submitted_at)?->format('d/m/Y H:i') }} WIB
                                @if ($model->reason)
                                    <br>Alasan pengajuan: {{ $model->reason }}
                                @endif
                            </small></div></div>
                            @forelse ($model->approvalEvents as $event)
                                <div class="workspace-detail-timeline-item {{ $event->action === 'reject' ? 'is-danger' : ($event->action === 'request_revision' ? 'is-warning' : 'is-done') }}"><span class="workspace-detail-timeline-dot">{{ $event->action === 'approve' ? '✓' : ($event->action === 'reject' ? '×' : '↻') }}</span><div><strong>{{ $event->action === 'request_revision' ? 'Meminta revisi' : ($event->action === 'resubmitted' ? 'Pengajuan dikirim ulang' : ($event->step === 'manager' ? 'Approval atasan' : 'Approval HR')) }}</strong><small>{{ $event->created_at?->format('d/m/Y H:i') }} WIB
                                    @if ($event->comment)
                                        <br>Keterangan: {{ $event->comment }}
                                    @endif
                                </small></div></div>
                            @empty
                                <div class="text-secondary small">Belum ada aktivitas approval.</div>
                            @endforelse
                        </div>
                    </div>
                </section>
            @endif
        </div>

        <div class="col-lg-4">
            <section class="card border-0 shadow-sm">
                <div class="card-header"><strong>Tindakan approval</strong><div class="small text-secondary">Tolak dan minta revisi memerlukan alasan.</div></div>
                <div class="card-body d-grid gap-3">
                    <div class="border rounded-3 p-3 bg-light-subtle">
                        <div class="fw-semibold mb-1">Setujui pengajuan</div>
                        <div class="small text-secondary mb-3">Pengajuan akan diteruskan ke tahap berikutnya.</div>
                        <form method="POST" action="{{ route('workspace.hris.approve', [$type, $model->getKey(), $step]) }}" data-confirm="Setujui pengajuan {{ $typeLabel }} dari {{ $model->employee?->full_name ?? 'pemohon' }}?" data-confirm-action="Setujui pengajuan" data-confirm-comment="optional">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <button class="btn btn-primary w-100">Setujui pengajuan</button>
                        </form>
                    </div>

                    <div class="border rounded-3 p-3">
                        <div class="fw-semibold text-danger mb-1">Tolak pengajuan</div>
                        <div class="small text-secondary mb-3">Pengajuan akan dihentikan dan alasan wajib diberikan.</div>
                        <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#approval-reject-modal">Tolak pengajuan</button>
                    </div>

                    @if ($type === 'leave')
                        <div class="border rounded-3 p-3">
                            <div class="fw-semibold text-warning-emphasis mb-1">Minta revisi</div>
                            <div class="small text-secondary mb-3">Pemohon akan diminta memperbaiki pengajuan sebelum diproses kembali.</div>
                            <button type="button" class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#approval-revision-modal">Minta revisi</button>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    @if ($hasSupportingDocument && $canPreviewSupportingDocument)
        <div class="modal fade" id="approval-supporting-document-modal" tabindex="-1" aria-labelledby="approval-supporting-document-title" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approval-supporting-document-title">Dokumen pendukung · {{ $model->request_number }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-0" style="min-height: 70vh">
                        @if ($supportingMimeType === 'application/pdf')
                            <iframe src="{{ route('workspace.hris.supporting-document', ['type' => $type, 'id' => $model->getKey(), 'step' => $step, 'preview' => 1]) }}" title="Dokumen pendukung" style="width: 100%; height: 70vh; border: 0"></iframe>
                        @else
                            <img src="{{ route('workspace.hris.supporting-document', ['type' => $type, 'id' => $model->getKey(), 'step' => $step, 'preview' => 1]) }}" alt="Dokumen pendukung" class="img-fluid d-block mx-auto" style="max-height: 70vh">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="approval-reject-modal" tabindex="-1" aria-labelledby="approval-reject-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="approval-reject-title">Tolak pengajuan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <form method="POST" action="{{ route('workspace.hris.approve', [$type, $model->getKey(), $step]) }}">
                    @csrf
                    <div class="modal-body"><p class="text-secondary">Berikan alasan penolakan untuk {{ $model->employee?->full_name ?? 'pemohon' }}.</p><label class="form-label">Alasan penolakan <span class="text-danger">*</span></label><textarea name="comment" class="form-control" rows="4" required maxlength="2000" placeholder="Jelaskan alasan penolakan."></textarea><input type="hidden" name="action" value="reject"></div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger">Tolak pengajuan</button></div>
                </form>
            </div>
        </div>
    </div>

    @if ($type === 'leave')
        <div class="modal fade" id="approval-revision-modal" tabindex="-1" aria-labelledby="approval-revision-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title" id="approval-revision-title">Minta revisi pengajuan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                    <form method="POST" action="{{ route('workspace.hris.approve', [$type, $model->getKey(), $step]) }}">
                        @csrf
                        <div class="modal-body">
                            <p class="text-secondary mb-3">Pilih jenis revisi dan jelaskan perubahan yang harus dilakukan pemohon.</p>
                            <div class="workspace-revision-info mb-3">
                                <button type="button" class="workspace-revision-info-toggle" data-bs-toggle="collapse" data-bs-target="#approval-revision-guide" aria-expanded="false" aria-controls="approval-revision-guide"><span class="workspace-revision-info-title mb-0">Panduan jenis revisi</span><span class="workspace-revision-info-chevron" aria-hidden="true">⌄</span></button>
                                <div id="approval-revision-guide" class="collapse"><div class="workspace-revision-info-content"><div class="workspace-revision-info-item"><strong>Revisi substansi</strong><span>Perubahan tanggal, jumlah hari, jenis, atau isi utama. Pengajuan akan kembali meminta persetujuan Atasan.</span></div><div class="workspace-revision-info-item"><strong>Revisi administrasi</strong><span>Perbaikan dokumen atau keterangan tanpa mengubah isi utama. Pengajuan kembali ke tahap yang meminta revisi.</span></div></div></div>
                            </div>
                            <label class="form-label">Jenis revisi <span class="text-danger">*</span></label><select name="revision_type" class="form-select mb-3" required><option value="substantive">Revisi substansi</option><option value="administrative">Revisi administrasi</option></select>
                            <label class="form-label">Catatan revisi <span class="text-danger">*</span></label><textarea name="comment" class="form-control" rows="4" required maxlength="2000" placeholder="Jelaskan perubahan yang harus dilakukan pemohon."></textarea><input type="hidden" name="action" value="request_revision">
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-warning">Kirim permintaan revisi</button></div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
