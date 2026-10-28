@extends('layouts.app', ['title' => 'Audit trail'])

@section('content')
<div class="workspace-page-heading d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <span class="workspace-form-kicker">Core</span>
        <h1 class="h3 mb-1">Audit trail</h1>
        <p class="text-secondary mb-0">Riwayat aktivitas pengguna dan perubahan data aplikasi.</p>
    </div>
</div>

<form method="GET" class="card border-0 shadow-sm mb-3 workspace-audit-filter-card" data-filter-panel>
    <div class="card-body">
        <div class="workspace-audit-filter-heading">
            <div><span class="workspace-form-kicker">Penyaringan</span><strong class="d-block">Cari aktivitas</strong></div>
        </div>
        <div class="workspace-audit-filter-grid">
            <div class="workspace-audit-filter-search"><label class="form-label" for="audit-q">Pencarian</label><input id="audit-q" name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Aksi, route, atau IP address"></div>
            <div><label class="form-label" for="audit-module">Modul</label><select id="audit-module" name="module" class="form-select"><option value="">Semua modul</option>@foreach($modules as $module)<option value="{{ $module }}" @selected($filters['module'] === $module)>{{ str($module)->headline() }}</option>@endforeach</select></div>
            <div><label class="form-label" for="audit-actor">Pengguna</label><select id="audit-actor" name="actor_id" class="form-select"><option value="">Semua pengguna</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string) $filters['actor_id'] === (string) $user->id)>{{ $user->name }}</option>@endforeach</select></div>
            <div><label class="form-label" for="audit-from">Dari tanggal</label><input id="audit-from" type="date" name="from" value="{{ $filters['from'] }}" class="form-control"></div>
            <div><label class="form-label" for="audit-until">Sampai tanggal</label><input id="audit-until" type="date" name="until" value="{{ $filters['until'] }}" class="form-control"></div>
            <div><label class="form-label" for="audit-per-page">Baris per halaman</label><select id="audit-per-page" name="per_page" class="form-select"><option value="10" @selected($perPage === 10)>10</option><option value="25" @selected($perPage === 25)>25</option><option value="50" @selected($perPage === 50)>50</option><option value="100" @selected($perPage === 100)>100</option></select></div>
            <div class="workspace-audit-filter-submit"><div class="workspace-filter-actions workspace-audit-filter-actions"><a href="{{ route('workspace.core.audit-log') }}" class="btn btn-sm btn-outline-secondary">Reset</a><button class="btn btn-primary btn-sm" type="submit">Terapkan</button></div></div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Waktu</th><th>Pengguna</th><th>Modul / aksi</th><th>Perangkat</th><th>IP</th><th class="text-end">Detail</th></tr></thead>
            <tbody>
            @forelse($events as $event)
                <tr>
                    <td class="text-nowrap">{{ $event->created_at?->format('d/m/Y H:i:s') }}</td>
                    <td><strong>{{ $event->actor?->name ?: 'Sistem / anonim' }}</strong><small class="d-block text-secondary">{{ $event->actor?->email ?: '—' }}</small></td>
                    <td><span class="badge text-bg-light border">{{ str($event->module ?: 'system')->headline() }}</span><div class="small mt-1">{{ $event->action }}</div></td>
                    <td><span class="small">{{ $event->device ?: '—' }}</span><small class="d-block text-secondary text-truncate" style="max-width:180px" title="{{ $event->user_agent }}">{{ $event->user_agent ?: '—' }}</small></td>
                    <td class="text-nowrap">{{ $event->ip_address ?: '—' }}</td>
                    <td class="workspace-table-actions text-end"><button type="button" class="btn btn-sm btn-outline-primary workspace-audit-detail-button" data-bs-toggle="modal" data-bs-target="#audit-detail-{{ $event->id }}">Lihat detail</button></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-5">Belum ada audit event yang sesuai.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white workspace-list-pagination"><div class="workspace-pagination-summary">Menampilkan <strong>{{ $events->firstItem() ?? 0 }}</strong>&ndash;<strong>{{ $events->lastItem() ?? 0 }}</strong> dari <strong>{{ $events->total() }}</strong> event</div>{{ $events->onEachSide(1)->links() }}</div>
</div>

@foreach($events as $event)
    @php
        $oldJson = json_encode($event->old_values ?? new \stdClass(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $newJson = json_encode($event->new_values ?? new \stdClass(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $metadataJson = json_encode($event->metadata ?? new \stdClass(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    @endphp
    <div class="modal fade" id="audit-detail-{{ $event->id }}" tabindex="-1" aria-labelledby="audit-detail-label-{{ $event->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content workspace-audit-modal">
                <div class="modal-header">
                    <div><span class="workspace-form-kicker">Detail audit</span><h2 class="modal-title fs-5" id="audit-detail-label-{{ $event->id }}">{{ str($event->action)->headline() }}</h2></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="workspace-audit-detail-meta">
                        <div><small>Pengguna</small><strong>{{ $event->actor?->name ?: 'Sistem / anonim' }}</strong><span>{{ $event->actor?->email ?: '—' }}</span></div>
                        <div><small>Waktu</small><strong>{{ $event->created_at?->format('d/m/Y H:i:s') }}</strong><span>{{ $event->module ?: 'system' }}</span></div>
                        <div><small>IP / device</small><strong>{{ $event->ip_address ?: '—' }}</strong><span>{{ $event->device ?: '—' }}</span></div>
                        <div><small>Route</small><code>{{ $event->http_method ?: '—' }} {{ $event->route_name ?: '—' }}</code></div>
                        <div class="workspace-audit-detail-subject"><small>Subject</small><code>{{ $event->subject_type ?: '—' }}{{ $event->subject_id ? ' #'.$event->subject_id : '' }}</code></div>
                    </div>
                    <div class="workspace-audit-json-grid">
                        <section><div class="workspace-audit-json-heading"><h3>Sebelum</h3><span>old_values</span></div><pre class="workspace-audit-json">{{ $oldJson }}</pre></section>
                        <section><div class="workspace-audit-json-heading"><h3>Sesudah</h3><span>new_values</span></div><pre class="workspace-audit-json">{{ $newJson }}</pre></section>
                    </div>
                    <section class="workspace-audit-metadata-block"><div class="workspace-audit-json-heading"><h3>Konteks aksi</h3><span>metadata audit</span></div>@if($event->metadata)<pre class="workspace-audit-json">{{ $metadataJson }}</pre>@else<div class="workspace-audit-empty">Tidak ada konteks tambahan untuk aksi ini.</div>@endif</section>
                    <details class="workspace-audit-user-agent"><summary>User agent lengkap</summary><p>{{ $event->user_agent ?: '—' }}</p></details>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
