@extends('layouts.app', ['title' => 'Pengaturan Akun'])

@section('content')
<div class="container-fluid py-4 account-settings-page">
    <header class="account-settings-heading">
        <span class="account-settings-heading-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/><path d="M5 20c.7-3.4 3.1-5.2 7-5.2s6.3 1.8 7 5.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M19 4v4m-2-2h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></span>
        <div><div class="account-settings-eyebrow">PENGATURAN UMUM</div><h1>Pengaturan Akun</h1><p>Kelola profil, scope perusahaan, keamanan, dan preferensi akun Workspace Anda.</p></div>
    </header>

    @if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><strong>Perubahan belum disimpan.</strong><ul class="mb-0 mt-1 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <button class="account-settings-open btn btn-outline-primary d-lg-none mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#account-settings-sidebar" aria-controls="account-settings-sidebar">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        <span>Menu Pengaturan Akun</span>
    </button>

    <div class="account-settings-layout">
        <aside class="account-settings-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="account-settings-sidebar" aria-label="Menu pengaturan akun">
            <div class="offcanvas-header account-settings-offcanvas-header">
                <div><small>PENGATURAN UMUM</small><h2>Pengaturan Akun</h2></div>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#account-settings-sidebar" aria-label="Tutup menu"></button>
            </div>
            <div class="offcanvas-body account-settings-offcanvas-body">
              <nav class="account-settings-nav" aria-label="Bagian pengaturan akun">
                <div class="account-settings-nav-label">AKUN SAYA</div>
                <a href="{{ route('workspace.account.settings', ['section' => 'summary']) }}" @if($section === 'summary') aria-current="page" @endif class="account-settings-nav-link {{ $section === 'summary' ? 'active' : '' }}">
                <span class="account-settings-nav-icon summary" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.3" stroke="currentColor" stroke-width="1.8"/><path d="M5 20c.7-3.2 3-5 7-5s6.3 1.8 7 5M19 4v4m-2-2h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                <span><strong>Ringkasan Akun</strong><small>Profil dan scope perusahaan</small></span>
                </a>
                <a href="{{ route('workspace.account.settings', ['section' => 'password']) }}" @if($section === 'password') aria-current="page" @endif class="account-settings-nav-link {{ $section === 'password' ? 'active' : '' }}">
                <span class="account-settings-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="4" y="10" width="16" height="11" rx="2.5" stroke="currentColor" stroke-width="1.8"/><path d="M8 10V7a4 4 0 1 1 8 0v3m-4 4v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                <span><strong>Ubah Password</strong><small>Keamanan akun</small></span>
                </a>
                <a href="{{ route('workspace.account.settings', ['section' => 'notifications']) }}" @if($section === 'notifications') aria-current="page" @endif class="account-settings-nav-link {{ $section === 'notifications' ? 'active' : '' }}">
                <span class="account-settings-nav-icon notifications" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Zm-8 12h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <span><strong>Preferensi Notifikasi</strong><small>Atur email notifikasi</small></span>
                </a>
                <div class="account-settings-nav-account"><span class="account-settings-avatar">{{ mb_strtoupper(mb_substr($user->name ?? $user->email, 0, 1)) }}</span><span><strong>{{ $user->name ?? 'Akun Workspace' }}</strong><small>{{ $user->email }}</small></span></div>
              </nav>
            </div>
        </aside>

        <section class="account-settings-content" aria-labelledby="account-settings-section-title">
            <div class="account-settings-content-heading">
                <div class="account-settings-section-kicker">{{ ['summary' => 'PROFIL', 'password' => 'KEAMANAN', 'notifications' => 'PREFERENSI'][$section] }}</div>
                <h2 id="account-settings-section-title">{{ ['summary' => 'Ringkasan Akun', 'password' => 'Ubah Password', 'notifications' => 'Preferensi Notifikasi'][$section] }}</h2>
                <p>{{ ['summary' => 'Informasi profil dan perusahaan yang masuk dalam scope akun Anda.', 'password' => 'Gunakan password saat ini untuk mengatur password baru akun Anda.', 'notifications' => 'Pilih apakah notifikasi Workspace juga dikirim ke email akun Anda.'][$section] }}</p>
            </div>
            <div class="account-settings-content-body">
                @if($section === 'summary')
                    <div class="account-summary-section">
                        <div class="account-summary-profile">
                            <span class="account-summary-avatar">{{ mb_strtoupper(mb_substr($user->name ?? $user->email, 0, 1)) }}</span>
                            <div class="account-summary-profile-main">
                                <span class="account-summary-label">PROFIL PENGGUNA</span>
                                <h3>{{ $user->name ?: 'Nama belum diatur' }}</h3>
                                <p>{{ $user->email }}</p>
                            </div>
                            <span class="account-summary-status"><i></i>Akun aktif</span>
                        </div>

                        <div class="account-summary-facts">
                            <div><small>Nama pengguna</small><strong>{{ $user->name ?: 'Belum diatur' }}</strong></div>
                            <div><small>Email akun</small><strong>{{ $user->email }}</strong></div>
                            <div><small>Status verifikasi email</small><strong>{{ $user->email_verified_at ? 'Terverifikasi' : 'Belum diverifikasi' }}</strong></div>
                            <div><small>Peran Workspace</small><strong>{{ $user->getRoleNames()->isNotEmpty() ? $user->getRoleNames()->implode(', ') : 'Belum ada peran khusus' }}</strong></div>
                        </div>

                        <section class="account-summary-companies" aria-labelledby="account-summary-companies-title">
                            <div class="account-summary-companies-heading">
                                <span class="account-summary-company-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 20V6.5L12 3l8 3.5V20M2.5 20h19M8 9h1m6 0h1M8 12.5h1m6 0h1M10 20v-4h4v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                <div><h3 id="account-summary-companies-title">Scope perusahaan</h3><p>Perusahaan yang dapat digunakan atau dilihat melalui akses Workspace Anda.</p></div>
                                <span class="account-summary-company-count">{{ $user->hasRole('super_admin') ? 'Semua' : $companies->count() }}</span>
                            </div>

                            @if($user->hasRole('super_admin'))
                                <div class="account-summary-scope-note"><strong>Akses seluruh perusahaan</strong><span>Peran Super Admin memberikan akses lintas perusahaan.</span></div>
                            @endif

                            @forelse($companies as $company)
                                <div class="account-summary-company-row">
                                    <span class="account-summary-company-mark" aria-hidden="true">{{ mb_strtoupper(mb_substr($company->name, 0, 1)) }}</span>
                                    <span class="account-summary-company-name"><strong>{{ $company->name }}</strong><small>{{ $company->code ?: ucfirst(str_replace('_', ' ', $company->entity_type ?: 'Perusahaan')) }}</small></span>
                                    @if(!$user->hasRole('super_admin'))<span class="account-summary-company-role">{{ str($company->pivot->scope_role ?? 'member')->replace('_', ' ')->title() }}</span>@endif
                                    <span class="account-summary-company-state {{ $company->status === 'active' ? 'is-active' : '' }}">{{ $company->status === 'active' ? 'Aktif' : ucfirst($company->status ?: 'Tidak diketahui') }}</span>
                                </div>
                            @empty
                                @unless($user->hasRole('super_admin'))
                                    <div class="account-summary-empty"><strong>Belum ada scope perusahaan</strong><span>Akun Anda belum ditautkan ke perusahaan. Hubungi administrator Workspace untuk memperbarui akses.</span></div>
                                @endunless
                            @endforelse
                        </section>
                    </div>
                @elseif($section === 'password')
                    @include('modules.core.partials.account-password-form')
                @else
                    @include('modules.core.partials.notification-preferences-form')
                @endif
            </div>
        </section>
    </div>
</div>
@endsection
