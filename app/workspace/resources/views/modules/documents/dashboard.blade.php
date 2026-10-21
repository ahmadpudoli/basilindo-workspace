@extends('layouts.app', ['title' => 'Document Management'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><div class="text-uppercase text-secondary small fw-semibold mb-2">Ringkasan modul</div><h1 class="h3 mb-1">Document Management</h1><p class="text-secondary mb-0">Simpan, cari, verifikasi, dan bundel dokumen perusahaan secara aman.</p></div>
    <div class="d-flex gap-2"><a href="{{ route('workspace.documents.list') }}" class="btn btn-outline-primary">Lihat dokumen</a><a href="{{ route('workspace.documents.create') }}" class="btn btn-primary">Upload dokumen</a></div>
</div>
@include('modules.partials.stats')
<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2"><span>Dokumen terbaru</span><a href="{{ route('workspace.documents.list') }}" class="btn btn-sm btn-outline-primary">Buka daftar</a></div>
    <div class="table-responsive"><table class="table"><thead><tr><th>Judul</th><th>Perusahaan</th><th>Tipe</th><th>Status</th></tr></thead><tbody>
        @forelse($documents as $document)
            <tr><td class="fw-semibold">{{ $document->title }}</td><td>{{ $document->company?->name ?: '—' }}</td><td>{{ $document->documentType?->name ?: '—' }}</td><td><span class="badge text-bg-secondary">{{ $document->status ?: 'Belum diatur' }}</span></td></tr>
        @empty
            <tr><td colspan="4" class="text-center text-secondary py-5">Belum ada dokumen.</td></tr>
        @endforelse
    </tbody></table></div>
</div>
@endsection
