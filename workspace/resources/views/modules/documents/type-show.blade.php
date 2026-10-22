@extends('layouts.app', ['title' => 'Detail Tipe Dokumen'])

@section('content')
<div class="workspace-page-heading d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><span class="workspace-form-kicker">Konfigurasi dokumen</span><h1 class="h3 mb-1">Detail tipe dokumen</h1><p class="text-secondary mb-0">Lihat konfigurasi metadata dan riwayat perubahan tipe dokumen.</p></div>
    <div class="d-flex flex-wrap gap-2"><a href="{{ route('workspace.documents.types') }}" class="btn btn-sm btn-outline-secondary">Kembali ke daftar</a>@can('update', $type)<a href="{{ route('workspace.documents.types.edit', $type) }}" class="btn btn-sm btn-primary">Edit tipe dokumen</a>@endcan</div>
</div>

<div class="workspace-document-edit-layout">
    <main class="workspace-document-edit-main">
        <section class="card border-0 shadow-sm mb-3"><div class="card-body p-4 p-xl-5"><div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4"><div><span class="workspace-form-kicker">Informasi utama</span><h2 class="h4 mb-1">{{ $type->name }}</h2><p class="text-secondary mb-0"><code>{{ $type->code }}</code></p></div><span class="badge {{ $type->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $type->is_active ? 'Aktif' : 'Nonaktif' }}</span></div><div class="row g-3 workspace-document-detail-grid"><div class="col-md-6"><span>Perusahaan utama</span><strong>{{ $type->company?->name ?: 'Global' }}</strong></div><div class="col-md-6"><span>Jumlah metadata</span><strong>{{ count($type->metadataDefinitions()) }} field</strong></div></div><div class="workspace-document-detail-block"><span>Perusahaan yang menggunakan</span><div class="d-flex flex-wrap gap-1 mt-2">@forelse($companies as $companyId => $companyName)<span class="badge text-bg-light border">{{ $companyName }}</span>@empty<span class="text-secondary">Global — semua perusahaan operasional</span>@endforelse</div></div></div></section>
        <section class="card border-0 shadow-sm"><div class="card-header bg-white border-0"><span class="workspace-form-kicker">Metadata tambahan</span><strong class="d-block">Field yang tersedia</strong></div><div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Nama field</th><th>Tipe data</th><th>Aturan</th></tr></thead><tbody>@forelse($type->metadataDefinitions() as $fieldName => $config)<tr><td class="fw-semibold">{{ str($fieldName)->replace('_', ' ')->headline() }}</td><td>{{ str($config['type'] ?? 'string')->replace('_', ' ')->headline() }}</td><td>{{ ($config['mandatory'] ?? false) ? 'Wajib diisi' : 'Opsional' }}</td></tr>@empty<tr><td colspan="3" class="text-center text-secondary py-4">Belum ada field metadata.</td></tr>@endforelse</tbody></table></div></section>
    </main>
    @include('modules.partials.subject-audit-sidebar', ['auditEvents' => $auditEvents, 'auditTitle' => 'Riwayat tipe dokumen', 'auditId' => 'document-type-audit-history'])
</div>
@endsection
