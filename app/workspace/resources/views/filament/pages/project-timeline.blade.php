<x-filament-panels::page>
    <div class="space-y-4">
        @forelse ($projects as $project)
            @php($days = max(1, $project->start_date->diffInDays($project->end_date) + 1))
            <div class="rounded-xl border bg-white p-4 dark:bg-gray-900">
                <div class="flex justify-between gap-4"><span class="font-semibold">{{ $project->name }}</span><span class="text-sm text-gray-500">{{ $project->start_date->format('d/m/Y') }} - {{ $project->end_date->format('d/m/Y') }}</span></div>
                <div class="mt-3 h-3 rounded-full bg-gray-200"><div class="h-3 rounded-full bg-primary-600" style="width: {{ min(100, max(0, now()->diffInDays($project->start_date, false) * -100 / $days)) }}%"></div></div>
                <div class="mt-2 text-sm text-gray-500">{{ $project->tickets_count }} ticket</div>
            </div>
        @empty
            <x-filament::section><p class="text-sm text-gray-500">Belum ada project dengan rentang tanggal.</p></x-filament::section>
        @endforelse
    </div>
</x-filament-panels::page>
