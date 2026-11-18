@extends('layouts.app', ['title' => 'Queue & Job', 'wide' => true])

@section('content')
@php
    $statusLabels = ['queued' => 'Dalam antrean', 'processing' => 'Diproses', 'succeeded' => 'Berhasil', 'failed' => 'Gagal'];
    $statusStyles = ['queued' => 'queue', 'processing' => 'processing', 'succeeded' => 'success', 'failed' => 'failed'];
    $sortUrl = fn (string $column) => route('workspace.core.queue-jobs', array_merge($filters, [
        'per_page' => $perPage,
        'sort' => $column,
        'direction' => $sort === $column && $direction === 'asc' ? 'desc' : 'asc',
    ]));
@endphp

<section class="queue-monitor-page">
    <header class="queue-monitor-header">
        <div class="queue-monitor-heading">
            <span class="queue-monitor-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="5" rx="1.5"/><rect x="4" y="15" width="16" height="5" rx="1.5"/><path d="M8 6.5h.01M8 17.5h.01M12 9v6"/></svg></span>
            <div><div class="workspace-eyebrow">PENGATURAN · MONITORING</div><h1>Queue &amp; Job</h1><p>Pantau proses latar belakang aplikasi.</p></div>
        </div>
        <div class="queue-monitor-live"><span class="queue-live-dot"></span><div><small>Queue aktif</small><strong>{{ $queueName }}</strong></div><span class="queue-live-count">{{ number_format($queueSize) }} antrean</span></div>
    </header>

    <div class="queue-kpi-grid">
        @foreach($statusLabels as $key => $label)
            <a class="queue-kpi-card queue-kpi-{{ $statusStyles[$key] }} {{ $filters['status'] === $key ? 'is-selected' : '' }}" href="{{ route('workspace.core.queue-jobs', array_merge(request()->query(), ['status' => $filters['status'] === $key ? '' : $key, 'page' => 1])) }}">
                <span class="queue-kpi-mark" aria-hidden="true">{{ ['queued' => '…', 'processing' => '↻', 'succeeded' => '✓', 'failed' => '!'][$key] }}</span>
                <span class="queue-kpi-copy"><small>{{ $label }}</small><strong>{{ number_format((int) ($counts[$key] ?? 0)) }}</strong></span>
                <span class="queue-kpi-caption">log job <span aria-hidden="true">↗</span></span>
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('workspace.core.queue-jobs') }}" class="queue-filter-panel">
        <input type="hidden" name="sort" value="{{ $sort }}"><input type="hidden" name="direction" value="{{ $direction }}">
        <div class="queue-filter-title"><div><span class="queue-filter-kicker">PENCARIAN</span><strong>Filter log</strong></div><span class="queue-result-count">{{ number_format($totalCount) }} hasil</span></div>
        <div class="queue-filter-fields">
            <label class="queue-filter-search"><span>Cari job atau ID</span><input type="search" name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Nama job, ID job..."></label>
            <label><span>Queue</span><select name="queue" class="form-select"><option value="">Semua queue</option>@foreach($queues as $queue)<option value="{{ $queue }}" @selected($filters['queue'] === $queue)>{{ $queue }}</option>@endforeach</select></label>
            <label><span>Status</span><select name="status" class="form-select"><option value="">Semua status</option>@foreach($statusLabels as $key => $label)<option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>@endforeach</select></label>
            <label><span>Dari tanggal</span><input type="date" name="from" value="{{ $filters['from'] }}" class="form-control"></label>
            <label><span>Sampai tanggal</span><input type="date" name="until" value="{{ $filters['until'] }}" class="form-control"></label>
            <label class="queue-filter-limit"><span>Tampilan</span><select name="per_page" class="form-select">@foreach([10, 25, 50, 100] as $size)<option value="{{ $size }}" @selected($perPage === $size)>{{ $size }} baris</option>@endforeach</select></label>
            <div class="queue-filter-actions"><a href="{{ route('workspace.core.queue-jobs') }}" class="btn btn-sm btn-light">Reset</a><button type="submit" class="btn btn-sm btn-primary">Terapkan filter</button></div>
        </div>
    </form>

    <section class="queue-log-panel">
        <div class="queue-log-heading"><div><span class="queue-filter-kicker">AKTIVITAS</span><h2>Log eksekusi job</h2><p>Riwayat eksekusi dicatat tanpa menyimpan payload sensitif.</p></div>@can('view_email_queue')<a class="btn btn-sm btn-outline-primary" href="{{ route('workspace.core.email-queue') }}">Antrean email <span aria-hidden="true">↗</span></a>@endcan</div>
        <div class="table-responsive">
            <table class="table queue-jobs-table align-middle mb-0">
                <thead><tr>
                    @foreach(['job_name' => 'Job', 'queue_name' => 'Queue', 'status' => 'Status', 'queued_at' => 'Dijadwalkan', 'started_at' => 'Mulai', 'finished_at' => 'Selesai', 'attempts' => 'Percobaan'] as $column => $label)
                        <th><a href="{{ $sortUrl($column) }}">{{ $label }} @if($sort === $column)<span class="queue-sort-indicator">{{ $direction === 'asc' ? '↑' : '↓' }}</span>@else<span class="queue-sort-muted">↕</span>@endif</a></th>
                    @endforeach
                    <th class="text-end">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($jobs as $job)
                        <tr>
                            <td><strong class="queue-job-name">{{ class_basename($job->job_name) }}</strong><small class="queue-job-class">{{ $job->job_name }}</small><small class="queue-job-id">ID {{ $job->job_id ?: '—' }}</small></td>
                            <td><span class="queue-name-pill">{{ $job->queue_name ?: '—' }}</span></td>
                            <td><span class="queue-status-pill queue-status-{{ $statusStyles[$job->status] ?? 'queue' }}"><i></i>{{ $statusLabels[$job->status] ?? ucfirst($job->status) }}</span></td>
                            <td class="text-nowrap">{{ $job->queued_at?->format('d/m/Y H:i:s') ?: '—' }}</td>
                            <td class="text-nowrap">{{ $job->started_at?->format('d/m/Y H:i:s') ?: '—' }}</td>
                            <td class="text-nowrap">{{ $job->finished_at?->format('d/m/Y H:i:s') ?: '—' }}</td>
                            <td>{{ $job->attempts }}</td>
                            <td class="queue-action-cell">
                                <div class="dropdown">
                                    <button class="queue-action-toggle" type="button" data-queue-action-toggle data-menu-id="queue-job-action-menu-{{ $job->id }}" aria-controls="queue-job-action-menu-{{ $job->id }}" aria-haspopup="true" aria-expanded="false" aria-label="Aksi untuk {{ class_basename($job->job_name) }}"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg></button>
                                    <div id="queue-job-action-menu-{{ $job->id }}" class="dropdown-menu dropdown-menu-end queue-action-menu" role="menu">
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#queueJobDetailModal"
                                            data-job-name="{{ $job->job_name }}" data-job-id="{{ $job->job_id }}" data-job-queue="{{ $job->queue_name }}"
                                            data-job-connection="{{ $job->connection_name }}" data-job-status="{{ $statusLabels[$job->status] ?? ucfirst($job->status) }}"
                                            data-job-attempts="{{ $job->attempts }}" data-job-queued="{{ $job->queued_at?->format('d/m/Y H:i:s') ?: '—' }}"
                                            data-job-started="{{ $job->started_at?->format('d/m/Y H:i:s') ?: '—' }}" data-job-finished="{{ $job->finished_at?->format('d/m/Y H:i:s') ?: '—' }}"
                                            data-job-error="{{ $job->error_message ?: '' }}"><span class="queue-action-icon">↗</span>Detail job</button>
                                        @if($job->status === 'failed' && $job->attempts >= ($job->max_attempts ?? (int) config('workers.max_attempts', 3)))
                                            @can('retry_queue_jobs')
                                                <div class="dropdown-divider"></div>
                                                <form method="POST" action="{{ route('workspace.core.queue-jobs.retry', $job) }}" data-confirm="Job {{ class_basename($job->job_name) }} akan dimasukkan kembali ke antrean dan dijalankan ulang. Lanjutkan?" data-confirm-action="Antrikan ulang">@csrf<button type="submit" class="dropdown-item queue-action-retry"><span class="queue-action-icon">↻</span>Antrikan ulang</button></form>
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
                        <tr><td colspan="8"><div class="queue-empty-state"><span>⌕</span><strong>Tidak ada log yang cocok</strong><small>Ubah filter atau reset pencarian untuk melihat log lainnya.</small></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <footer class="queue-log-footer"><span>Menampilkan {{ $jobs->firstItem() ?? 0 }}–{{ $jobs->lastItem() ?? 0 }} dari {{ number_format($jobs->total()) }} log</span>{{ $jobs->links() }}</footer>
    </section>
