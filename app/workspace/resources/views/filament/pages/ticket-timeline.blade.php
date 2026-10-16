<x-filament-panels::page>
    <div class="space-y-4">
        @forelse ($tickets as $ticket)
            <div class="rounded-xl border bg-white p-4 dark:bg-gray-900">
                <div class="flex justify-between"><span class="font-semibold">{{ $ticket->uuid }} — {{ $ticket->name }}</span><span class="text-sm text-gray-500">{{ $ticket->project?->name }}</span></div>
                <div class="mt-3 space-y-2 border-l-2 pl-4">
                    @forelse ($ticket->histories as $history)
                        <div class="text-sm"><span class="font-medium">{{ $history->status?->name ?? 'Status' }}</span> oleh {{ $history->user?->name ?? 'Sistem' }} <span class="text-gray-500">{{ $history->created_at?->format('d/m/Y H:i') }}</span></div>
                    @empty
                        <div class="text-sm text-gray-500">Belum ada perubahan status.</div>
                    @endforelse
                </div>
            </div>
        @empty
            <x-filament::section><p class="text-sm text-gray-500">Belum ada ticket.</p></x-filament::section>
        @endforelse
    </div>
</x-filament-panels::page>
