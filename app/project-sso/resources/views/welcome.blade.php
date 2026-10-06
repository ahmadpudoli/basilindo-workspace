<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7fafc">
    <meta name="description" content="Basilindo Access adalah pintu masuk aman ke aplikasi internal perusahaan.">
    <title>Basilindo Access &mdash; Akses Aplikasi Internal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['DM Sans', 'ui-sans-serif', 'system-ui'] } } } };</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>:focus-visible { outline: 3px solid #0891b2; outline-offset: 4px; }</style>
</head>
<body class="bg-[#f7fafc] font-sans text-slate-900 antialiased">
    <div class="min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute -right-24 -top-32 h-[34rem] w-[34rem] rounded-full bg-cyan-100 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-28 h-[28rem] w-[28rem] rounded-full bg-blue-100/70 blur-3xl"></div>
        <header class="relative mx-auto flex max-w-6xl items-center justify-between px-6 py-6 lg:px-8">
            <a href="/" class="flex items-center gap-3" aria-label="Basilindo Access, beranda"><span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-900 text-lg font-bold text-cyan-300 shadow-lg shadow-slate-900/10">B</span><span><span class="block text-lg font-bold tracking-tight">Basilindo</span><span class="block text-[10px] font-semibold uppercase tracking-[0.24em] text-cyan-700">Access</span></span></a>
            <span class="hidden rounded-full border border-slate-200 bg-white/70 px-3 py-1.5 text-xs font-semibold text-slate-500 sm:inline-flex">Internal identity platform</span>
        </header>
        <main class="relative mx-auto max-w-6xl px-6 pb-20 pt-12 lg:px-8 lg:pt-20">
            <div class="grid items-center gap-12 lg:grid-cols-[1fr_0.8fr] lg:gap-24">
                <section><div class="mb-6 inline-flex items-center gap-2 rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1.5 text-xs font-semibold text-cyan-800"><span class="h-2 w-2 rounded-full bg-cyan-500" aria-hidden="true"></span> Akses terpusat untuk tim Basilindo</div><h1 class="max-w-2xl text-4xl font-bold leading-[1.08] tracking-tight sm:text-6xl">Satu akun untuk <span class="text-cyan-700">semua pekerjaan penting.</span></h1><p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">Basilindo Access menjaga pintu masuk aplikasi internal tetap sederhana, aman, dan mudah dikelola oleh tim Anda.</p><div class="mt-9 flex flex-wrap gap-3"><a href="/admin" class="rounded-xl bg-slate-900 px-5 py-3.5 text-sm font-bold text-white shadow-xl shadow-slate-900/15 transition hover:bg-slate-800">Masuk ke Basilindo Access <span aria-hidden="true">&rarr;</span></a><a href="/auth/google" class="rounded-xl border border-slate-300 bg-white px-5 py-3.5 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:bg-cyan-50">Gunakan Google Workspace</a></div><div class="mt-12 grid max-w-xl gap-4 sm:grid-cols-3"><div><p class="text-sm font-bold text-slate-900">Terpusat</p><p class="mt-1 text-sm leading-6 text-slate-500">Identitas dikelola dari satu tempat.</p></div><div><p class="text-sm font-bold text-slate-900">Terkontrol</p><p class="mt-1 text-sm leading-6 text-slate-500">Akses mengikuti peran dan aplikasi.</p></div><div><p class="text-sm font-bold text-slate-900">Terukur</p><p class="mt-1 text-sm leading-6 text-slate-500">Aktivitas login dapat diaudit.</p></div></div></section>
                <section class="relative" aria-label="Ringkasan keamanan Basilindo Access"><div class="absolute -inset-6 rounded-[2rem] bg-gradient-to-br from-cyan-200/70 to-blue-100/50 blur-2xl"></div><div class="relative rounded-[2rem] border border-white/80 bg-white/80 p-5 shadow-2xl shadow-slate-300/30 backdrop-blur-xl sm:p-7"><div class="flex items-center justify-between border-b border-slate-100 pb-5"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-700">Basilindo Access</p><p class="mt-1 text-sm text-slate-500">Status layanan</p></div><span class="rounded-lg bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-700">&#9679; Beroperasi</span></div><div class="space-y-3 py-6"><div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3"><span class="grid h-9 w-9 place-items-center rounded-lg bg-cyan-100 text-cyan-700" aria-hidden="true">&#10003;</span><div><p class="text-sm font-semibold">Single sign-on</p><p class="text-xs text-slate-500">Masuk lebih praktis ke aplikasi internal</p></div></div><div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3"><span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-100 text-blue-700" aria-hidden="true">&#9672;</span><div><p class="text-sm font-semibold">Perlindungan berbasis peran</p><p class="text-xs text-slate-500">Hak akses sesuai tanggung jawab Anda</p></div></div><div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3"><span class="grid h-9 w-9 place-items-center rounded-lg bg-violet-100 text-violet-700" aria-hidden="true">&#8599;</span><div><p class="text-sm font-semibold">Jejak aktivitas</p><p class="text-xs text-slate-500">Peristiwa keamanan tercatat dan terukur</p></div></div></div><div class="rounded-xl border border-cyan-100 bg-cyan-50 p-4"><p class="text-sm font-semibold text-cyan-950">Butuh bantuan masuk?</p><p class="mt-1 text-xs leading-5 text-cyan-800">Hubungi admin IT Basilindo untuk pemulihan akses.</p></div></div></section>
            </div>
        </main>
        <footer class="relative border-t border-slate-200 bg-white/50 px-6 py-7 lg:px-8"><div class="mx-auto flex max-w-6xl flex-col gap-2 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between"><span>&copy; {{ date('Y') }} Basilindo. Semua hak dilindungi.</span><span>Akses internal yang aman untuk bekerja lebih lancar.</span></div></footer>
    </div>
</body>
</html>
