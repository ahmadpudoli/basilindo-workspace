@extends('layouts.app', ['title' => 'Document Management'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><a href="{{ route('dashboard') }}" class="text-decoration-none">← Semua modul</a><h1 class="h3 mt-2 mb-1">Document Management</h1><p class="text-secondary mb-0">Dokumen, metadata, verifikasi, dan bundel.</p></div></div>
@include('modules.partials.stats')
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Dokumen terbaru</strong></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Judul</th><th>Perusahaan</th><th>Tipe</th><th>Status</th></tr></thead><tbody>@forelse($documents as $document)<tr><td>{{ $document->title }}</td><td>{{ $document->company?->name ?: '—' }}</td><td>{{ $document->documentType?->name ?: '—' }}</td><td>{{ $document->status ?: '—' }}</td></tr>@empty<tr><td colspan="4" class="text-center text-secondary py-4">Belum ada dokumen.</td></tr>@endforelse</tbody></table></div></div>
@endsection
