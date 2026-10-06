@if (count($applications))
    <div x-data="{ open: false }" class="relative mr-2">
        <button type="button" @click="open = ! open" class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800" aria-label="Buka aplikasi">
            <x-heroicon-o-squares-2x2 class="h-5 w-5" />
        </button>
        <div x-show="open" x-cloak @click.outside="open = false" class="absolute right-0 z-50 mt-2 w-64 rounded-xl border border-gray-200 bg-white p-2 shadow-xl dark:border-gray-700 dark:bg-gray-900">
            <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Aplikasi perusahaan</div>
            @foreach ($applications as $application)
                <a href="{{ $application['url'] }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        <x-heroicon-o-squares-2x2 class="h-4 w-4" />
                    </span>
                    <span>{{ $application['name'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif
