@extends('layouts.app', ['title' => 'Ubah password'])

@section('content')
<div class="container py-4 account-password-page">
    <div class="account-password-heading mb-4">
        <span class="account-password-heading-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="4" y="10" width="16" height="11" rx="2.5" stroke="currentColor" stroke-width="1.8"/><path d="M8 10V7a4 4 0 1 1 8 0v3m-4 4v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
        <div><div class="text-uppercase small fw-semibold text-primary mb-1">Keamanan akun</div><h1 class="h3 mb-1">Ubah password</h1><p class="text-secondary mb-0">Gunakan password saat ini untuk mengatur password baru akun Anda.</p></div>
    </div>

    @if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><strong>Password belum diperbarui.</strong><ul class="mb-0 mt-1 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="account-password-layout">
        <form method="POST" action="{{ route('workspace.account.password.update') }}" class="account-password-form">
            @csrf @method('PUT')
            <div class="account-password-form-header"><strong>Password akun</strong><span>{{ auth()->user()?->email }}</span></div>
            <div class="account-password-form-body">
                <div class="mb-3"><label class="form-label" for="current_password">Password saat ini</label><div class="workspace-password-wrap"><input class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" type="password" autocomplete="current-password" required><button type="button" class="workspace-password-toggle" data-password-toggle="#current_password" aria-label="Tampilkan password saat ini"><svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg></button></div></div>
                <hr>
                <div class="mb-3"><label class="form-label" for="new_password">Password baru</label><div class="workspace-password-wrap"><input class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" type="password" autocomplete="new-password" minlength="8" required><button type="button" class="workspace-password-toggle" data-password-toggle="#new_password" aria-label="Tampilkan password baru"><svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg></button></div><div class="form-text">Minimal 8 karakter dan harus berbeda dari password saat ini.</div></div>
                <div class="mb-1"><label class="form-label" for="new_password_confirmation">Konfirmasi password baru</label><div class="workspace-password-wrap"><input class="form-control" id="new_password_confirmation" name="new_password_confirmation" type="password" autocomplete="new-password" minlength="8" required><button type="button" class="workspace-password-toggle" data-password-toggle="#new_password_confirmation" aria-label="Tampilkan konfirmasi password"><svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg></button></div></div>
            </div>
            <div class="account-password-form-footer"><a href="{{ route('dashboard') }}" class="btn btn-light">Batal</a><button type="submit" class="btn btn-primary">Simpan password baru</button></div>
        </form>
        <aside class="account-password-guidance"><span class="account-password-guidance-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3 4.5 6v5.5c0 4.5 3.2 7.5 7.5 9.5 4.3-2 7.5-5 7.5-9.5V6L12 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><strong>Tips keamanan</strong><p>Buat password yang unik dan tidak digunakan di layanan lain. Setelah password diganti, sesi login lain pada akun ini akan diminta untuk login kembali.</p></aside>
    </div>
</div>
@endsection
