@extends('layouts.app', ['title' => 'Document Management'])

@section('content')
<section class="workspace-doc-hero mb-4">
    <div class="workspace-doc-hero-orb workspace-doc-hero-orb-one"></div>
    <div class="workspace-doc-hero-orb workspace-doc-hero-orb-two"></div>
    <div class="workspace-doc-hero-content">
        <span class="workspace-doc-eyebrow"><span class="workspace-doc-live-dot"></span> Ruang kerja dokumen</span>
        <h1>Semua dokumen,<br><span>dalam satu alur.</span></h1>
        <p>Simpan, cari, dan bundel dokumen perusahaan secara aman dari satu tempat.</p>
        <div class="d-flex flex-wrap gap-2 mt-4">
            <a href="{{ route('workspace.documents.create') }}" class="btn btn-light btn-sm workspace-doc-hero-button">Upload dokumen <span aria-hidden="true">&rarr;</span></a>
            <a href="{{ route('workspace.documents.list') }}" class="btn btn-outline-light btn-sm">Buka daftar dokumen</a>
        </div>
    </div>
    <div class="workspace-doc-hero-art" aria-hidden="true">
        <div class="workspace-doc-art-ring workspace-doc-art-ring-one"></div>
        <div class="workspace-doc-art-ring workspace-doc-art-ring-two"></div>
        <div class="workspace-doc-art-file workspace-doc-art-file-main"><span class="workspace-doc-art-file-fold"></span><i></i><i></i><i></i></div>
        <div class="workspace-doc-art-file workspace-doc-art-file-small"><span class="workspace-doc-art-file-fold"></span><i></i><i></i></div>
        <div class="workspace-doc-art-check">✓</div>
    </div>
</section>

<div class="row g-3 mb-4 workspace-doc-stat-grid">
    @foreach ($stats as $index => $stat)
        <div class="col-6 col-xl-3">
            <div class="workspace-doc-stat workspace-doc-stat-{{ $index }} h-100">
                <div class="workspace-doc-stat-icon" aria-hidden="true">
                    @if($index === 0)<svg viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6zM14 3v5h5M9 13h6M9 17h4"/></svg>
                    @elseif($index === 1)<svg viewBox="0 0 24 24"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5zM4 5.5v16M7 7h9M7 11h9"/></svg>
                    @elseif($index === 2)<svg viewBox="0 0 24 24"><path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/></svg>
                    @else<svg viewBox="0 0 24 24"><path d="M4 7h16v13H4zM8 7V4h8v3M8 12l2.5 2.5L16 9"/></svg>@endif
                </div>
                <div class="workspace-doc-stat-copy"><span>{{ $stat['label'] }}</span><strong>{{ number_format($stat['value']) }}</strong></div>
                <span class="workspace-doc-stat-arrow" aria-hidden="true">&nearr;</span>
            </div>
        </div>
    @endforeach
</div>

<section class="workspace-doc-recent card border-0">
    <div class="workspace-doc-recent-header"><div><span class="workspace-doc-section-kicker">Aktivitas terbaru</span><h2>Dokumen terbaru</h2><p>File yang baru saja masuk ke workspace.</p></div><a href="{{ route('workspace.documents.list') }}" class="btn btn-sm btn-outline-primary">Lihat semua <span aria-hidden="true">&rarr;</span></a></div>
    <div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Dokumen</th><th>Perusahaan</th><th>Tipe</th><th>Status</th></tr></thead><tbody>
        @forelse($documents as $document)
            <tr><td><div class="workspace-doc-row-title"><span class="workspace-doc-row-icon" aria-hidden="true">↗</span><span><strong>{{ $document->title }}</strong><small>{{ $document->reference_number ?: $document->original_filename }}</small></span></div></td><td>{{ $document->company?->name ?: '—' }}</td><td>{{ $document->documentType?->name ?: '—' }}</td><td><span class="workspace-doc-ready-badge"><span></span>Tersedia</span></td></tr>
        @empty
            <tr><td colspan="4" class="text-center text-secondary py-5">Belum ada dokumen.</td></tr>
        @endforelse
    </tbody></table></div>
</section>
@endsection
