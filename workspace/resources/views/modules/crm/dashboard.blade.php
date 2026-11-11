@extends('layouts.app', ['title' => 'CRM'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><div class="text-uppercase text-secondary small fw-semibold mb-2">Ringkasan modul</div><h1 class="h3 mb-1">CRM</h1><p class="text-secondary mb-0">Kelola account, contact, pipeline sales, dan aktivitas.</p></div>
    <div class="d-flex gap-2"><a href="{{ route('workspace.crm.pipeline') }}" class="btn btn-primary">Buka pipeline</a><a href="{{ route('workspace.crm.resource', 'accounts') }}" class="btn btn-outline-primary">Buka account</a></div>
</div>
@include('modules.partials.stats')
<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2"><span>Account terbaru</span><a href="{{ route('workspace.crm.resource', 'accounts') }}" class="btn btn-sm btn-outline-primary">Buka daftar</a></div>
    <div class="table-responsive"><table class="table"><thead><tr><th>Nama</th><th>Tipe</th><th>Client / partner</th></tr></thead><tbody>
        @forelse($accounts as $account)
            <tr><td class="fw-semibold">{{ $account->name }}</td><td>{{ $account->account_type === 'company' ? 'Perusahaan' : ($account->account_type === 'person' ? 'Perorangan' : 'Belum ditentukan') }}</td><td>{{ $account->partyCompany?->name ?: '—' }}</td></tr>
        @empty
            <tr><td colspan="3" class="text-center text-secondary py-5">Belum ada account.</td></tr>
        @endforelse
    </tbody></table></div>
</div>
@endsection
