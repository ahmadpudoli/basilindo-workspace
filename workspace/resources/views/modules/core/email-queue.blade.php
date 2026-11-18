@extends('layouts.app', ['title' => 'Antrean Email', 'wide' => true])

@section('content')
@php
    $statusLabels = ['queued' => 'Dalam antrean', 'processing' => 'Diproses', 'sent' => 'Terkirim', 'failed' => 'Gagal'];
    $statusStyles = ['queued' => 'queue', 'processing' => 'processing', 'sent' => 'success', 'failed' => 'failed'];
    $sortUrl = fn (string $column) => route('workspace.core.email-queue', array_merge($filters, ['per_page' => $perPage, 'sort' => $column, 'direction' => $sort === $column && $direction === 'asc' ? 'desc' : 'asc']));
@endphp

<section class="queue-monitor-page email-queue-page">
    <header class="queue-monitor-header">
        <div class="queue-monitor-heading">
            <span class="queue-monitor-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span>
            <div><div class="workspace-eyebrow">PENGATURAN · MONITORING</div><h1>Antrean Email</h1><p>Pantau status dan riwayat pengiriman email notifikasi.</p></div>
        </div>
        <div class="queue-monitor-live"><span class="queue-live-dot"></span><div><small>Total tercatat</small><strong>{{ number_format($totalCount) }} email</strong></div><span class="queue-live-count">{{ number_format((int) ($counts['queued'] ?? 0)) }} antrean</span></div>
    </header>

    <div class="queue-kpi-grid">
        @foreach($statusLabels as $key => $label)
            <a class="queue-kpi-card queue-kpi-{{ $statusStyles[$key] }} {{ $filters['status'] === $key ? 'is-selected' : '' }}" href="{{ route('workspace.core.email-queue', array_merge($filters, ['status' => $filters['status'] === $key ? '' : $key, 'page' => 1, 'per_page' => $perPage])) }}">
                <span class="queue-kpi-mark" aria-hidden="true">{{ ['queued' => '…', 'processing' => '↻', 'sent' => '✓', 'failed' => '!'][$key] }}</span>
                <span class="queue-kpi-copy"><small>{{ $label }}</small><strong>{{ number_format((int) ($counts[$key] ?? 0)) }}</strong></span>
                <span class="queue-kpi-caption">email <span aria-hidden="true">↗</span></span>
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('workspace.core.email-queue') }}" class="queue-filter-panel">
        <input type="hidden" name="sort" value="{{ $sort }}"><input type="hidden" name="direction" value="{{ $direction }}">
        <div class="queue-filter-title"><div><span class="queue-filter-kicker">PENCARIAN</span><strong>Filter antrean email</strong></div><span class="queue-result-count">{{ number_format($totalCount) }} hasil</span></div>
        <div class="queue-filter-fields email-queue-filter-fields">
            <label class="queue-filter-search"><span>Cari email</span><input type="search" name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Penerima, subjek, template..."></label>
            <label><span>Status</span><select name="status" class="form-select"><option value="">Semua status</option>@foreach($statusLabels as $key => $label)<option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>@endforeach</select></label>
            <label><span>Dari tanggal</span><input type="date" name="from" value="{{ $filters['from'] }}" class="form-control"></label>
            <label><span>Sampai tanggal</span><input type="date" name="until" value="{{ $filters['until'] }}" class="form-control"></label>
            <label class="queue-filter-limit"><span>Tampilan</span><select name="per_page" class="form-select">@foreach([10, 25, 50, 100] as $size)<option value="{{ $size }}" @selected($perPage === $size)>{{ $size }} baris</option>@endforeach</select></label>
            <div class="queue-filter-actions"><a href="{{ route('workspace.core.email-queue') }}" class="btn btn-sm btn-light">Reset</a><button type="submit" class="btn btn-sm btn-primary">Terapkan filter</button></div>
        </div>
    </form>

    <section class="queue-log-panel">
        <div class="queue-log-heading"><div><span class="queue-filter-kicker">AKTIVITAS</span><h2>Log pengiriman email</h2><p>Body email tersimpan terenkripsi dan tidak ditampilkan di halaman monitor.</p></div>@can('view_queue_jobs')<a class="btn btn-sm btn-outline-primary" href="{{ route('workspace.core.queue-jobs') }}">Log job umum <span aria-hidden="true">↗</span></a>@endcan</div>
        <div class="table-responsive">
            <table class="table queue-jobs-table email-queue-table align-middle mb-0">
                <thead><tr>
                    @foreach(['recipient' => 'Penerima', 'subject' => 'Subjek', 'template' => 'Template', 'status' => 'Status', 'attempts' => 'Percobaan', 'queued_at' => 'Masuk antrean', 'sent_at' => 'Terkirim'] as $column => $label)
                        <th><a href="{{ $sortUrl($column) }}">{{ $label }} @if($sort === $column)<span class="queue-sort-indicator">{{ $direction === 'asc' ? '↑' : '↓' }}</span>@else<span class="queue-sort-muted">↕</span>@endif</a></th>
                    @endforeach
                    <th class="text-end">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td><strong class="email-recipient">{{ $log->recipient }}</strong><small class="email-log-id">Email #{{ $log->id }}</small></td>
                            <td class="email-subject">{{ $log->subject }}</td>
                            <td><span class="queue-name-pill">{{ $log->template }}</span></td>
                            <td><span class="queue-status-pill queue-status-{{ $statusStyles[$log->status] ?? 'queue' }}"><i></i>{{ $statusLabels[$log->status] ?? ucfirst($log->status) }}</span></td>
                            <td>{{ $log->attempts }}</td>
                            <td class="text-nowrap">{{ $log->queued_at?->format('d/m/Y H:i:s') ?: '—' }}</td>
                            <td class="text-nowrap">{{ $log->sent_at?->format('d/m/Y H:i:s') ?: '—' }}</td>
                            <td class="queue-action-cell">
                                <div class="dropdown dropup">
                                    <button class="queue-action-toggle" type="button" data-queue-action-toggle data-menu-id="email-action-menu-{{ $log->id }}" aria-controls="email-action-menu-{{ $log->id }}" aria-haspopup="true" aria-expanded="false" aria-label="Aksi untuk email {{ $log->id }}"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg></button>
                                    <div id="email-action-menu-{{ $log->id }}" class="dropdown-menu dropdown-menu-end queue-action-menu" role="menu">
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#emailLogDetailModal"
                                            data-email-id="{{ $log->id }}" data-email-recipient="{{ $log->recipient }}" data-email-subject="{{ $log->subject }}"
                                            data-email-template="{{ $log->template }}" data-email-status="{{ $statusLabels[$log->status] ?? ucfirst($log->status) }}"
                                            data-email-attempts="{{ $log->attempts }}" data-email-queued="{{ $log->queued_at?->format('d/m/Y H:i:s') ?: '—' }}"
                                            data-email-sent="{{ $log->sent_at?->format('d/m/Y H:i:s') ?: '—' }}" data-email-error="{{ $log->error_message ?: '' }}">
                                            <span class="queue-action-icon">↗</span>Detail email
                                        </button>
                                        @if($log->status === 'failed' && $log->attempts >= \App\Modules\Core\Notifications\Jobs\SendEmailNotification::MAX_ATTEMPTS)
                                            @can('retry_queue_jobs')
                                                <div class="dropdown-divider"></div>
                                                <form method="POST" action="{{ route('workspace.core.email-queue.retry', $log) }}" data-confirm="Email ke {{ $log->recipient }} sudah gagal setelah {{ $log->attempts }} percobaan. Masukkan kembali ke antrean?" data-confirm-action="Antrikan ulang">
                                                    @csrf<button type="submit" class="dropdown-item queue-action-retry"><span class="queue-action-icon">↻</span>Antrikan ulang</button>
                                                </form>
                                            @else
                                                <div class="dropdown-divider"></div>
                                                <button type="button" class="dropdown-item queue-action-retry queue-action-retry-locked" disabled title="Hubungi administrator untuk meminta permission retry_queue_jobs."><span class="queue-action-icon">↻</span><span>Antrikan ulang<small>Perlu akses retry_queue_jobs</small></span></button>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="queue-empty-state"><span>✉</span><strong>Belum ada antrean email</strong><small>Ubah filter atau reset pencarian untuk melihat catatan lainnya.</small></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <footer class="queue-log-footer"><span>Menampilkan {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} dari {{ number_format($logs->total()) }} email</span>{{ $logs->links() }}</footer>
    </section>
