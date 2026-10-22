@extends('layouts.app', ['title' => 'Upload Dokumen'])

@section('content')
    <div class="workspace-page-heading">
        <div>
            <h1 class="h3 mb-1">Upload dokumen</h1>
            <p class="text-secondary mb-0">File disimpan ke object storage private dan langsung tersedia setelah validasi upload selesai.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('workspace.documents.store') }}" enctype="multipart/form-data" class="card border-0 shadow-sm" data-client-validation="document" novalidate>
        @csrf
        <div class="card-body p-4 p-xl-5">
            @if ($errors->any())
                <div class="alert alert-danger mb-4" role="alert">
                    <div class="fw-semibold mb-1">Periksa data berikut:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
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
                                <input id="document-upload" type="file" name="upload" class="form-control @error('upload') is-invalid @enderror" accept="application/pdf,image/jpeg,image/png" data-document-upload-input required>
                                @error('upload')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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

                <div class="col-12"><span class="workspace-form-kicker">Informasi utama</span></div>
                <div class="col-md-6"><label for="document-title" class="form-label">Judul <span class="text-danger">*</span></label><input id="document-title" name="title" value="{{ old('title') }}" class="form-control" placeholder="Contoh: Invoice vendor Januari 2026" required></div>
                <div class="col-md-6"><label class="form-label">Tanggal dokumen <span class="text-danger">*</span></label><input type="date" name="document_date" value="{{ old('document_date', now()->format('Y-m-d')) }}" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Perusahaan internal <span class="text-danger">*</span></label><select name="company_id" data-document-company-select class="form-select" required><option value="">Pilih perusahaan</option>@foreach($companies as $id => $name)<option value="{{ $id }}" @selected((string) old('company_id', $defaults['company_id'] ?? '') === (string) $id)>{{ $name }}</option>@endforeach</select></div>
                <livewire:document-metadata-fields :types="$types->map(fn ($type) => ['id' => (string) $type->getKey(), 'name' => $type->name])->values()->all()" :selected="old('document_type_id')" :company-id="old('company_id', $defaults['company_id'] ?? null)" />
                <div class="col-md-6"><label class="form-label">Perusahaan client/vendor</label><div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-single-select data-dropdown-empty-label="Opsional"><button type="button" class="form-select text-start workspace-dropdown-trigger" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span data-dropdown-label>Opsional</span></button><div class="dropdown-menu workspace-dropdown-menu p-2"><input type="search" class="form-control form-control-sm mb-2" placeholder="Cari nama atau kode perusahaan..." data-dropdown-search><div class="workspace-dropdown-options">@forelse($counterpartyCompanies as $counterparty)<label class="workspace-dropdown-option" data-dropdown-option><input type="checkbox" name="counterparty_company_id" value="{{ $counterparty->id }}" data-dropdown-item data-dropdown-value="{{ $counterparty->code }} — {{ $counterparty->name }}" @checked((string) old('counterparty_company_id') === (string) $counterparty->id)><span><strong>{{ $counterparty->code }}</strong><small>{{ $counterparty->name }} · {{ collect($counterparty->roles ?? [])->map(fn ($role) => str($role)->headline())->join(', ') }}</small></span></label>@empty<div class="text-secondary small p-2">Belum ada perusahaan client/vendor yang tersedia.</div>@endforelse</div></div></div></div>
                <div class="col-md-6"><label class="form-label">Project</label><select name="project_id" class="form-select"><option value="">Opsional</option>@foreach($projects as $id => $name)<option value="{{ $id }}" @selected((string) old('project_id', $defaults['project_id'] ?? '') === (string) $id)>{{ $name }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Nomor referensi</label><input name="reference_number" value="{{ old('reference_number') }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Tag</label><div class="workspace-tag-input" data-tag-input><input type="hidden" name="tags" value="{{ old('tags') }}" data-tag-value><div class="workspace-tag-chips" data-tag-chips></div><input type="text" class="workspace-tag-entry" placeholder="Ketik tag lalu tekan Enter" data-tag-entry autocomplete="off"></div><div class="form-text">Tekan Enter untuk menambahkan tag.</div></div>
                <div class="col-md-6"><label class="form-label">Mata uang</label><select name="currency" class="form-select"><option value="">Opsional</option>@foreach(['IDR', 'USD', 'SGD'] as $currency)<option value="{{ $currency }}" @selected(old('currency') === $currency)>{{ $currency }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Nominal</label><input name="amount" value="{{ \App\Modules\DocumentManagement\Application\Services\Documents\DocumentMetadataValueNormalizer::formatIndonesian(old('amount')) }}" class="form-control" inputmode="decimal" data-local-decimal placeholder="0,00"><div class="form-text">Gunakan format Indonesia, misalnya 1.250.000,50.</div></div>
            </div>
        </div>

        <div class="card-footer bg-white p-4 p-xl-5 pt-0 border-0">
            <button class="btn btn-primary" type="submit">Upload dan simpan</button>
        </div>
    </form>
@endsection
