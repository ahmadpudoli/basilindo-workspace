<x-filament-panels::page>
    @php($data = $this->getViewData())

    @if ($data['activeModule'])
        <div class="mx-auto max-w-6xl space-y-6">
            <section class="rounded-3xl bg-slate-950 px-6 py-8 text-white shadow-xl sm:px-8">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-cyan-300">{{ $data['activeModule']['label'] }}</p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Dashboard {{ $data['activeModule']['label'] }}</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">{{ $data['activeModule']['description'] }}</p>
            </section>

            <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($data['activeLinks'] as $link)
                    <a href="{{ $link['url'] }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-md dark:border-white/10 dark:bg-white/5">
                        <x-filament::icon :icon="$link['icon']" class="h-6 w-6 text-cyan-600 dark:text-cyan-300" />
                        <h2 class="mt-5 text-base font-bold text-slate-900 dark:text-white">{{ $link['label'] }}</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $link['description'] }}</p>
                        <span class="mt-4 inline-flex text-sm font-semibold text-cyan-600 dark:text-cyan-300">Buka <span class="ml-2 transition group-hover:translate-x-1">→</span></span>
                    </a>
                @endforeach
            </section>
        </div>
    @else
        <div class="mx-auto max-w-6xl space-y-8">
            <section class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-10 text-white shadow-xl sm:px-10">
                <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-cyan-400/20 blur-3xl"></div>
                <div class="absolute -bottom-32 left-1/3 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>
                <div class="relative max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-cyan-300">Basilindo Workspace</p>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Pilih aplikasi yang ingin Anda gunakan</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-300">Workspace adalah launcher terpadu. Pilih modul untuk membuka halaman kerja dan menu yang relevan dengan tugas Anda.</p>
                </div>
            </section>

            <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($data['modules'] as $module)
                    <a href="{{ route('workspace.module', ['module' => $module['key']]) }}" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-md dark:border-white/10 dark:bg-white/5">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-{{ $module['tone'] }}-50 text-{{ $module['tone'] }}-600 dark:bg-{{ $module['tone'] }}-400/10 dark:text-{{ $module['tone'] }}-300">
                            <x-filament::icon :icon="$module['icon']" class="h-5 w-5" />
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $module['label'] }}</span>
                    </a>
                @endforeach
            </section>
        </div>
    @endif
</x-filament-panels::page>
