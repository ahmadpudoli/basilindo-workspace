<div class="claim-image-viewer workspace-static-image-viewer" data-claim-image-viewer style="height:70vh;min-height:300px;padding:.75rem">
    <div class="claim-image-zoom-toolbar" data-claim-image-zoom-toolbar hidden>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-claim-zoom-out aria-label="Perkecil gambar">−</button>
        <span data-claim-zoom-level aria-live="polite">100%</span>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-claim-zoom-in aria-label="Perbesar gambar">+</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-claim-zoom-reset>Sesuaikan</button>
    </div>
    <iframe class="claim-attachment-preview-frame" src="{{ $src }}" title="{{ $title ?? 'Preview gambar' }}" referrerpolicy="no-referrer" style="flex:1;height:auto;min-height:0"></iframe>
</div>
