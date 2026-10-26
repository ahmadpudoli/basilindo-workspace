@extends('layouts.app', ['title' => 'Basilindo Workspace'])

@section('content')
<div class="workspace-landing">
    <header class="workspace-landing-nav">
        <a class="workspace-landing-brand" href="{{ route('home') }}" aria-label="Basilindo Workspace">
            <img class="workspace-landing-brand-logo" src="{{ asset('images/logo-basilindo.png') }}" alt="Logo Basilindo" width="42" height="42">
            <span><strong>Basilindo</strong><small>WORKSPACE</small></span>
        </a>
        <nav class="workspace-landing-links d-none d-md-flex" aria-label="Navigasi landing page">
            <a href="#platform">Platform</a>
            <a href="#modules">Modul</a>
            <a href="#flow">Cara kerja</a>
        </nav>
        <a class="workspace-landing-login" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
            {{ auth()->check() ? 'Buka workspace' : 'Masuk ke aplikasi' }}
            <span aria-hidden="true">↗</span>
        </a>
    </header>

    <main>
        <section class="workspace-landing-hero" id="platform">
            <div class="workspace-landing-hero-copy">
                <div class="workspace-landing-eyebrow"><span></span> Satu workspace, semua pekerjaan</div>
                <h1>Kerja lebih <em>terarah.</em><br>Bisnis lebih terkoneksi.</h1>
                <p class="workspace-landing-lead">Basilindo Workspace menyatukan data, proses, dan kolaborasi perusahaan dalam satu platform yang aman, terukur, dan mudah digunakan.</p>
                <div class="workspace-landing-actions">
                    <a class="btn btn-primary btn-lg workspace-landing-primary" href="{{ auth()->check() ? route('dashboard') : route('login') }}">Mulai bekerja <span aria-hidden="true">→</span></a>
                    <a class="workspace-landing-secondary" href="#modules">Jelajahi platform <span aria-hidden="true">↓</span></a>
                </div>
                <div class="workspace-landing-trust"><span class="workspace-landing-avatars"><i>B</i><i>F</i><i>A</i></span><span>Dibangun untuk tim yang ingin bekerja lebih cerdas</span></div>
            </div>
            <div class="workspace-landing-visual" aria-label="Pratinjau dashboard Basilindo Workspace">
                <div class="workspace-landing-orb workspace-landing-orb-one"></div>
                <div class="workspace-landing-orb workspace-landing-orb-two"></div>
                <div class="workspace-landing-dashboard">
                    <div class="workspace-landing-dashboard-top"><span class="workspace-landing-mini-brand"><b>B</b> Basilindo Workspace</span><span class="workspace-landing-dots">•••</span></div>
                    <div class="workspace-landing-dashboard-body">
                        <div class="workspace-landing-dashboard-heading"><div><small>RINGKASAN WORKSPACE</small><strong>Selamat datang kembali</strong></div><span class="workspace-landing-dashboard-avatar">A</span></div>
                        <div class="workspace-landing-metrics"><div><span>Total dokumen</span><strong>2,480</strong><small class="up">↑ 12.8%</small></div><div><span>Perlu review</span><strong>24</strong><small class="warning">● Perlu perhatian</small></div></div>
                        <div class="workspace-landing-chart"><div class="workspace-landing-chart-head"><span>Aktivitas workspace</span><small>7 hari terakhir</small></div><div class="workspace-landing-bars"><i style="height:35%"></i><i style="height:51%"></i><i style="height:42%"></i><i style="height:68%"></i><i style="height:57%"></i><i style="height:82%"></i><i style="height:72%"></i></div><div class="workspace-landing-chart-labels"><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span></div></div>
                    </div>
                </div>
                <div class="workspace-landing-float workspace-landing-float-document"><span class="workspace-landing-float-icon">✓</span><span><small>Dokumen terverifikasi</small><strong>Invoice LTN-024</strong></span></div>
                <div class="workspace-landing-float workspace-landing-float-scope"><span class="workspace-landing-float-icon">◌</span><span><small>Company scope</small><strong>3 perusahaan aktif</strong></span></div>
            </div>
        </section>

        <section class="workspace-landing-strip">
            <span>Dirancang untuk alur kerja yang terintegrasi</span><i></i><strong>Company</strong><i></i><strong>CRM</strong><i></i><strong>Projects</strong><i></i><strong>Documents</strong><i></i><strong>HRIS</strong>
        </section>

        <section class="workspace-landing-section" id="modules">
            <div class="workspace-landing-section-heading"><div><div class="workspace-landing-eyebrow"><span></span> Platform terpadu</div><h2>Semua yang dibutuhkan<br>untuk terus bergerak.</h2></div><p>Satu login untuk mengelola proses penting perusahaan—dengan konteks, akses, dan visibilitas yang tepat.</p></div>
            <div class="workspace-landing-module-grid">
                <article class="workspace-landing-module-card workspace-landing-module-blue"><div class="workspace-landing-module-icon">▦</div><small>MASTER DATA</small><h3>Company</h3><p>Kelola struktur group, perusahaan, vendor, dan scope akses dengan rapi.</p><a href="{{ auth()->check() ? route('workspace.company') : route('login') }}">Buka modul <span>↗</span></a></article>
                <article class="workspace-landing-module-card workspace-landing-module-purple"><div class="workspace-landing-module-icon">⌁</div><small>RELATIONSHIP</small><h3>CRM</h3><p>Bangun relasi lebih kuat dari account, lead, opportunity, hingga aktivitas.</p><a href="{{ auth()->check() ? route('workspace.crm') : route('login') }}">Buka modul <span>↗</span></a></article>
                <article class="workspace-landing-module-card workspace-landing-module-orange"><div class="workspace-landing-module-icon">◫</div><small>COLLABORATION</small><h3>Project Management</h3><p>Jaga project, ticket, timeline, dan kontribusi tim tetap sinkron.</p><a href="{{ auth()->check() ? route('workspace.project-management') : route('login') }}">Buka modul <span>↗</span></a></article>
                <article class="workspace-landing-module-card workspace-landing-module-green"><div class="workspace-landing-module-icon">▤</div><small>CONTROL CENTER</small><h3>Document Management</h3><p>Simpan, cari, verifikasi, dan hubungkan dokumen secara aman.</p><a href="{{ auth()->check() ? route('workspace.documents') : route('login') }}">Buka modul <span>↗</span></a></article>
            </div>
        </section>

        <section class="workspace-landing-flow" id="flow"><div class="workspace-landing-flow-copy"><div class="workspace-landing-eyebrow"><span></span> Dibuat untuk kejelasan</div><h2>Dari data yang tersebar menjadi keputusan yang terarah.</h2><p>Workspace membantu tim menemukan konteks yang tepat, menjaga akses tetap aman, dan menyelesaikan pekerjaan tanpa berpindah-pindah aplikasi.</p><a class="workspace-landing-text-link" href="{{ auth()->check() ? route('dashboard') : route('login') }}">Lihat workspace <span>→</span></a></div><div class="workspace-landing-steps"><div><b>01</b><span><strong>Satukan</strong><small>Data dan proses di satu workspace.</small></span></div><div><b>02</b><span><strong>Hubungkan</strong><small>Company, project, CRM, dan dokumen.</small></span></div><div><b>03</b><span><strong>Bergerak</strong><small>Dengan kontrol dan visibilitas yang jelas.</small></span></div></div></section>

        <section class="workspace-landing-cta"><div><div class="workspace-landing-eyebrow"><span></span> Basilindo Workspace</div><h2>Siap membuat pekerjaan terasa lebih sederhana?</h2><p>Masuk ke workspace Anda dan mulai bekerja dengan konteks yang lengkap.</p></div><a class="btn btn-light btn-lg" href="{{ auth()->check() ? route('dashboard') : route('login') }}">Masuk ke aplikasi <span>↗</span></a></section>
    </main>
    <footer class="workspace-landing-footer"><span>© {{ date('Y') }} Basilindo Workspace</span><span>Secure workspace for connected teams.</span></footer>
</div>
@endsection