</section>

<div class="modal fade queue-job-detail-modal" id="emailLogDetailModal" tabindex="-1" aria-labelledby="emailLogDetailTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header"><div><span class="queue-filter-kicker">ANTREAN EMAIL</span><h2 class="modal-title" id="emailLogDetailTitle">Detail email</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
        <div class="modal-body">
            <div class="queue-detail-hero"><span class="queue-monitor-icon"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span><div><small>Penerima</small><strong data-email-detail="recipient">—</strong><code data-email-detail="id">Email #—</code></div><span class="queue-status-pill queue-status-queue" data-email-detail="status">—</span></div>
            <dl class="queue-detail-grid"><div><dt>Subjek</dt><dd data-email-detail="subject">—</dd></div><div><dt>Template</dt><dd data-email-detail="template">—</dd></div><div><dt>Percobaan</dt><dd data-email-detail="attempts">—</dd></div><div><dt>Masuk antrean</dt><dd data-email-detail="queued">—</dd></div><div><dt>Terkirim</dt><dd data-email-detail="sent">—</dd></div></dl>
            <section class="queue-detail-error"><span>Pesan error</span><pre data-email-detail="error">Tidak ada pesan error.</pre></section>
            <p class="email-detail-privacy">Isi email tidak ditampilkan. Body tetap tersimpan dalam bentuk terenkripsi.</p>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button></div>
    </div></div>
</div>
@endsection

@push('scripts')
@include('modules.partials.queue-action-menu-script')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('emailLogDetailModal');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', (event) => {
        const data = event.relatedTarget?.dataset ?? {};
        for (const key of ['id', 'recipient', 'subject', 'template', 'status', 'attempts', 'queued', 'sent']) {
            const target = modal.querySelector(`[data-email-detail="${key}"]`);
            if (target) target.textContent = data[`email${key[0].toUpperCase()}${key.slice(1)}`] || '—';
        }
        const error = modal.querySelector('[data-email-detail="error"]');
        if (error) error.textContent = data.emailError || 'Tidak ada pesan error.';
    });
});
</script>
@endpush
