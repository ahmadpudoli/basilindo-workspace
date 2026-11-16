@extends('layouts.app', ['title' => 'Pengajuan Cash Advance'])

@section('content')
<div class="mb-4">
    <div class="text-uppercase text-secondary small fw-semibold mb-2">HRIS · Claim</div>
    <h1 class="h3 mb-1">Pengajuan Cash Advance</h1>
    <p class="text-secondary mb-0">Form ini akan disiapkan terpisah dari reimbursement.</p>
</div>

<section class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="alert alert-info mb-4" role="status">
            Form dan workflow pengajuan Cash Advance belum tersedia. Pengajuan belum dapat dibuat melalui halaman ini.
        </div>
        <a href="{{ route('workspace.hris.claims') }}" class="btn btn-outline-secondary">Kembali ke Reimbursement &amp; Cash Advance</a>
    </div>
</section>
@endsection
