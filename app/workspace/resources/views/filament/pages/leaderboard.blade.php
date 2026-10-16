<x-filament-panels::page>
    <x-filament::section heading="Kontributor teratas">
        <div class="divide-y">@foreach ($users as $index => $item)<div class="flex justify-between py-3"><span>{{ $index + 1 }}. {{ $item['user']->name }}</span><span class="text-sm text-gray-500">{{ $item['comments'] }} komentar · {{ $item['updates'] }} perubahan</span></div>@endforeach</div>
    </x-filament::section>
</x-filament-panels::page>
