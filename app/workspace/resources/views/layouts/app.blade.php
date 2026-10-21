<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Basilindo Workspace') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="{{ auth()->check() ? '' : 'workspace-auth-page' }}">
    @php
        $moduleDefinitions = [
            'company' => [
                'label' => 'Company', 'icon' => 'company', 'url' => route('workspace.company'),
                'items' => [
                    ['label' => 'Ringkasan', 'url' => route('workspace.company')],
                    ['label' => 'Perusahaan', 'url' => route('workspace.company.companies')],
                    ['label' => 'Vendor', 'url' => route('workspace.company.vendors')],
                ],
            ],
            'crm' => [
                'label' => 'CRM', 'icon' => 'crm', 'url' => route('workspace.crm'),
                'items' => [
                    ['label' => 'Ringkasan', 'url' => route('workspace.crm')],
                    ['label' => 'Account', 'url' => route('workspace.crm.resource', 'accounts')],
                    ['label' => 'Contact', 'url' => route('workspace.crm.resource', 'contacts')],
                    ['label' => 'Lead', 'url' => route('workspace.crm.resource', 'leads')],
                    ['label' => 'Opportunity', 'url' => route('workspace.crm.resource', 'opportunities')],
                    ['label' => 'Aktivitas', 'url' => route('workspace.crm.resource', 'activities')],
                ],
            ],
            'project-management' => [
                'label' => 'Project Management', 'icon' => 'project', 'url' => route('workspace.project-management'),
                'items' => [
                    ['label' => 'Ringkasan', 'url' => route('workspace.project-management')],
                    ['label' => 'Project', 'url' => route('workspace.pm.resource', 'projects')],
                    ['label' => 'Ticket', 'url' => route('workspace.pm.resource', 'tickets')],
                    ['label' => 'Board ticket', 'url' => route('workspace.pm.board')],
                    ['label' => 'Timeline', 'url' => route('workspace.pm.timeline')],
                    ['label' => 'Epic', 'url' => route('workspace.pm.resource', 'epics')],
                    ['label' => 'Catatan', 'url' => route('workspace.pm.resource', 'notes')],
                    ['label' => 'Komentar', 'url' => route('workspace.pm.resource', 'comments')],
                    ['label' => 'Notifikasi', 'url' => route('workspace.pm.resource', 'notifications')],
                    ['label' => 'Kontribusi pengguna', 'url' => route('workspace.pm.contributions')],
                    ['label' => 'Leaderboard', 'url' => route('workspace.pm.leaderboard')],
                    ['label' => 'Status & prioritas', 'url' => route('workspace.pm.resource', 'statuses')],
                ],
            ],
            'documents' => [
                'label' => 'Document Management', 'icon' => 'documents', 'url' => route('workspace.documents'),
                'items' => [
                    ['label' => 'Ringkasan', 'url' => route('workspace.documents')],
                    ['label' => 'Dokumen', 'url' => route('workspace.documents.list')],
                    ['label' => 'Upload dokumen', 'url' => route('workspace.documents.create')],
                    ['label' => 'Tipe dokumen', 'url' => route('workspace.documents.types')],
                    ['label' => 'Verifikasi', 'url' => route('workspace.documents.verification')],
                    ['label' => 'Bundel', 'url' => route('workspace.documents.bundles')],
                ],
            ],
        ];
        $moduleDefinitions['settings'] = [
            'label' => 'Pengaturan', 'icon' => 'settings', 'url' => route('workspace.settings'),
            'items' => [
                ['label' => 'Ringkasan pengaturan', 'url' => route('workspace.settings')],
                ['label' => 'Modul aktif', 'url' => route('workspace.settings.modules')],
                ['label' => 'Pengguna & akses', 'url' => route('workspace.core.users')],
                ['label' => 'Role & permission', 'url' => route('workspace.core.roles')],
            ],
        ];
        $enabledModuleKeys = app(\App\Modules\Core\Application\Services\WorkspaceModuleRegistry::class)->enabledKeys();
        $moduleDefinitions = array_filter($moduleDefinitions, function (array $module, string $key) use ($enabledModuleKeys): bool {
            if ($key === 'settings') return auth()->user()?->hasRole('super_admin') || auth()->user()?->can('access_settings');
            return $key === 'core' || in_array($key, $enabledModuleKeys, true);
        }, ARRAY_FILTER_USE_BOTH);
        $activeModule = collect(array_keys($moduleDefinitions))->first(fn ($module) => request()->is('workspace/'.$module.'*'));
        if (! $activeModule && request()->is('workspace/core/*') && isset($moduleDefinitions['settings'])) {
            $activeModule = 'settings';
        }
        $activeModuleDefinition = $activeModule ? $moduleDefinitions[$activeModule] : null;
        $navigationIcons = [
            'Ringkasan' => 'dashboard', 'Perusahaan' => 'company', 'Vendor' => 'vendor', 'Pengguna & akses' => 'users',
            'Account' => 'account', 'Contact' => 'contact', 'Lead' => 'lead', 'Opportunity' => 'opportunity', 'Aktivitas' => 'activity',
            'Project' => 'project', 'Ticket' => 'ticket', 'Board ticket' => 'board', 'Timeline' => 'timeline', 'Epic' => 'epic',
            'Catatan' => 'note', 'Komentar' => 'comment', 'Notifikasi' => 'notification', 'Kontribusi pengguna' => 'users',
            'Leaderboard' => 'leaderboard', 'Status & prioritas' => 'settings', 'Ringkasan pengaturan' => 'settings', 'Modul aktif' => 'toggle', 'Role & permission' => 'settings', 'Dokumen' => 'document', 'Upload dokumen' => 'upload',
            'Tipe dokumen' => 'document-type', 'Verifikasi' => 'verification', 'Bundel' => 'bundle', 'Semua modul' => 'grid',
        ];
        $userName = auth()->user()?->name ?: auth()->user()?->email;
        $userInitial = $userName ? mb_strtoupper(mb_substr($userName, 0, 1)) : 'B';
    @endphp

    <div class="workspace-app-shell">
        <header class="workspace-topbar">
            <div class="workspace-topbar-left">
                @if ($activeModuleDefinition)
                    <button class="workspace-icon-button d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#workspaceSidebar" aria-label="Buka menu">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                @endif
                <a class="workspace-brand" href="{{ route('dashboard') }}">
                    <span class="workspace-brand-mark">B</span>
                    <span><strong>Basilindo</strong><small>Workspace</small></span>
                </a>
                @if ($activeModuleDefinition)
                    <span class="workspace-topbar-divider d-none d-lg-inline"></span>
                    <span class="workspace-topbar-context d-none d-lg-inline">{{ $activeModuleDefinition['label'] }}</span>
                    <button class="workspace-icon-button workspace-sidebar-toggle d-none d-lg-inline-flex" type="button" data-workspace-sidebar-toggle aria-label="Perkecil sidebar" title="Perkecil sidebar">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
                    </button>
                @endif
            </div>

            @auth
                <div class="workspace-topbar-actions">
                    <div class="dropdown">
                        <button class="workspace-module-launcher" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Beralih modul" title="Beralih modul">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="5" r="1.5"/><circle cx="12" cy="5" r="1.5"/><circle cx="19" cy="5" r="1.5"/><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/><circle cx="5" cy="19" r="1.5"/><circle cx="12" cy="19" r="1.5"/><circle cx="19" cy="19" r="1.5"/></svg>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end workspace-module-menu">
                            <div class="workspace-module-menu-header"><strong>Beralih ke modul</strong><small>Satu workspace, semua pekerjaan</small></div>
                            <div class="workspace-module-grid">
                                @foreach ($moduleDefinitions as $key => $module)
                                    <a class="workspace-module-tile {{ $activeModule === $key ? 'active' : '' }}" href="{{ $module['url'] }}">
                                        <span class="workspace-launcher-icon">@include('layouts.partials.module-icon', ['module' => $module['icon']])</span>
                                        <span>{{ $module['label'] }}</span>
                                        @if ($activeModule === $key)<span class="workspace-module-current">Aktif</span>@endif
                                    </a>
                                @endforeach
                            </div>
                            <a class="workspace-module-all" href="{{ route('dashboard') }}">Lihat semua modul <span aria-hidden="true">&rarr;</span></a>
                        </div>
                    </div>
                    <button class="workspace-icon-button" type="button" data-workspace-theme-toggle aria-label="Ubah tema" title="Ubah tema">
                        <svg class="theme-icon-light" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                        <svg class="theme-icon-dark" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 15.5A8.5 8.5 0 0 1 8.5 3.5 8.5 8.5 0 1 0 20.5 15.5Z"/></svg>
                    </button>
                    <div class="dropdown">
                        <button class="workspace-user-menu dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="workspace-avatar">{{ $userInitial }}</span>
                            <span class="workspace-user-copy d-none d-md-flex"><strong>{{ $userName }}</strong><small>Pengguna Workspace</small></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end workspace-account-menu">
                            <li><h6 class="dropdown-header">Akun aktif</h6></li>
                            <li><span class="dropdown-item-text small text-secondary">{{ auth()->user()?->email }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger" type="submit">Keluar</button></form></li>
                        </ul>
                    </div>
                </div>
            @endauth
        </header>

        <div class="workspace-shell">
            @if ($activeModuleDefinition)
                <aside class="workspace-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="workspaceSidebar" aria-label="Navigasi modul">
                    <div class="offcanvas-header d-lg-none">
                        <span class="workspace-sidebar-heading">{{ $activeModuleDefinition['label'] }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
                    </div>
                    <div class="workspace-sidebar-inner">
                        <div class="workspace-module-card">
                            <span class="workspace-module-icon">@include('layouts.partials.module-icon', ['module' => $activeModuleDefinition['icon']])</span>
                            <span><small>Modul aktif</small><strong>{{ $activeModuleDefinition['label'] }}</strong></span>
                        </div>
                        <div class="workspace-nav-label">Navigasi</div>
                        @php
                            $currentPath = parse_url(request()->url(), PHP_URL_PATH);
                            $activeNavPath = collect($activeModuleDefinition['items'])
                                ->map(fn (array $item) => parse_url($item['url'], PHP_URL_PATH))
                                ->filter(fn (string $path) => $currentPath === $path || str_starts_with($currentPath, rtrim($path, '/').'/'))
                                ->sortByDesc(fn (string $path) => strlen($path))
                                ->first();
                        @endphp
                        <nav class="workspace-nav">
                            @foreach ($activeModuleDefinition['items'] as $item)
                                @php($itemUrl = parse_url($item['url'], PHP_URL_PATH))
                                <a class="workspace-nav-link {{ $activeNavPath === $itemUrl ? 'active' : '' }}" href="{{ $item['url'] }}">
                                    <span class="workspace-nav-icon">@include('layouts.partials.nav-icon', ['icon' => $navigationIcons[$item['label']] ?? 'dashboard'])</span><span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </nav>
                        <div class="workspace-sidebar-footer">
                            <a class="workspace-nav-link" href="{{ route('dashboard') }}"><span class="workspace-nav-icon">@include('layouts.partials.nav-icon', ['icon' => 'grid'])</span><span>Semua modul</span></a>
                        </div>
                    </div>
                </aside>
            @endif

            <main class="workspace-main">
                <div class="workspace-content">
                    @if (session('status'))
                        <div class="alert alert-success workspace-alert" role="alert">{{ session('status') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger workspace-alert" role="alert">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger workspace-alert" role="alert">Periksa kembali input yang ditandai pada formulir.</div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div class="workspace-loading-overlay" data-workspace-loading aria-hidden="true"><div class="workspace-loading-card"><span class="workspace-loading-spinner" aria-hidden="true"></span><span data-workspace-loading-message>Memproses...</span></div></div>

    <div class="modal fade workspace-preview-modal" id="workspacePreviewModal" tabindex="-1" aria-labelledby="workspacePreviewTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content workspace-modal-content">
                <div class="modal-header workspace-modal-header"><div class="workspace-modal-heading"><span class="workspace-modal-icon workspace-modal-icon-document"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h8l4 4v14H6zM14 3v5h5M9 13h6M9 17h6"/></svg></span><div><span class="workspace-modal-kicker">Dokumen</span><h5 class="modal-title" id="workspacePreviewTitle">Preview dokumen</h5></div></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body p-0"><div class="workspace-preview-stage" data-workspace-preview-stage><div class="workspace-preview-loading">Memuat preview...</div></div></div>
                <div class="modal-footer"><a class="btn btn-outline-primary" data-workspace-preview-fallback target="_blank" rel="noopener">Buka di tab baru</a><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="workspaceConfirmModal" tabindex="-1" aria-labelledby="workspaceConfirmTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content workspace-modal-content workspace-confirm-modal"><div class="modal-header workspace-modal-header"><div class="workspace-modal-heading"><span class="workspace-modal-icon workspace-modal-icon-warning"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4 3 20h18L12 4Z"/><path d="M12 9v5M12 17h.01"/></svg></span><div><span class="workspace-modal-kicker">Perhatian</span><h5 class="modal-title" id="workspaceConfirmTitle">Konfirmasi tindakan</h5></div></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body workspace-confirm-body" data-workspace-confirm-message>Apakah Anda yakin ingin melanjutkan tindakan ini?</div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger workspace-confirm-submit" data-workspace-confirm-submit>Lanjutkan</button></div></div></div>
    </div>

    @livewireScripts
</body>
</html>
