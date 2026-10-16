<div class="space-y-3">
    <p class="text-sm text-gray-500 dark:text-gray-400">
        Preview file ditampilkan secara privat dan tetap melewati otorisasi dokumen.
    </p>

    <iframe
        src="{{ $previewUrl }}"
        title="Preview {{ $document->original_filename }}"
        class="h-[75vh] min-h-[480px] w-full rounded-lg border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-900"
        loading="eager"
    ></iframe>
</div>
