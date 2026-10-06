<x-filament-panels::page>
    @php($data = $this->getViewData())

    <div class="space-y-6">
        <section class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-8 text-white shadow-xl sm:px-8">
            <div class="absolute -right-16 -top-24 h-72 w-72 rounded-full bg-cyan-400/20 blur-3xl"></div>
            <div class="absolute -bottom-32 left-1/3 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="mb-3 text-sm font-semibold uppercase tracking-[0.22em] text-cyan-300">Basilindo Document Hub</p>
                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">Semua dokumen penting, tertata dalam satu tempat.</h1>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300">Kelola arsip Finance, temukan referensi lebih cepat, dan pastikan setiap dokumen melewati proses verifikasi yang jelas.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('filament.admin.resources.documents.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-cyan-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                        <x-filament::icon icon="heroicon-m-arrow-up-tray" class="h-5 w-5" />
                        Unggah dokumen
                    </a>
                    <a href="{{ route('filament.admin.resources.documents.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/20 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        <x-filament::icon icon="heroicon-m-magnifying-glass" class="h-5 w-5" />
                        Cari arsip
                    </a>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Total dokumen', 'value' => $data['stats']['total'], 'hint' => 'Seluruh arsip terotorisasi', 'icon' => 'heroicon-o-document-text', 'tone' => 'blue'],
                ['label' => 'Dokumen siap', 'value' => $data['stats']['ready'], 'hint' => 'Siap digunakan atau diunduh', 'icon' => 'heroicon-o-check-circle', 'tone' => 'emerald'],
                ['label' => 'Perlu perhatian', 'value' => $data['stats']['pending'], 'hint' => 'Menunggu proses berikutnya', 'icon' => 'heroicon-o-clock', 'tone' => 'amber'],
                ['label' => 'Dalam review', 'value' => $data['stats']['review'], 'hint' => 'Kasus verifikasi terbuka', 'icon' => 'heroicon-o-shield-exclamation', 'tone' => 'violet'],
            ] as $stat)
                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</p>
                            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ number_format($stat['value']) }}</p>
                        </div>
                        <div class="rounded-xl bg-{{ $stat['tone'] }}-50 p-3 text-{{ $stat['tone'] }}-600 dark:bg-{{ $stat['tone'] }}-400/10 dark:text-{{ $stat['tone'] }}-300">
                            <x-filament::icon :icon="$stat['icon']" class="h-6 w-6" />
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">{{ $stat['hint'] }}</p>
                </div>
            @endforeach
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
            <section class="rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-white/10 dark:bg-white/5">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-white/10">
                    <div><h2 class="font-semibold text-slate-900 dark:text-white">Dokumen terbaru</h2><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Arsip yang baru ditambahkan ke workspace Anda</p></div>
                    <a href="{{ route('filament.admin.resources.documents.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-500">Lihat semua</a>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-white/10">
                    @forelse ($data['recentDocuments'] as $document)
                        <a href="{{ route('filament.admin.resources.documents.index') }}" class="flex items-center gap-4 px-5 py-4 transition hover:bg-slate-50 dark:hover:bg-white/5">
                            <div class="rounded-xl bg-blue-50 p-3 text-blue-600 dark:bg-blue-400/10 dark:text-blue-300"><x-filament::icon icon="heroicon-o-document" class="h-5 w-5" /></div>
                            <div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $document->title }}</p><p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $document->company?->name ?? 'Tanpa perusahaan' }} · {{ $document->documentType?->name ?? 'Dokumen' }}</p></div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $document->status === 'ready' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300' }}">{{ ucfirst($document->status) }}</span>
                        </a>
                    @empty
                        <div class="px-5 py-12 text-center"><x-filament::icon icon="heroicon-o-folder-open" class="mx-auto h-10 w-10 text-slate-300" /><p class="mt-3 text-sm font-medium text-slate-600 dark:text-slate-300">Belum ada dokumen</p><p class="mt-1 text-xs text-slate-500">Mulai dengan mengunggah arsip pertama Anda.</p></div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-white/10 dark:bg-white/5">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-white/10"><h2 class="font-semibold text-slate-900 dark:text-white">Aktivitas terbaru</h2><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Jejak aktivitas workspace</p></div>
                <div class="space-y-5 p-5">
                    @forelse ($data['activity'] as $event)
                        <div class="flex gap-3"><div class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-cyan-400 ring-4 ring-cyan-400/10"></div><div class="min-w-0"><p class="truncate text-sm text-slate-700 dark:text-slate-200">{{ str_replace('_', ' ', ucfirst($event->action)) }}</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $event->actor?->name ?? 'Sistem' }} · {{ $event->created_at?->diffForHumans() }}</p></div></div>
                    @empty
                        <div class="py-8 text-center text-sm text-slate-500">Aktivitas akan tampil di sini.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-filament-panels::page>
