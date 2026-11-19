@extends('layouts.app', ['title' => 'Workspace'])

@section('content')
<style>
.workspace-dashboard-module-grid{--bs-gutter-x:1rem;--bs-gutter-y:1rem}.workspace-dashboard-module-grid .workspace-dashboard-module-column{width:50%;flex:0 0 50%}.workspace-dashboard-module-grid .workspace-dashboard-module{min-height:176px;border-radius:16px;background:linear-gradient(145deg,#fff 0%,#fbfdff 100%);box-shadow:0 7px 20px rgba(28,49,76,.055)}.workspace-dashboard-module-grid .workspace-dashboard-module-body{padding:16px 15px 13px}.workspace-dashboard-module-grid .workspace-dashboard-module-icon{width:42px;height:42px;border-radius:12px}.workspace-dashboard-module-grid .workspace-dashboard-module-icon svg{width:22px;height:22px}.workspace-dashboard-module-grid .workspace-dashboard-module-arrow{width:30px;height:30px}.workspace-dashboard-module-grid .workspace-dashboard-module-copy{margin-top:16px}.workspace-dashboard-module-grid .workspace-dashboard-module-kicker{font-size:.6rem;letter-spacing:.09em}.workspace-dashboard-module-grid .workspace-dashboard-module h2{margin:5px 0;font-size:.94rem;line-height:1.25}.workspace-dashboard-module-grid .workspace-dashboard-module p{display:-webkit-box;overflow:hidden;font-size:.73rem;line-height:1.4;-webkit-box-orient:vertical;-webkit-line-clamp:2}.workspace-dashboard-module-grid .workspace-dashboard-module-link{padding-top:12px;font-size:.71rem}.workspace-dashboard-module-grid .workspace-dashboard-module:hover,.workspace-dashboard-module-grid .workspace-dashboard-module:focus-visible{transform:translateY(-4px);box-shadow:0 14px 28px rgba(28,49,76,.12)}@media(min-width:576px){.workspace-dashboard-module-grid .workspace-dashboard-module-column{width:33.3333%;flex:0 0 33.3333%}}@media(min-width:768px){.workspace-dashboard-module-grid .workspace-dashboard-module-column{width:25%;flex:0 0 25%}}@media(min-width:1200px){.workspace-dashboard-module-grid .workspace-dashboard-module-column{width:16.6667%;flex:0 0 16.6667%}}@media(max-width:575.98px){.workspace-dashboard-module-grid{--bs-gutter-x:.65rem;--bs-gutter-y:.65rem}.workspace-dashboard-module-grid .workspace-dashboard-module{min-height:158px;border-radius:13px}.workspace-dashboard-module-grid .workspace-dashboard-module-body{padding:12px 11px 10px}.workspace-dashboard-module-grid .workspace-dashboard-module-icon{width:36px;height:36px}.workspace-dashboard-module-grid .workspace-dashboard-module-arrow{width:26px;height:26px}.workspace-dashboard-module-grid .workspace-dashboard-module-copy{margin-top:11px}.workspace-dashboard-module-grid .workspace-dashboard-module h2{font-size:.84rem}.workspace-dashboard-module-grid .workspace-dashboard-module p{font-size:.68rem;line-height:1.35}.workspace-dashboard-module-grid .workspace-dashboard-module-link{padding-top:9px;font-size:.66rem}}
</style>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <p class="text-primary fw-semibold mb-1">Basilindo Workspace</p>
        <h1 class="h3 mb-1">Pilih modul</h1>
        <p class="text-secondary mb-0">Satu login untuk seluruh pekerjaan Anda.</p>
    </div>
</div>
<div class="row g-3 workspace-dashboard-module-grid">
    @foreach ($modules as $module)
        @php($moduleClass = str_replace('_', '-', $module['key']))
        <div class="col-6 col-sm-4 col-md-3 col-xl-2 workspace-dashboard-module-column">
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
