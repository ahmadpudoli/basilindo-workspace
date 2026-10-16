<x-filament-panels::page>
    <x-filament::section heading="Kontribusi pengguna">
        <div class="divide-y">@foreach ($users as $item)<div class="flex justify-between py-3"><span>{{ $item['user']->name }}</span><span class="text-sm text-gray-500">{{ $item['comments'] }} komentar · {{ $item['status_updates'] }} perubahan status</span></div>@endforeach</div>
    </x-filament::section>
</x-filament-panels::page>
