@extends('layouts.app', ['title' => 'Masuk — Basilindo Workspace'])

@section('content')
<main class="workspace-login-page">
    <section class="workspace-login-showcase">
        <div class="workspace-login-orbit workspace-login-orbit-one"></div>
        <div class="workspace-login-orbit workspace-login-orbit-two"></div>
        <div class="workspace-login-showcase-content">
            <a class="workspace-login-brand" href="{{ route('login') }}"><span class="workspace-login-brand-mark">B</span><span><strong>Basilindo</strong><small>Workspace</small></span></a>
            <div class="workspace-login-copy">
                <span class="workspace-login-eyebrow">BASILINDO WORKSPACE</span>
                <h1>Satu workspace untuk <em>seluruh bisnis.</em></h1>
                <p>Hubungkan dokumen, people, customer, dan project dalam satu platform kerja yang terintegrasi—lebih rapi, mudah dipantau, dan siap berkembang bersama perusahaan.</p>
                <div class="workspace-login-highlights"><span><svg viewBox="0 0 24 24"><path d="M4 5h16v12H4zM8 21h8M12 17v4"/></svg>Document Management</span><span><svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 20c.5-3 2.5-5 6-5s5.5 2 6 5M16 11a3 3 0 1 0 0-6M17 15c2.3.2 3.7 1.8 4 5"/></svg>HRIS & CRM</span><span><svg viewBox="0 0 24 24"><path d="M4 19V5M4 19h16M8 16v-4M12 16V8M16 16v-6"/></svg>Project Management</span></div>
            </div>
            <div class="workspace-login-decoration"><span></span><span></span><span></span><span></span><span></span></div>
        </div>
    </section>

    <section class="workspace-login-panel">
        <div class="workspace-login-form-wrap">
            <div class="workspace-login-mobile-brand"><span class="workspace-login-brand-mark">B</span><span><strong>Basilindo</strong><small>Workspace</small></span></div>
            <div class="workspace-login-heading"><span class="workspace-login-kicker">Selamat datang kembali</span><h2>Masuk ke Workspace</h2><p>Gunakan akun lokal Basilindo Anda untuk melanjutkan.</p></div>
            @error('authentication')<div class="workspace-login-feedback" role="alert"><span class="workspace-login-feedback-icon" aria-hidden="true">!</span><div><strong>Gagal masuk</strong><span>{{ $message }}</span></div></div>@enderror
            @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
            <form method="POST" action="{{ route('login.store') }}" class="workspace-login-form">
                @csrf
                <div class="workspace-login-field"><label class="form-label" for="email">Email</label><input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@perusahaan.com" required autofocus>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="workspace-login-field"><label class="form-label" for="password">Kata sandi</label><div class="workspace-password-wrap"><input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" placeholder="Masukkan kata sandi" required><button type="button" class="workspace-password-toggle" data-password-toggle="#password" aria-label="Tampilkan kata sandi"><svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg></button></div>@error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <div class="d-flex justify-content-between align-items-center mb-4"><div class="form-check"><input class="form-check-input" id="remember" name="remember" type="checkbox" value="1"><label class="form-check-label" for="remember">Ingat perangkat ini</label></div></div>
                <button class="btn workspace-login-submit w-100" type="submit"><span>Masuk ke Workspace</span><svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
            </form>
            <p class="workspace-login-footer">Basilindo Workspace · Document Management</p>
        </div>
    </section>
</main>
@endsection
