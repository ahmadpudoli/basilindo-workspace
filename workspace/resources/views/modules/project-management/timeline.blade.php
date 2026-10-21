@extends('layouts.app', ['title' => 'Timeline Project'])
@section('content')
<h1 class="h3 mb-1">Timeline ticket</h1><p class="text-secondary mb-4">Ticket yang memiliki target tanggal.</p><div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Tanggal</th><th>Ticket</th><th>Project</th><th>Status</th></tr></thead><tbody>@forelse($tickets as $ticket)<tr><td>{{ $ticket->due_date?->format('d/m/Y') }}</td><td>{{ $ticket->name }}</td><td>{{ $ticket->project?->name ?: '—' }}</td><td>{{ $ticket->status?->name ?: '—' }}</td></tr>@empty<tr><td colspan="4" class="text-center text-secondary py-5">Belum ada ticket bertanggal.</td></tr>@endforelse</tbody></table></div><div class="card-footer bg-white">{{ $tickets->links() }}</div></div>
@endsection
