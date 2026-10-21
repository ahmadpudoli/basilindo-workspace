@extends('layouts.app', ['title' => 'Project Management'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><div class="text-uppercase text-secondary small fw-semibold mb-2">Ringkasan modul</div><h1 class="h3 mb-1">Project Management</h1><p class="text-secondary mb-0">Pantau project, ticket, epic, status, dan prioritas.</p></div>
    <a href="{{ route('workspace.pm.resource', 'projects') }}" class="btn btn-primary">Buka project</a>
</div>
@include('modules.partials.stats')
<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2"><span>Ticket terbaru</span><a href="{{ route('workspace.pm.resource', 'tickets') }}" class="btn btn-sm btn-outline-primary">Buka daftar</a></div>
    <div class="table-responsive"><table class="table"><thead><tr><th>Ticket</th><th>Project</th><th>Status</th><th>Prioritas</th></tr></thead><tbody>
        @forelse($tickets as $ticket)
            <tr><td class="fw-semibold">{{ $ticket->name }}</td><td>{{ $ticket->project?->name ?: '—' }}</td><td><span class="badge text-bg-light">{{ $ticket->status?->name ?: 'Belum diatur' }}</span></td><td>{{ $ticket->priority?->name ?: '—' }}</td></tr>
        @empty
            <tr><td colspan="4" class="text-center text-secondary py-5">Belum ada ticket.</td></tr>
        @endforelse
    </tbody></table></div>
</div>
@endsection
