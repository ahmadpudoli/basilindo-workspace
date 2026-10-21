@extends('layouts.app', ['title' => 'Verifikasi Dokumen'])
@section('content')
<h1 class="h3 mb-1">Verifikasi</h1><p class="text-secondary mb-4">Review dan tindak lanjut kasus verifikasi dokumen.</p>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Status</th><th>Ringkasan</th><th>Dibuat</th><th>Selesai</th></tr></thead><tbody>@forelse($cases as $case)<tr><td><a href="{{ route('workspace.documents.verification.case', $case) }}" class="text-decoration-none"><span class="badge text-bg-warning">{{ $case->status }}</span></a></td><td>{{ $case->summary ?: '—' }}</td><td>{{ $case->created_at?->format('d/m/Y H:i') }}</td><td>{{ $case->resolved_at?->format('d/m/Y H:i') ?: '—' }}</td></tr>@empty<tr><td colspan="4" class="text-center text-secondary py-5">Belum ada kasus verifikasi.</td></tr>@endforelse</tbody></table></div><div class="card-footer bg-white">{{ $cases->links() }}</div></div>
@endsection
