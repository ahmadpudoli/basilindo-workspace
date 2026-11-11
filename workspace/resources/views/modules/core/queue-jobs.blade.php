@extends('layouts.app', ['title' => 'Queue & Job', 'wide' => true])

@section('content')
<div class="workspace-page-header d-flex justify-content-between align-items-end gap-3">
    <div><div class="workspace-eyebrow">PENGATURAN</div><h1>Queue & Job</h1><p class="text-secondary mb-0">Pantau antrean dan riwayat eksekusi job aplikasi.</p></div>
    <div class="text-end"><small class="text-secondary d-block">Queue aktif</small><strong>{{ $queueName }}</strong><span class="badge text-bg-primary ms-2">{{ $queueSize }} antrean</span></div>
</div>
<div class="row g-3 mb-4">
    @foreach(['queued' => 'Dalam antrean', 'processing' => 'Diproses', 'succeeded' => 'Berhasil', 'failed' => 'Gagal'] as $key => $label)
    <div class="col-md-3"><a class="workspace-stat-card text-decoration-none" href="{{ route('workspace.core.queue-jobs', ['status' => $key]) }}"><small>{{ $label }}</small><strong>{{ number_format((int) ($counts[$key] ?? 0)) }}</strong><span>log aplikasi</span></a></div>
    @endforeach
</div>
<div class="workspace-card">
    <div class="workspace-card-header d-flex justify-content-between align-items-center gap-3"><div><h2>Log eksekusi job</h2><p class="text-secondary mb-0">Job berhasil dan gagal dicatat tanpa menyimpan payload sensitif.</p></div><a class="btn btn-sm btn-outline-secondary" href="{{ route('workspace.core.queue-jobs') }}">Semua status</a></div>
    <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Job</th><th>Queue</th><th>Status</th><th>Dijadwalkan</th><th>Mulai</th><th>Selesai</th><th>Percobaan</th><th>Error</th></tr></thead><tbody>
    @forelse($jobs as $job)<tr><td><code>{{ $job->job_name }}</code><small class="d-block text-secondary">ID: {{ $job->job_id ?: '-' }}</small></td><td>{{ $job->queue_name ?: '-' }}</td><td><span class="badge text-bg-{{ $job->status === 'succeeded' ? 'success' : ($job->status === 'failed' ? 'danger' : ($job->status === 'processing' ? 'warning' : 'secondary')) }}">{{ $job->status }}</span></td><td>{{ $job->queued_at?->format('d/m/Y H:i:s') ?: '-' }}</td><td>{{ $job->started_at?->format('d/m/Y H:i:s') ?: '-' }}</td><td>{{ $job->finished_at?->format('d/m/Y H:i:s') ?: '-' }}</td><td>{{ $job->attempts }}</td><td class="text-danger">{{ $job->error_message ?: '-' }}</td></tr>@empty<tr><td colspan="8" class="text-center text-secondary py-4">Belum ada log job.</td></tr>@endforelse
    </tbody></table></div>
    <div class="p-3">{{ $jobs->links() }}</div>
</div>
@endsection
