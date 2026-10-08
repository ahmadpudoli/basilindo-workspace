<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#07111f">
    <title>Basilindo Document Hub — Manajemen Dokumen Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] } } } }</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Active/current navigation must remain readable over the dark hero background. */
        nav > a[href="/admin"] {
            background: rgb(2 6 23 / 0.9);
            border-color: rgb(103 232 249 / 0.45);
            color: rgb(255 255 255);
            box-shadow: 0 8px 24px rgb(2 6 23 / 0.22), inset 0 0 0 1px rgb(255 255 255 / 0.06);
        }

        nav > a[href="/admin"]:hover {
            background: rgb(15 23 42);
            border-color: rgb(103 232 249 / 0.75);
        }

        nav > a[href="/admin"]:focus-visible {
            outline: 2px solid rgb(103 232 249);
            outline-offset: 3px;
        }
    </style>
</head>
<body class="bg-[#07111f] font-sans text-white antialiased">
    <div class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute -left-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-cyan-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute right-0 top-1/3 h-[30rem] w-[30rem] rounded-full bg-blue-600/20 blur-3xl"></div>
        <nav class="relative mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8"><a href="/" class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-cyan-400 text-lg font-bold text-slate-950 shadow-lg shadow-cyan-400/20">B</span><span><span class="block text-lg font-bold tracking-tight">Basilindo</span><span class="block text-[10px] font-semibold uppercase tracking-[0.24em] text-cyan-300">Document Hub</span></span></a><a href="/admin" class="rounded-xl border border-white/15 px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:border-cyan-300/60 hover:bg-white/10 hover:text-white">Masuk ke aplikasi <span aria-hidden="true">→</span></a></nav>
        <main class="relative mx-auto max-w-7xl px-6 pb-20 pt-14 lg:px-8 lg:pt-24"><div class="grid items-center gap-16 lg:grid-cols-[1.08fr_0.92fr]"><div><div class="mb-6 inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-300/10 px-3 py-1.5 text-xs font-semibold text-cyan-200"><span class="h-2 w-2 rounded-full bg-cyan-300 shadow-[0_0_12px_#67e8f9]"></span> Workspace Finance yang lebih tertata</div><h1 class="max-w-3xl text-4xl font-bold leading-[1.08] tracking-tight sm:text-6xl">Arsip rapi. <span class="text-cyan-300">Kontrol pasti.</span> Kerja lebih cepat.</h1><p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">Basilindo Document Hub membantu tim Finance menyimpan, menemukan, memverifikasi, dan membagikan dokumen perusahaan dengan aman.</p><div class="mt-9 flex flex-wrap gap-3"><a href="/admin" class="rounded-xl bg-cyan-400 px-5 py-3.5 text-sm font-bold text-slate-950 shadow-xl shadow-cyan-500/20 transition hover:bg-cyan-300">Buka Document Hub <span aria-hidden="true">↗</span></a><a href="#fitur" class="rounded-xl border border-white/15 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-white/10">Lihat fitur</a></div><div class="mt-12 flex flex-wrap gap-x-8 gap-y-4 text-sm text-slate-400"><span>✓ Akses berbasis peran</span><span>✓ Audit trail terukur</span><span>✓ Penyimpanan privat</span></div></div><div class="relative"><div class="absolute -inset-5 rounded-[2rem] bg-gradient-to-br from-cyan-400/20 to-blue-500/10 blur-2xl"></div><div class="relative rounded-[2rem] border border-white/10 bg-white/[0.07] p-4 shadow-2xl backdrop-blur-xl sm:p-6"><div class="mb-5 flex items-center justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300">Workspace overview</p><p class="mt-1 text-sm text-slate-300">Ringkasan dokumen perusahaan</p></div><span class="rounded-lg bg-emerald-400/10 px-2.5 py-1.5 text-xs font-semibold text-emerald-300">● Aktif</span></div><div class="grid grid-cols-2 gap-3"><div class="rounded-2xl bg-slate-950/50 p-4"><p class="text-xs text-slate-400">Total dokumen</p><p class="mt-2 text-3xl font-bold">1,248</p><p class="mt-2 text-xs text-emerald-300">↑ 12,8% bulan ini</p></div><div class="rounded-2xl bg-slate-950/50 p-4"><p class="text-xs text-slate-400">Perlu review</p><p class="mt-2 text-3xl font-bold">24</p><p class="mt-2 text-xs text-amber-300">Menunggu tindakan</p></div></div><div class="mt-4 rounded-2xl bg-slate-950/50 p-4"><div class="mb-4 flex items-center justify-between"><p class="text-sm font-semibold">Aktivitas dokumen</p><span class="text-xs text-slate-500">7 hari terakhir</span></div><div class="flex h-28 items-end gap-2">@foreach ([38,55,42,72,58,88,68,96,76,84,64,92] as $height)<span class="flex-1 rounded-t-md bg-gradient-to-t from-blue-500 to-cyan-300 opacity-80" style="height: {{ $height }}%"></span>@endforeach</div><div class="mt-3 flex justify-between text-[10px] text-slate-500"><span>Sen</span><span>Rab</span><span>Jum</span><span>Min</span></div></div><div class="mt-4 rounded-2xl border border-cyan-300/10 bg-cyan-300/5 p-4"><p class="text-sm font-semibold">✓ Penyimpanan aman dan terverifikasi</p><p class="mt-1 text-xs text-slate-400">Dokumen terlindungi dengan kontrol akses</p></div></div></div></div><section id="fitur" class="mt-28 border-t border-white/10 pt-14"><div class="max-w-xl"><p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Satu alur kerja</p><h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Dibuat untuk ritme kerja Finance</h2></div><div class="mt-10 grid gap-4 md:grid-cols-3"><div class="rounded-2xl border border-white/10 bg-white/[0.05] p-6"><span class="text-2xl text-cyan-300">⌕</span><h3 class="mt-5 text-lg font-semibold">Temukan lebih cepat</h3><p class="mt-2 text-sm leading-6 text-slate-400">Filter berdasarkan perusahaan, proyek, vendor, periode, jenis, dan nomor referensi.</p></div><div class="rounded-2xl border border-white/10 bg-white/[0.05] p-6"><span class="text-2xl text-cyan-300">◈</span><h3 class="mt-5 text-lg font-semibold">Verifikasi terarah</h3><p class="mt-2 text-sm leading-6 text-slate-400">Hubungkan PO, invoice, dan bukti pembayaran dengan rule yang dapat dijelaskan.</p></div><div class="rounded-2xl border border-white/10 bg-white/[0.05] p-6"><span class="text-2xl text-cyan-300">↗</span><h3 class="mt-5 text-lg font-semibold">Bagikan dengan aman</h3><p class="mt-2 text-sm leading-6 text-slate-400">Buat bundel dokumen, kelola expiry, dan simpan jejak akses secara akuntabel.</p></div></div></section></main>
        <footer class="relative border-t border-white/10 px-6 py-7 lg:px-8"><div class="mx-auto flex max-w-7xl flex-col gap-2 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between"><span>© {{ date('Y') }} Basilindo. Semua hak dilindungi.</span><span>Document management untuk operasional yang lebih tenang.</span></div></footer>
    </div>
</body>
</html>
