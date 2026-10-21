@extends('layouts.app', ['title' => 'Workspace'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <p class="text-primary fw-semibold mb-1">Basilindo Workspace</p>
        <h1 class="h3 mb-1">Pilih modul</h1>
        <p class="text-secondary mb-0">Satu login untuk seluruh pekerjaan Anda.</p>
    </div>
</div>
<div class="row g-4">
    @foreach ($modules as $module)
        @php($moduleClass = str_replace('_', '-', $module['key']))
        <div class="col-12 col-md-6 workspace-dashboard-module-column">
            <a class="workspace-dashboard-module workspace-dashboard-module--{{ $moduleClass }}" href="{{ route('workspace.module', $module['key']) }}" aria-label="Buka modul {{ $module['label'] }}">
                <span class="workspace-dashboard-module-glow" aria-hidden="true"></span>
                <div class="workspace-dashboard-module-body">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <span class="workspace-dashboard-module-icon">
                            @include('layouts.partials.module-icon', ['module' => $module['key']])
                        </span>
                        <span class="workspace-dashboard-module-arrow" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M5 12h13M13 6l6 6-6 6"/></svg>
                        </span>
                    </div>
                    <div class="workspace-dashboard-module-copy">
                        <span class="workspace-dashboard-module-kicker">Modul kerja</span>
                        <h2>{{ $module['label'] }}</h2>
                        <p>{{ $module['description'] }}</p>
                    </div>
                    <span class="workspace-dashboard-module-link">Buka modul <span aria-hidden="true">↗</span></span>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endsection
