<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($epics as $epic)
            <div class="rounded-xl border bg-white p-4 dark:bg-gray-900"><div class="font-semibold">{{ $epic->name }}</div><div class="text-sm text-gray-500">{{ $epic->project?->name }} · {{ $epic->tickets_count }} ticket</div><p class="mt-3 text-sm">{{ $epic->description }}</p></div>
        @empty
            <x-filament::section><p class="text-sm text-gray-500">Belum ada epic.</p></x-filament::section>
        @endforelse
    </div>
</x-filament-panels::page>
