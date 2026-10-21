@extends('layouts.app', ['title' => 'Upload Dokumen'])

@section('content')
    <div class="workspace-page-heading">
        <div>
            <h1 class="h3 mb-1">Upload dokumen</h1>
            <p class="text-secondary mb-0">File disimpan ke object storage private dan masuk proses quarantine.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('workspace.documents.store') }}" enctype="multipart/form-data" class="card border-0 shadow-sm">
        @csrf
        <div class="card-body p-4 p-xl-5">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="workspace-document-intake-panel h-100" data-document-upload>
                        <div class="workspace-document-intake-heading">
                            <div>
                                <span class="workspace-form-kicker">Lampiran</span>
                                <label for="document-upload" class="form-label mb-1">File dokumen <span class="text-danger">*</span></label>
                                <p class="form-text mb-0">PDF, JPG, atau PNG. Maksimal 50 MB.</p>
                            </div>
                            <span class="workspace-document-intake-required">Wajib</span>
                        </div>
                        <div class="workspace-document-upload-pair mt-3">
                            <div class="workspace-document-upload-control">
                                <input id="document-upload" type="file" name="upload" class="form-control" accept="application/pdf,image/jpeg,image/png" data-document-upload-input required>
                            </div>
                            <div class="workspace-document-upload-preview" data-document-upload-preview aria-live="polite">
                                <div class="workspace-document-preview-empty">
                                    <span class="workspace-document-preview-empty-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6zM14 3v5h5M9 13h6M9 17h4"/></svg>
                                    </span>
                                    <span>Pilih file untuk melihat preview</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="workspace-document-intake-panel workspace-document-description-panel h-100">
                        <div class="workspace-document-intake-heading">
                            <div>
                                <span class="workspace-form-kicker">Ringkasan</span>
                                <label for="document-description" class="form-label mb-1">Deskripsi</label>
                                <p class="form-text mb-0">Tambahkan konteks, catatan, atau informasi penting.</p>
                            </div>
                        </div>
                        <textarea id="document-description" name="description" class="form-control workspace-richtext-editor mt-3" rows="8" placeholder="Tulis deskripsi dokumen...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="col-12">
                    <div class="workspace-form-section">
                        <span class="workspace-form-kicker">Informasi utama</span>
                        <label for="document-title" class="form-label">Judul <span class="text-danger">*</span></label>
                        <input id="document-title" name="title" value="{{ old('title') }}" class="form-control" placeholder="Contoh: Invoice vendor Januari 2026" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Perusahaan internal <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-select" required>
                        <option value="">Pilih perusahaan</option>
                        @foreach($companies as $id => $name)
                            <option value="{{ $id }}" @selected((string) old('company_id', $defaults['company_id'] ?? '') === (string) $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <livewire:document-metadata-fields :types="$types->map(fn ($type) => ['id' => (string) $type->getKey(), 'name' => $type->name])->values()->all()" :selected="old('document_type_id')" />

                <div class="col-md-6">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select">
                        <option value="">Opsional</option>
                        @foreach($projects as $id => $name)
                            <option value="{{ $id }}" @selected((string) old('project_id', $defaults['project_id'] ?? '') === (string) $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">CRM account</label>
                    <input type="text" name="crm_account_id" value="{{ old('crm_account_id', $defaults['crm_account_id'] ?? '') }}" class="form-control" placeholder="ID account bila tersedia">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tanggal dokumen <span class="text-danger">*</span></label>
                    <input type="date" name="document_date" value="{{ old('document_date', now()->format('Y-m-d')) }}" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nomor referensi</label>
                    <input name="reference_number" value="{{ old('reference_number') }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nominal</label>
                    <input name="amount" value="{{ old('amount') }}" class="form-control" inputmode="decimal">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Mata uang</label>
                    <select name="currency" class="form-select">
                        <option value="">Opsional</option>
                        @foreach(['IDR', 'USD', 'SGD'] as $currency)
                            <option value="{{ $currency }}" @selected(old('currency') === $currency)>{{ $currency }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Tag</label>
                    <input name="tags" value="{{ old('tags') }}" class="form-control" placeholder="invoice, vendor, 2026">
                    <div class="form-text">Pisahkan tag dengan koma.</div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white p-4 p-xl-5 pt-0 border-0">
            <button class="btn btn-primary" type="submit">Upload dan simpan</button>
        </div>
    </form>
@endsection
