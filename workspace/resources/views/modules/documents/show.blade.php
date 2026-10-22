@extends('layouts.app', ['title' => 'Detail Dokumen'])

@section('content')
<div class="workspace-page-heading d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><span class="workspace-form-kicker">Document Management</span><h1 class="h3 mb-1">Detail dokumen</h1><p class="text-secondary mb-0">Lihat informasi dokumen dan riwayat aktivitasnya.</p></div>
    <div class="d-flex flex-wrap gap-2"><a href="{{ route('workspace.documents.list') }}" class="btn btn-sm btn-outline-secondary">Kembali ke daftar</a>@can('update', $document)<a href="{{ route('workspace.documents.edit', $document) }}" class="btn btn-sm btn-primary">Edit dokumen</a>@endcan</div>
</div>

<div class="workspace-document-edit-layout workspace-document-detail-layout">
    <main class="workspace-document-edit-main">
        <section class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4 p-xl-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4"><div><span class="workspace-form-kicker">Informasi utama</span><h2 class="h4 mb-1">{{ $document->title }}</h2><p class="text-secondary mb-0">{{ $document->original_filename ?: 'File tidak memiliki nama asli.' }}</p></div><span class="badge text-bg-success">{{ str($document->status ?: 'ready')->headline() }}</span></div>
                <div class="row g-3 workspace-document-detail-grid">
                    <div class="col-md-6"><span>Perusahaan internal</span><strong>{{ $document->company?->name ?: '—' }}</strong></div>
                    <div class="col-md-6"><span>Jenis dokumen</span><strong>{{ $document->documentType?->name ?: '—' }}</strong></div>
                    <div class="col-md-6"><span>Tanggal dokumen</span><strong>{{ $document->document_date?->format('d/m/Y') ?: '—' }}</strong></div>
                    <div class="col-md-6"><span>Nomor referensi</span><strong>{{ $document->reference_number ?: '—' }}</strong></div>
                    <div class="col-md-6"><span>Perusahaan client/vendor</span><strong>{{ $document->counterpartyCompany?->name ?: '—' }}</strong></div>
                    <div class="col-md-6"><span>Project</span><strong>{{ $document->project?->name ?: '—' }}</strong></div>
                    <div class="col-md-6"><span>Mata uang</span><strong>{{ $document->currency ?: '—' }}</strong></div>
                    <div class="col-md-6"><span>Nominal</span><strong>{{ $document->amount !== null ? \App\Modules\DocumentManagement\Application\Services\Documents\DocumentMetadataValueNormalizer::formatIndonesian($document->amount) : '—' }}</strong></div>
                </div>
                <div class="workspace-document-detail-block"><span>Tag</span><div class="d-flex flex-wrap gap-1 mt-2">@forelse($document->tags ?? [] as $tag)<span class="badge text-bg-light border">{{ $tag }}</span>@empty<span class="text-secondary">—</span>@endforelse</div></div>
                <div class="workspace-document-detail-block"><span>Deskripsi</span><p class="mb-0 mt-2 text-secondary">{{ $document->description ?: 'Tidak ada deskripsi.' }}</p></div>
            </div>
        </section>

        <section class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-2"><div><span class="workspace-form-kicker">Lampiran</span><strong class="d-block">File dokumen</strong></div>@if($document->currentVersion)<a href="{{ route('documents.preview', $document) }}" class="btn btn-sm btn-outline-primary" data-workspace-preview data-preview-url="{{ route('documents.preview', $document) }}" data-preview-type="{{ $document->currentVersion->mime_type }}" data-preview-title="{{ $document->title }}">Preview file</a>@endif</div>
            <div class="card-body pt-0"><div class="workspace-document-detail-file"><span class="workspace-document-file-icon">{{ strtoupper(pathinfo($document->original_filename ?? 'FILE', PATHINFO_EXTENSION) ?: 'FILE') }}</span><div><strong>{{ $document->original_filename ?: 'Nama file tidak tersedia' }}</strong><small class="d-block text-secondary">{{ $document->currentVersion?->mime_type ?: 'Format tidak tersedia' }} · {{ $document->currentVersion?->file_size ? number_format($document->currentVersion->file_size / 1024, 1).' KB' : 'Ukuran tidak tersedia' }}</small></div></div></div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0"><span class="workspace-form-kicker">Metadata tambahan</span><strong class="d-block">Field metadata</strong></div>
            <div class="table-responsive"><table class="table table-sm mb-0 align-middle"><thead><tr><th>Field</th><th>Nilai</th></tr></thead><tbody>@forelse($document->metadata ?? [] as $field => $value)<tr><td class="fw-semibold">{{ str($field)->replace('_', ' ')->headline() }}</td><td>{{ is_scalar($value) || $value === null ? ($value === null || $value === '' ? '—' : $value) : json_encode($value, JSON_UNESCAPED_UNICODE) }}</td></tr>@empty<tr><td colspan="2" class="text-center text-secondary py-4">Tidak ada metadata tambahan.</td></tr>@endforelse</tbody></table></div>
        </section>
    </main>

    <aside class="workspace-audit-sidebar is-collapsed" data-subject-audit-panel>
        <button class="workspace-audit-sidebar-toggle" type="button" data-subject-audit-toggle aria-expanded="false" aria-controls="document-audit-history" title="Buka audit trail"><span class="workspace-audit-sidebar-toggle-icon" aria-hidden="true">‹</span><span class="workspace-audit-sidebar-toggle-label">Audit trail</span></button>
        <div id="document-audit-history" class="workspace-audit-sidebar-content"><div class="card border-0 shadow-sm workspace-audit-panel"><div class="card-header bg-white border-0 d-flex align-items-center justify-content-between gap-2 py-3"><div><span class="workspace-form-kicker">Audit trail</span><strong class="d-block">Riwayat dokumen</strong></div><span class="workspace-audit-count">{{ $auditEvents->count() }}</span></div><div class="card-body pt-0">@forelse($auditEvents as $event)<div class="workspace-audit-entry"><div class="workspace-audit-entry-dot"></div><div class="small"><strong>{{ str($event->action)->headline() }}</strong><span class="d-block text-secondary">{{ $event->actor?->name ?: 'Sistem / anonim' }}</span><span class="d-block text-secondary">{{ $event->created_at?->format('d/m/Y H:i') }} · {{ $event->device ?: 'Perangkat tidak diketahui' }}</span></div>@if($event->old_values || $event->new_values)<details class="mt-2"><summary class="small text-primary">Lihat perubahan</summary><pre class="small bg-light rounded p-2 mt-2 mb-0" style="white-space:pre-wrap;max-height:220px;overflow:auto">@json(['old' => $event->old_values, 'new' => $event->new_values], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</pre></details>@endif</div>@empty<p class="text-secondary small mb-0">Belum ada riwayat perubahan untuk dokumen ini.</p>@endforelse</div></div></div>
    </aside>
</div>
@endsection
