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
            <section class="card border-0 shadow-sm mb-4 {{ $type === 'claim' ? 'approval-claim-card' : '' }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase text-secondary small fw-semibold">Pengajuan {{ $typeLabel }}</div>
                        <h2 class="h5 mb-0">{{ $model->employee?->full_name ?? '-' }}</h2>
                        <div class="small text-primary mt-1">Nomor pengajuan: {{ $model->request_number }}</div>
                    </div>
                    <span class="badge rounded-pill {{ in_array($step, ['hr', 'finance', 'final'], true) ? 'text-bg-info' : 'text-bg-warning' }}">Tahap {{ $stage }}</span>
                </div>
                <div class="card-body py-3">
                    <div class="row g-3 {{ $type === 'claim' ? 'approval-claim-fields' : '' }}">
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
                            <div class="col-md-6"><div class="small text-secondary">Divisi / unit</div><div class="fw-semibold">{{ $model->organization_unit_name ?: '-' }}</div></div>
                            <div class="col-md-6"><div class="small text-secondary">Jenis / lokasi</div><div class="fw-semibold">{{ $model->overtime_type === 'external_project' ? 'External / Project' : 'Internal' }} · {{ $model->work_location ?: '-' }}</div></div>
                            <div class="col-md-6"><div class="small text-secondary">Total durasi</div><div class="fw-semibold">{{ intdiv($model->duration_minutes, 60) }} jam {{ $model->duration_minutes % 60 }} menit</div></div>
                            <div class="col-12"><div class="small text-secondary">Alasan</div><div class="fw-semibold">{{ $model->reason ?: '-' }}</div></div>
                            <div class="col-12"><div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Tanggal</th><th>Jenis hari</th><th>Jam</th><th>Durasi</th><th>Pekerjaan</th></tr></thead><tbody>@foreach($model->entries as $entry)<tr><td>{{ $entry->work_date?->format('d/m/Y') }}</td><td><span class="badge {{ $entry->day_type === 'holiday' ? 'text-bg-danger' : 'text-bg-light' }}">{{ $entry->day_type === 'holiday' ? 'Hari Libur' : 'Hari Kerja' }}</span></td><td>{{ substr($entry->start_time,0,5) }}–{{ substr($entry->end_time,0,5) }}</td><td>{{ $entry->duration_minutes }} menit</td><td>{{ $entry->work_description }}</td></tr>@endforeach</tbody></table></div></div>
                        @else
                            <div class="col-12"><section class="approval-claim-purpose"><span>Keperluan</span><strong>{{ $model->purpose ?: 'Tidak ada keterangan keperluan.' }}</strong></section></div>
                            <div class="col-12"><div class="approval-claim-context">
                                <div class="approval-claim-context-item"><span>Jenis</span><strong>{{ $model->type === 'cash_advance' ? 'Cash advance' : 'Reimbursement' }}</strong></div>
                                <div class="approval-claim-context-item"><span>Cakupan</span><strong>{{ $model->expense_scope === 'external_project' ? 'Project / client' : 'Operasional internal' }}</strong></div>
                                @if($model->expense_scope === 'external_project')
                                    <div class="approval-claim-context-item approval-claim-context-project"><span>Project / referensi</span><strong>{{ $model->reference_name_snapshot ?: $model->project?->name ?: '—' }}</strong></div>
                                    <div class="approval-claim-context-item"><span>Nomor PO</span><strong>{{ $model->po_number ?: '—' }}</strong></div>
                                @endif
                                <div class="approval-claim-context-item"><span>Kategori</span><strong>{{ $model->category ?: '—' }}</strong></div>
                                <div class="approval-claim-context-item approval-claim-context-total"><span>Total</span><strong>{{ $model->currency ?? 'IDR' }} {{ $model->amount !== null ? number_format((float) $model->amount, 2, ',', '.') : '—' }}</strong></div>
                            </div></div>
                            @if($model->relationLoaded('expenseItems') && $model->expenseItems->isNotEmpty())
                                <div class="col-12"><div class="approval-claim-items-heading"><div><span>Rincian biaya</span><small>{{ $model->expenseItems->count() }} item pengeluaran</small></div></div><div class="table-responsive approval-claim-items-table"><table class="table align-middle mb-0"><thead><tr><th>#</th><th>Tanggal nota</th><th>Kategori & deskripsi</th><th class="text-end">Nominal</th><th class="text-end">Bukti</th></tr></thead><tbody>@foreach($model->expenseItems as $item)<tr><td><span class="approval-claim-line-number">{{ $item->line_number }}</span></td><td class="text-nowrap">{{ $item->receipt_date?->format('d/m/Y') ?: '—' }}</td><td><strong class="d-block">{{ $item->category ?: 'Pengeluaran' }}</strong><small class="text-secondary">{{ $item->description ?: 'Tanpa deskripsi' }}</small></td><td class="text-end text-nowrap fw-semibold">{{ $item->currency }} {{ number_format((float) $item->amount, 2, ',', '.') }}</td><td class="text-end">@if(filled($item->attachment_object_key))<button type="button" class="btn btn-sm btn-outline-primary approval-claim-preview-trigger" data-bs-toggle="modal" data-bs-target="#approval-claim-receipt-modal" data-preview-url="{{ route('workspace.hris.claims.expense-items.attachment', ['claim' => $model, 'item' => $item, 'preview' => 1]) }}" data-line="{{ $item->line_number }}" data-receipt-date="{{ $item->receipt_date?->format('d/m/Y') ?: '—' }}" data-activity-date="{{ $item->activity_date?->format('d/m/Y') ?: '—' }}" data-category="{{ $item->category }}" data-description="{{ $item->description }}" data-currency="{{ $item->currency }}" data-amount="{{ number_format((float) $item->amount, 2, ',', '.') }}" data-reference="{{ $item->attachment_reference ?: '—' }}">Preview bukti</button>@else<span class="text-secondary">—</span>@endif</td></tr>@endforeach</tbody></table></div></div>
                            @endif
                            <div class="col-12">
                                    <div class="accordion approval-claim-history" id="approval-claim-history-accordion">
                                        <div class="accordion-item border rounded-3 overflow-hidden">
                                            <h3 class="accordion-header" id="approval-claim-history-heading">
                                                <button class="accordion-button collapsed approval-claim-history-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#approval-claim-history-panel" aria-expanded="false" aria-controls="approval-claim-history-panel">
                                                    <span class="approval-claim-history-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 8v4l2.5 1.5M20 12a8 8 0 1 1-2.34-5.66M20 4v5h-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                                    <span class="approval-claim-history-copy"><strong>Riwayat pengajuan</strong><small>{{ $approvalTimeline->count() }} aktivitas <span aria-hidden="true">·</span> timeline approval dan revisi</small></span>
                                                </button>
                                            </h3>
                                            <div id="approval-claim-history-panel" class="accordion-collapse collapse" aria-labelledby="approval-claim-history-heading" data-bs-parent="#approval-claim-history-accordion">
                                                <div class="accordion-body approval-claim-history-body">
                                                    <div class="workspace-detail-timeline">
                                                        @forelse ($approvalTimeline as $entry)
                                                            <div class="workspace-detail-timeline-item {{ $entry['state'] }}"><span class="workspace-detail-timeline-dot">{{ $entry['action'] === 'reject' ? '×' : (in_array($entry['action'], ['request_revision', 'resubmitted'], true) ? '↻' : '✓') }}</span><div><strong>{{ $entry['label'] }} · {{ $entry['step'] }}</strong><small>{{ $entry['actor'] }} · {{ $entry['at']?->format('d/m/Y H:i') }} WIB</small>@if($entry['comment'])<div class="claim-detail-history-note">{{ $entry['comment'] }}</div>@endif</div></div>
                                                        @empty
                                                            <div class="text-secondary small">Belum ada aktivitas pengajuan.</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        @endif

                        @if ($type !== 'claim')
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
                        @endif
                    </div>
                </div>
            </section>
            @if (in_array($type, ['leave', 'overtime'], true))
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

                    @if (in_array($type, ['leave', 'overtime'], true) || ($type === 'claim' && in_array($step, ['manager', 'finance'], true)))
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

    @if ($type === 'claim')
        <div class="modal fade" id="approval-claim-receipt-modal" tabindex="-1" aria-labelledby="approval-claim-receipt-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl claim-verification-modal">
                <div class="modal-content">
                    <div class="modal-header"><div><span class="approval-claim-modal-kicker">Reimbursement · {{ $model->request_number }}</span><h2 class="modal-title fs-5" id="approval-claim-receipt-title">Preview Bukti Dokumen</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                    <div class="modal-body"><div class="claim-verification-grid approval-claim-preview-grid">
                        <section class="claim-verification-details"><span class="approval-claim-preview-eyebrow">RINCIAN PENGELUARAN</span><h3 class="h6 mt-2 mb-3" data-approval-claim-item-title>Pilih bukti nota</h3><dl class="claim-verification-list" data-approval-claim-item-fields><dt class="claim-verification-label">Dokumen</dt><dd class="claim-verification-value">Pilih tombol Preview bukti pada rincian biaya.</dd></dl></section>
                        <section class="claim-verification-document"><div class="d-flex justify-content-between align-items-center gap-2 mb-2"><h3 class="h6 mb-0">Viewer dokumen</h3><a class="btn btn-sm btn-outline-secondary" data-approval-claim-download target="_blank" rel="noopener">Buka file</a></div><div class="claim-image-viewer" data-claim-image-viewer><div class="claim-image-zoom-toolbar" data-claim-image-zoom-toolbar hidden><button type="button" class="btn btn-sm btn-outline-secondary" data-claim-zoom-out aria-label="Perkecil gambar">−</button><span data-claim-zoom-level aria-live="polite">100%</span><button type="button" class="btn btn-sm btn-outline-secondary" data-claim-zoom-in aria-label="Perbesar gambar">+</button><button type="button" class="btn btn-sm btn-outline-secondary" data-claim-zoom-reset>Sesuaikan</button></div><iframe class="claim-attachment-preview-frame" data-approval-claim-frame title="Preview Bukti Dokumen" referrerpolicy="no-referrer"></iframe></div></section>
                    </div></div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('approval-claim-receipt-modal');
                if (!modal) return;
                const frame = modal.querySelector('[data-approval-claim-frame]');
                const download = modal.querySelector('[data-approval-claim-download]');
                const title = modal.querySelector('[data-approval-claim-item-title]');
                const fields = modal.querySelector('[data-approval-claim-item-fields]');
                document.querySelectorAll('.approval-claim-preview-trigger').forEach((trigger) => trigger.addEventListener('click', () => {
                    const url = trigger.dataset.previewUrl;
                    title.textContent = `Rincian biaya #${trigger.dataset.line}`;
                    const details = [['Tanggal nota', trigger.dataset.receiptDate], ['Tanggal kegiatan', trigger.dataset.activityDate], ['Kategori', trigger.dataset.category], ['Deskripsi', trigger.dataset.description], ['Nominal', `${trigger.dataset.currency} ${trigger.dataset.amount}`], ['Referensi nota', trigger.dataset.reference]];
                    fields.replaceChildren(...details.flatMap(([label, value]) => { const dt = document.createElement('dt'); dt.className = 'claim-verification-label'; dt.textContent = label; const dd = document.createElement('dd'); dd.className = 'claim-verification-value'; dd.textContent = value || '—'; return [dt, dd]; }));
                    frame.src = url;
                    download.href = trigger.dataset.downloadUrl || url;
                }));
                modal.addEventListener('hidden.bs.modal', () => { frame.src = 'about:blank'; download.removeAttribute('href'); });
            });
        </script>
    @endif

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
                            @include('modules.partials.image-zoom-viewer', ['src' => route('workspace.hris.supporting-document', ['type' => $type, 'id' => $model->getKey(), 'step' => $step, 'preview' => 1]), 'title' => 'Dokumen pendukung'])
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

    @if (in_array($type, ['leave', 'overtime'], true) || ($type === 'claim' && in_array($step, ['manager', 'finance'], true)))
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
                                <div id="approval-revision-guide" class="collapse"><div class="workspace-revision-info-content"><div class="workspace-revision-info-item"><strong>Revisi substansi</strong><span>{{ $type === 'claim' ? 'Perubahan keperluan, referensi pekerjaan, rincian biaya, kategori, atau nominal. Pengajuan kembali ke Atasan untuk diperiksa ulang.' : 'Perubahan tanggal, jumlah hari, jenis, atau isi utama. Pengajuan akan kembali meminta persetujuan Atasan.' }}</span></div><div class="workspace-revision-info-item"><strong>Revisi administrasi</strong><span>{{ $type === 'claim' ? 'Perbaikan dokumen bukti atau keterangan administratif. Pengajuan kembali ke tahap yang meminta revisi.' : 'Perbaikan dokumen atau keterangan tanpa mengubah isi utama. Pengajuan kembali ke tahap yang meminta revisi.' }}</span></div></div></div>
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
