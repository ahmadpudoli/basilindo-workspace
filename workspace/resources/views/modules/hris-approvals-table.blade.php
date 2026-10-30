@extends('layouts.app', ['title' => 'Persetujuan HRIS'])

@section('content')
    <div class="mb-4">
        <div class="text-uppercase text-secondary small fw-semibold mb-2">Kehadiran &amp; claim</div>
        <h1 class="h3 mb-1">Persetujuan HRIS</h1>
        <p class="text-secondary mb-0">Semua pengajuan yang membutuhkan tindakan Anda ditampilkan dalam satu tabel.</p>
    </div>

    <form method="GET" class="card border-0 shadow-sm mb-3"><div class="card-body"><div class="row g-3 align-items-end"><div class="col-lg-4"><label class="form-label">Pencarian</label><input name="q" value="{{ $search }}" class="form-control" placeholder="Cari nama atau NIP pemohon"></div><div class="col-lg-2"><label class="form-label">Jenis</label><select name="type" class="form-select"><option value="">Semua jenis</option><option value="leave" @selected($typeFilter === 'leave')>Cuti & sakit</option><option value="overtime" @selected($typeFilter === 'overtime')>Lembur</option><option value="claim" @selected($typeFilter === 'claim')>Claim</option></select></div><div class="col-lg-2"><label class="form-label">Tahap</label><select name="step" class="form-select"><option value="">Semua tahap</option><option value="manager" @selected($stepFilter === 'manager')>Atasan</option><option value="hr" @selected($stepFilter === 'hr')>HR</option></select></div><div class="col-lg-2"><label class="form-label">Baris</label><select name="limit" class="form-select"><option value="10" @selected($perPage === 10)>10</option><option value="25" @selected($perPage === 25)>25</option><option value="50" @selected($perPage === 50)>50</option><option value="100" @selected($perPage === 100)>100</option></select></div><div class="col-lg-2 d-flex gap-2"><a href="{{ route('workspace.hris.approvals') }}" class="btn btn-outline-secondary flex-fill">Reset</a><button class="btn btn-primary flex-fill">Terapkan</button></div></div></div></form>

    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><strong>Antrian persetujuan</strong><div class="small text-secondary">Tolak dan minta revisi memerlukan alasan.</div></div>
            <span class="badge rounded-pill text-bg-light">{{ $approvalRows->total() }} pengajuan</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th><a class="workspace-sort-link {{ $sort === 'type' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'type', 'direction' => $sort === 'type' && $direction === 'asc' ? 'desc' : 'asc', 'page' => null]) }}">Jenis <span aria-hidden="true">{{ $sort === 'type' ? ($direction === 'asc' ? '↑' : '↓') : '↕' }}</span></a></th>
                        <th><a class="workspace-sort-link {{ $sort === 'employee' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'employee', 'direction' => $sort === 'employee' && $direction === 'asc' ? 'desc' : 'asc', 'page' => null]) }}">Pemohon <span aria-hidden="true">{{ $sort === 'employee' ? ($direction === 'asc' ? '↑' : '↓') : '↕' }}</span></a></th>
                        <th>Tanggal pengajuan</th>
                        <th>Tahap</th>
                        <th><a class="workspace-sort-link {{ $sort === 'created_at' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => $sort === 'created_at' && $direction === 'asc' ? 'desc' : 'asc', 'page' => null]) }}">Diajukan <span aria-hidden="true">{{ $sort === 'created_at' ? ($direction === 'asc' ? '↑' : '↓') : '↕' }}</span></a></th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($approvalRows as $row)
                        @php
                            $item = $row['item'];
                            $rowKey = $row['type'].'-'.$row['step'].'-'.$item->getKey();
                            $typeLabel = match ($row['type']) {
                                'leave' => $item->type === 'leave' ? 'Cuti' : 'Sakit',
                                'overtime' => 'Lembur',
                                default => $item->type === 'cash_advance' ? 'Cash advance' : 'Reimbursement',
                            };
                            $detail = match ($row['type']) {
                                'leave' => $item->date_list,
                                'overtime' => $item->work_date?->format('d/m/Y') ?? '-',
                                default => 'Pengajuan '.$typeLabel,
                            };
                        @endphp
                        <tr>
                            <td><span class="badge rounded-pill text-bg-light">{{ $typeLabel }}</span></td>
                            <td class="fw-semibold">{{ $item->employee?->full_name ?? '-' }}</td>
                            <td>
                                @if ($row['type'] === 'leave')
                                    <div class="workspace-leave-date-badges">
                                        @foreach ($item->dates as $date)
                                            <span class="badge rounded-pill">{{ $date->leave_date?->format('d/m/Y') ?? '-' }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    {{ $detail }}
                                @endif
                            </td>
                            <td><span class="badge rounded-pill {{ $row['step'] === 'hr' ? 'text-bg-info' : 'text-bg-warning' }}">{{ $row['stage'] }}</span></td>
                            <td>{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('workspace.hris.approval-detail', [$row['type'], $item->getKey(), $row['step']]) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                    <form method="POST" action="{{ route('workspace.hris.approve', [$row['type'], $item->getKey(), $row['step']]) }}" class="d-inline" data-confirm="Setujui pengajuan {{ $typeLabel }} dari {{ $item->employee?->full_name ?? 'pemohon' }}?" data-confirm-action="Setujui pengajuan" data-confirm-comment="optional">
                                        @csrf
                                        <button name="action" value="approve" class="btn btn-sm btn-primary">Setujui</button>
                                    </form>
                                    @if ($row['has_supporting_document'])
                                        @if ($row['can_preview_supporting_document'])
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#supporting-{{ $rowKey }}">Dokumen</button>
                                        @else
                                            <a href="{{ route('workspace.hris.supporting-document', ['type' => $row['type'], 'id' => $item->getKey(), 'step' => $row['step'], 'download' => 1]) }}" class="btn btn-sm btn-outline-secondary">Dokumen</a>
                                        @endif
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reject-{{ $rowKey }}">Tolak</button>
                                    @if ($row['type'] === 'leave')
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#revision-{{ $rowKey }}">Minta revisi</button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        @if ($row['has_supporting_document'] && $row['can_preview_supporting_document'])
                            <div class="modal fade" id="supporting-{{ $rowKey }}" tabindex="-1" aria-labelledby="supporting-title-{{ $rowKey }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="supporting-title-{{ $rowKey }}">Dokumen pendukung · {{ $item->employee?->full_name ?? '-' }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body p-0" style="min-height:70vh">@if ($row['supporting_mime_type'] === 'application/pdf')<iframe src="{{ route('workspace.hris.supporting-document', ['type' => $row['type'], 'id' => $item->getKey(), 'step' => $row['step'], 'preview' => 1]) }}" title="Dokumen pendukung" style="width:100%;height:70vh;border:0"></iframe>@else<img src="{{ route('workspace.hris.supporting-document', ['type' => $row['type'], 'id' => $item->getKey(), 'step' => $row['step'], 'preview' => 1]) }}" alt="Dokumen pendukung" class="img-fluid d-block mx-auto" style="max-height:70vh">@endif</div></div></div>
                            </div>
                        @endif

                        <div class="modal fade" id="detail-{{ $rowKey }}" tabindex="-1" aria-labelledby="detail-title-{{ $rowKey }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header"><div><div class="text-uppercase text-secondary small fw-semibold">Detail pengajuan</div><h5 class="modal-title" id="detail-title-{{ $rowKey }}">{{ $typeLabel }} · {{ $item->employee?->full_name ?? '-' }}</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6"><div class="small text-secondary">Tahap approval</div><div class="fw-semibold">{{ $row['stage'] }}</div></div>
                                            <div class="col-md-6"><div class="small text-secondary">Status</div><div class="fw-semibold">{{ $item->status_label ?? ucfirst(str_replace('_', ' ', $item->status ?? '-')) }}</div></div>
                                            <div class="col-md-6"><div class="small text-secondary">Tanggal pengajuan</div><div class="fw-semibold">
                                                @if ($row['type'] === 'leave')
                                                    <div class="workspace-leave-date-badges mt-1">
                                                        @foreach ($item->dates as $date)
                                                            <span class="badge rounded-pill">{{ $date->leave_date?->format('d/m/Y') ?? '-' }}</span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    {{ $detail }}
                                                @endif
                                            </div></div>
                                            @if ($row['type'] === 'leave')
                                                <div class="col-md-6"><div class="small text-secondary">Jumlah hari</div><div class="fw-semibold">{{ $item->total_days ?? '-' }} hari</div></div>
                                                <div class="col-12"><div class="small text-secondary">Alasan</div><div class="fw-semibold">{{ $item->reason ?: '-' }}</div></div>
                                            @elseif ($row['type'] === 'overtime')
                                                <div class="col-md-6"><div class="small text-secondary">Jam</div><div class="fw-semibold">{{ $item->start_time ?? '-' }} - {{ $item->end_time ?? '-' }}</div></div>
                                                <div class="col-md-6"><div class="small text-secondary">Durasi</div><div class="fw-semibold">{{ $item->duration_minutes ? $item->duration_minutes.' menit' : '-' }}</div></div>
                                                <div class="col-12"><div class="small text-secondary">Alasan</div><div class="fw-semibold">{{ $item->reason ?: '-' }}</div></div>
                                            @else
                                                <div class="col-md-6"><div class="small text-secondary">Kategori</div><div class="fw-semibold">{{ $item->category ?: '-' }}</div></div>
                                                <div class="col-md-6"><div class="small text-secondary">Nominal</div><div class="fw-semibold">{{ $item->currency ?? 'IDR' }} {{ $item->amount !== null ? number_format((float) $item->amount, 2, ',', '.') : '-' }}</div></div>
                                                <div class="col-12"><div class="small text-secondary">Keperluan</div><div class="fw-semibold">{{ $item->purpose ?: '-' }}</div></div>
                                            @endif
                                            <div class="col-12"><div class="small text-secondary">Diajukan pada</div><div class="fw-semibold">{{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}</div></div>
                                            <div class="col-12"><div class="small text-secondary">Dokumen pendukung</div>@if ($row['has_supporting_document'])@if ($row['can_preview_supporting_document'])<button type="button" class="btn btn-sm btn-outline-primary mt-1" data-bs-toggle="modal" data-bs-target="#supporting-{{ $rowKey }}">Preview dokumen pendukung</button>@else<a href="{{ route('workspace.hris.supporting-document', ['type' => $row['type'], 'id' => $item->getKey(), 'step' => $row['step'], 'download' => 1]) }}" class="btn btn-sm btn-outline-primary mt-1">Unduh dokumen pendukung</a>@endif@else<div class="text-secondary mt-1">Dokumen pendukung tidak tersedia atau belum diupload.</div>@endif</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button></div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="reject-{{ $rowKey }}" tabindex="-1" aria-labelledby="reject-title-{{ $rowKey }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header"><h5 class="modal-title" id="reject-title-{{ $rowKey }}">Tolak pengajuan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                                    <form method="POST" action="{{ route('workspace.hris.approve', [$row['type'], $item->getKey(), $row['step']]) }}">
                                        @csrf
                                        <div class="modal-body"><p class="text-secondary">Berikan alasan penolakan untuk {{ $item->employee?->full_name ?? 'pemohon' }}.</p><label class="form-label" for="reject-comment-{{ $rowKey }}">Alasan penolakan <span class="text-danger">*</span></label><textarea id="reject-comment-{{ $rowKey }}" name="comment" class="form-control" rows="4" required maxlength="2000"></textarea><input type="hidden" name="action" value="reject"></div>
                                        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger">Tolak pengajuan</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @if ($row['type'] === 'leave')
                            <div class="modal fade" id="revision-{{ $rowKey }}" tabindex="-1" aria-labelledby="revision-title-{{ $rowKey }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title" id="revision-title-{{ $rowKey }}">Minta revisi pengajuan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                                        <form id="revision-form-{{ $rowKey }}" method="POST" action="{{ route('workspace.hris.approve', [$row['type'], $item->getKey(), $row['step']]) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <p class="text-secondary mb-3">Pilih jenis revisi dan jelaskan perubahan yang harus dilakukan pemohon.</p>
                                                <div class="workspace-revision-info mb-3">
                                                    <button type="button" class="workspace-revision-info-toggle" data-bs-toggle="collapse" data-bs-target="#revision-guide-{{ $rowKey }}" aria-expanded="false" aria-controls="revision-guide-{{ $rowKey }}">
                                                        <span class="workspace-revision-info-title mb-0">Panduan jenis revisi</span><span class="workspace-revision-info-chevron" aria-hidden="true">⌄</span>
                                                    </button>
                                                    <div id="revision-guide-{{ $rowKey }}" class="collapse">
                                                        <div class="workspace-revision-info-content">
                                                            <div class="workspace-revision-info-item"><strong>Revisi substansi</strong><span>Perubahan tanggal, jumlah hari, jenis, atau isi utama. Pengajuan akan kembali meminta persetujuan Atasan.</span></div>
                                                            <div class="workspace-revision-info-item"><strong>Revisi administrasi</strong><span>Perbaikan dokumen atau keterangan tanpa mengubah isi utama. Pengajuan kembali ke tahap yang meminta revisi.</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <label class="form-label" for="revision-type-{{ $rowKey }}">Jenis revisi <span class="text-danger">*</span></label>
                                                <select id="revision-type-{{ $rowKey }}" name="revision_type" form="revision-form-{{ $rowKey }}" class="form-select mb-3" required><option value="substantive">Revisi substansi</option><option value="administrative">Revisi administrasi</option></select>
                                                <label class="form-label" for="revision-comment-{{ $rowKey }}">Catatan revisi <span class="text-danger">*</span></label>
                                                <textarea id="revision-comment-{{ $rowKey }}" name="comment" form="revision-form-{{ $rowKey }}" class="form-control" rows="4" required minlength="1" maxlength="2000" placeholder="Jelaskan perubahan yang harus dilakukan pemohon."></textarea>
                                                <input type="hidden" name="action" value="request_revision" form="revision-form-{{ $rowKey }}">
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" form="revision-form-{{ $rowKey }}" class="btn btn-warning">Kirim permintaan revisi</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-5">Tidak ada pengajuan yang menunggu tindakan Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white workspace-list-pagination"><div class="workspace-pagination-summary">Menampilkan <strong>{{ $approvalRows->firstItem() ?? 0 }}</strong>–<strong>{{ $approvalRows->lastItem() ?? 0 }}</strong> dari <strong>{{ $approvalRows->total() }}</strong> pengajuan</div>{{ $approvalRows->onEachSide(1)->links() }}</div>
    </div>
@endsection
