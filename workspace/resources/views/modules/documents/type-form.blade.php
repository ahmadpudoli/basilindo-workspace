@extends('layouts.app', ['title' => ($type ?? null) ? 'Edit tipe dokumen' : 'Tambah tipe dokumen'])
@section('content')
@php
    $type = $type ?? null;
    $isEdit = $type !== null;
    $typeRequiredFields = old('required_fields', $type?->required_fields ?? []);
    if (is_string($typeRequiredFields)) {
        $typeRequiredFields = json_decode($typeRequiredFields, true) ?: [];
    }
    $metadataFields = [];
    foreach ((array) $typeRequiredFields as $fieldName => $config) {
        if (is_int($fieldName) && is_array($config)) {
            $metadataFields[] = ['field_name' => $config['field_name'] ?? $config['name'] ?? '', 'type' => $config['type'] ?? 'string', 'mandatory' => filter_var($config['mandatory'] ?? true, FILTER_VALIDATE_BOOLEAN)];
        } else {
            $metadataFields[] = ['field_name' => (string) $fieldName, 'type' => is_array($config) ? ($config['type'] ?? 'string') : (string) $config, 'mandatory' => is_array($config) ? filter_var($config['mandatory'] ?? true, FILTER_VALIDATE_BOOLEAN) : true];
        }
    }
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><div class="text-uppercase text-secondary small fw-semibold mb-2">Konfigurasi dokumen</div><h1 class="h3 mb-1">{{ $isEdit ? 'Edit tipe dokumen' : 'Tambah tipe dokumen' }}</h1><p class="text-secondary mb-0">Atur field metadata yang akan muncul saat dokumen diinput.</p></div><a href="{{ route('workspace.documents.types') }}" class="btn btn-outline-secondary">Kembali</a></div>
<form method="POST" action="{{ $isEdit ? route('workspace.documents.types.update', $type) : route('workspace.documents.types.store') }}" class="card border-0 shadow-sm" data-metadata-form>
    <div class="card-body">
        @csrf @if($isEdit) @method('PUT') @endif
        <div class="row g-3"><div class="col-md-6"><label class="form-label">Nama *</label><input name="name" value="{{ old('name', $type?->name ?? '') }}" class="form-control" required></div><div class="col-md-6"><label class="form-label">Kode *</label><input name="code" value="{{ old('code', $type?->code ?? '') }}" class="form-control" required></div><div class="col-md-6"><label class="form-label">Perusahaan</label><select name="company_id" class="form-select"><option value="">Global</option>@foreach($companies as $id => $name)<option value="{{ $id }}" @selected((string) old('company_id', $type?->company_id ?? '') === (string) $id)>{{ $name }}</option>@endforeach</select></div></div>
        <div class="workspace-metadata-editor mt-4" data-metadata-editor>
            <div class="workspace-metadata-editor-heading"><div><h2 class="h6 mb-1">Metadata tambahan</h2><p class="text-secondary small mb-0">Tambahkan field dan tarik baris untuk mengatur urutan tampilnya.</p></div><button type="button" class="btn btn-sm btn-outline-primary" data-metadata-add>+ Tambah metadata</button></div>
            <div class="workspace-metadata-list" data-metadata-list></div>
            <div class="workspace-metadata-empty" data-metadata-empty>Belum ada metadata tambahan. Klik <strong>Tambah metadata</strong> untuk membuat field pertama.</div>
            <textarea name="required_fields" class="d-none" data-metadata-json>{{ json_encode($metadataFields, JSON_UNESCAPED_UNICODE) }}</textarea>
        </div>
        <div class="form-check mt-4"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="document-type-active" @checked(old('is_active', $type?->is_active ?? true))><label class="form-check-label" for="document-type-active">Aktif</label></div>
    </div>
    <div class="card-footer bg-white d-flex gap-2"><button class="btn btn-primary">Simpan tipe dokumen</button><a href="{{ route('workspace.documents.types') }}" class="btn btn-outline-secondary">Batal</a></div>
</form>
@endsection
