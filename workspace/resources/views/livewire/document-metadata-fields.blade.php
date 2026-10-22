<div class="workspace-metadata-fields" data-document-metadata-fields>
    <div class="col-md-6">
        <label class="form-label">Jenis dokumen *</label>
        <div class="workspace-document-type-loading position-relative">
            <select name="document_type_id" wire:model.live="documentTypeId" class="form-select" required @disabled(blank($companyId))>
                <option value="">{{ blank($companyId) ? 'Pilih perusahaan internal terlebih dahulu' : 'Pilih jenis dokumen' }}</option>
            @foreach($types as $id => $name)<option value="{{ $id }}">{{ $name }}</option>@endforeach
            </select>
            <span class="workspace-document-loading-indicator" wire:loading aria-label="Memuat">Memuat...</span>
        </div>
        @error('document_type_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div wire:loading wire:target="documentTypeId" class="col-12 workspace-metadata-loading" aria-live="polite">
        <div class="workspace-metadata-panel-inner workspace-metadata-loading-inner"><span class="workspace-loading-spinner" aria-hidden="true"></span><span>Memuat metadata tambahan...</span></div>
    </div>
    @if(count($definitions) > 0)
        <div class="col-12 workspace-metadata-panel">
            <div class="workspace-metadata-panel-inner">
                <div class="workspace-metadata-panel-heading">
                    <div>
                        <span class="workspace-form-kicker">Metadata tambahan</span>
                        <h2 class="h6 mb-1">Field mengikuti jenis dokumen</h2>
                        <p class="text-secondary mb-0">Isi metadata yang didefinisikan pada tipe dokumen terpilih.</p>
                    </div>
                    <span class="workspace-metadata-panel-count">{{ count($definitions) }} field</span>
                </div>
                <div class="row g-3 mt-1">
                    @foreach($definitions as $field => $config)
                        @php($metadataType = $config['type'] ?? 'string')
                        @php($metadataValue = old('metadata.'.$field, $metadata[$field] ?? ''))
                        <div class="col-md-6">
                            <label class="form-label">{{ str($field)->headline() }} @if($config['mandatory'])*@endif</label>
                            @if($metadataType === 'text')
                                <textarea name="metadata[{{ $field }}]" class="form-control" rows="3" @required($config['mandatory'])>{{ old('metadata.'.$field, $metadata[$field] ?? '') }}</textarea>
                            @elseif($metadataType === 'boolean')
                                <input type="hidden" name="metadata[{{ $field }}]" value="0">
                                <div class="form-check workspace-metadata-boolean"><input type="checkbox" name="metadata[{{ $field }}]" value="1" class="form-check-input" id="metadata-{{ str($field)->slug() }}" @checked((bool) old('metadata.'.$field, $metadata[$field] ?? false))><label class="form-check-label" for="metadata-{{ str($field)->slug() }}">Ya</label></div>
                            @else
                                <input name="metadata[{{ $field }}]" value="{{ in_array($metadataType, ['numeric', 'numeric_decimal'], true) ? \App\Modules\DocumentManagement\Application\Services\Documents\DocumentMetadataValueNormalizer::formatIndonesian($metadataValue) : $metadataValue }}" class="form-control" @required($config['mandatory']) @if(in_array($metadataType, ['numeric','numeric_decimal'], true)) type="text" inputmode="decimal" data-local-decimal data-metadata-type="{{ $metadataType }}" @elseif($metadataType === 'date') type="date" @elseif($metadataType === 'datetime') type="datetime-local" @else type="text" @endif>
                            @endif
                            @error('metadata.'.$field)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
