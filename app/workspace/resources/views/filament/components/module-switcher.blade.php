@php
    $activeModule = session('workspace.active_module');
    $modules = [
        'core' => 'Core & Company',
        'crm' => 'CRM',
        'project-management' => 'Project Management',
        'documents' => 'Document Management',
    ];
@endphp

<div class="relative mr-2" x-data="{ open: false }" @click.outside="open = false">
    <button type="button" @click="open = ! open" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-cyan-300 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
        <x-filament::icon icon="heroicon-o-squares-2x2" class="h-4 w-4 text-cyan-500" />
        <span>{{ $activeModule ? $modules[$activeModule] : 'Pilih modul' }}</span>
        <x-filament::icon icon="heroicon-m-chevron-down" class="h-4 w-4" />
    </button>

    <div x-cloak x-show="open" x-transition class="absolute right-0 z-50 mt-2 w-64 overflow-hidden rounded-2xl border border-gray-200 bg-white p-2 shadow-xl dark:border-white/10 dark:bg-gray-900">
        <a href="{{ route('workspace.launcher') }}" class="mb-1 block rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5">Basilindo Workspace</a>
        @foreach ($modules as $key => $label)
            <a href="{{ route('workspace.module', ['module' => $key]) }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-sm text-gray-600 hover:bg-cyan-50 hover:text-cyan-700 dark:text-gray-300 dark:hover:bg-cyan-400/10 dark:hover:text-cyan-300">
                <span>{{ $label }}</span>
                @if ($activeModule === $key)
                    <x-filament::icon icon="heroicon-m-check" class="h-4 w-4 text-cyan-500" />
                @endif
            </a>
        @endforeach
    </div>
</div>
