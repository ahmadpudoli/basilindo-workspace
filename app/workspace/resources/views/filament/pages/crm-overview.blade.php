<x-filament-panels::page>
    @if ($companyCount === 0)
        <x-filament::section icon="heroicon-o-information-circle" icon-color="warning">
            <x-slot name="heading">Company scope belum tersedia</x-slot>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Role CRM sudah aktif, tetapi user belum memiliki assignment perusahaan. Minta admin menambahkan perusahaan pada profil user sebelum mengelola data CRM.
            </p>
        </x-filament::section>
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        @foreach ($links as $link)
            <a href="{{ $link['url'] }}" class="block rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-primary-400 hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">{{ $link['label'] }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $link['description'] }}</p>
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
