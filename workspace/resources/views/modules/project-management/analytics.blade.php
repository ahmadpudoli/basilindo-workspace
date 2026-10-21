@extends('layouts.app', ['title' => $title])
@section('content')
<div class="mb-4"><h1 class="h3 mb-1">{{ $title }}</h1><p class="text-secondary">Aktivitas kolaborasi project berdasarkan komentar dan perubahan status ticket.</p></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>#</th><th>Pengguna</th><th>Komentar</th><th>Perubahan status</th><th>Total</th></tr></thead><tbody>@forelse($users as $index => $item)<tr><td>{{ $index + 1 }}</td><td>{{ $item['user']->name ?? $item['user']->email }}</td><td>{{ $item['comments'] }}</td><td>{{ $item['status_updates'] }}</td><td class="fw-semibold">{{ $item['comments'] + $item['status_updates'] }}</td></tr>@empty<tr><td colspan="5" class="text-center text-secondary py-5">Belum ada aktivitas.</td></tr>@endforelse</tbody></table></div></div>
@endsection