</section>

<div class="modal fade queue-job-detail-modal" id="queueJobDetailModal" tabindex="-1" aria-labelledby="queueJobDetailTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header"><div><span class="queue-filter-kicker">QUEUE &amp; JOB</span><h2 class="modal-title" id="queueJobDetailTitle">Detail job</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
        <div class="modal-body">
            <div class="queue-detail-hero"><span class="queue-monitor-icon"><svg viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="5" rx="1.5"/><rect x="4" y="15" width="16" height="5" rx="1.5"/><path d="M8 6.5h.01M8 17.5h.01M12 9v6"/></svg></span><div><small>Nama job</small><strong data-queue-job-field="name">—</strong><code data-queue-job-field="id">—</code></div><span class="queue-status-pill queue-status-queue" data-queue-job-field="status">—</span></div>
            <dl class="queue-detail-grid"><div><dt>Queue</dt><dd data-queue-job-field="queue">—</dd></div><div><dt>Koneksi</dt><dd data-queue-job-field="connection">—</dd></div><div><dt>Percobaan</dt><dd data-queue-job-field="attempts">—</dd></div><div><dt>Dijadwalkan</dt><dd data-queue-job-field="queued">—</dd></div><div><dt>Mulai</dt><dd data-queue-job-field="started">—</dd></div><div><dt>Selesai</dt><dd data-queue-job-field="finished">—</dd></div></dl>
            <section class="queue-detail-error"><span>Pesan error</span><pre data-queue-job-field="error">Tidak ada pesan error.</pre></section>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button></div>
    </div></div>
</div>
@endsection

@push('scripts')
@include('modules.partials.queue-action-menu-script')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('queueJobDetailModal');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', (event) => {
        const trigger = event.relatedTarget;
        const data = trigger?.dataset ?? {};
        for (const key of ['name', 'id', 'queue', 'connection', 'status', 'attempts', 'queued', 'started', 'finished']) {
            const target = modal.querySelector(`[data-queue-job-field="${key}"]`);
            if (target) target.textContent = data[`job${key[0].toUpperCase()}${key.slice(1)}`] || '—';
        }
        const error = modal.querySelector('[data-queue-job-field="error"]');
        if (error) error.textContent = data.jobError || 'Tidak ada pesan error.';
    });
});
</script>
@endpush
