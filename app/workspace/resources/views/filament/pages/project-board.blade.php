<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex flex-wrap gap-2">
            @foreach ($projects as $project)
                <a href="{{ url('/project-board?project_id='.$project->id) }}" class="rounded-lg border px-3 py-2 text-sm {{ $projectId == $project->id ? 'bg-primary-600 text-white' : 'bg-white' }}">{{ $project->name }}</a>
            @endforeach
        </div>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($statuses as $status)
                <section class="rounded-xl border bg-gray-50 p-4 dark:bg-gray-900">
                    <h2 class="mb-3 font-semibold">{{ $status->name }} <span class="text-sm text-gray-500">({{ $tickets->where('ticket_status_id', $status->id)->count() }})</span></h2>
                    <div class="space-y-3">
                        @foreach ($tickets->where('ticket_status_id', $status->id) as $ticket)
                            <a href="{{ url('/tickets/'.$ticket->id) }}" class="block rounded-lg bg-white p-3 shadow-sm dark:bg-gray-800">
                                <div class="text-xs text-gray-500">{{ $ticket->uuid }}</div>
                                <div class="font-medium">{{ $ticket->name }}</div>
                                <div class="mt-2 text-xs text-gray-500">{{ $ticket->priority?->name ?? 'Tanpa prioritas' }}</div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
