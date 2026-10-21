@extends('layouts.app', ['title' => 'Modul aktif'])
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><div class="text-uppercase text-secondary small fw-semibold mb-2">Pengaturan platform</div><h1 class="h3 mb-1">Modul aktif</h1><p class="text-secondary mb-0">Modul nonaktif tidak ditampilkan di launcher dan halaman pilih modul.</p></div><a href="{{ route('workspace.settings') }}" class="btn btn-outline-secondary">Kembali</a></div>
<form method="POST" action="{{ route('workspace.settings.modules.update') }}" class="card border-0 shadow-sm"><div class="card-body">@csrf @method('PUT')<div class="row g-3">
    @foreach([
        ['key' => 'documents', 'label' => 'Document Management', 'description' => 'Dokumen, metadata, verifikasi, dan bundel.'],
        ['key' => 'crm', 'label' => 'CRM', 'description' => 'Account, contact, lead, opportunity, dan aktivitas.'],
        ['key' => 'project-management', 'label' => 'Project Management', 'description' => 'Project, ticket, board, timeline, dan laporan.'],
        ['key' => 'company', 'label' => 'Company', 'description' => 'Perusahaan, vendor, dan company scope.'],
    ] as $module)
        <div class="col-md-6"><label class="workspace-settings-module-option"><span><strong>{{ $module['label'] }}</strong><small>{{ $module['description'] }}</small></span><input type="checkbox" name="active_modules[]" value="{{ $module['key'] }}" @checked(in_array($module['key'], $enabledModules, true))><span class="workspace-settings-toggle"></span></label></div>
    @endforeach
</div><div class="form-text mt-3">Minimal satu modul harus tetap aktif. Pengaturan ini berlaku untuk seluruh pengguna Workspace.</div></div><div class="card-footer bg-white"><button class="btn btn-primary">Simpan perubahan</button></div></form>
@endsection
