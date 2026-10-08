<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f4ff">
    <title>Basilindo Project Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] } } } }</script>
    <style>
        body { background: linear-gradient(135deg, #fbfaff 0%, #f4f1ff 48%, #fff8ef 100%); }
        .mesh { background: radial-gradient(circle at 20% 20%, rgb(129 140 248 / .28), transparent 34%), radial-gradient(circle at 82% 18%, rgb(251 191 36 / .22), transparent 28%), radial-gradient(circle at 75% 80%, rgb(192 132 252 / .2), transparent 32%); }
    </style>
</head>
<body class="font-sans text-slate-900 antialiased">
    <div class="relative min-h-screen overflow-hidden">
        <div class="mesh pointer-events-none absolute inset-0"></div>
        <div class="pointer-events-none absolute -right-24 top-40 h-72 w-72 rounded-full bg-amber-200/40 blur-3xl"></div>
        <nav class="relative mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
            <a href="/" class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-500 text-lg font-bold text-white shadow-lg shadow-indigo-300/40">B</span>
                <span><span class="block text-lg font-bold tracking-tight text-slate-950">Basilindo</span><span class="block text-[10px] font-bold uppercase tracking-[0.22em] text-indigo-600">Project Management</span></span>
            </a>
            <a href="/admin" class="rounded-full bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-slate-300/40 transition hover:-translate-y-0.5 hover:bg-indigo-700">Masuk ke aplikasi <span aria-hidden="true">↗</span></a>
        </nav>

        <main class="relative mx-auto max-w-7xl px-6 pb-20 pt-10 lg:px-8 lg:pt-20">
            <section class="grid items-center gap-14 lg:grid-cols-[.95fr_1.05fr]">
                <div>
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-white/75 px-3.5 py-2 text-xs font-bold text-indigo-700 shadow-sm"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Workspace tim yang lebih terarah</div>
                    <h1 class="max-w-2xl text-5xl font-black leading-[1.03] tracking-tight text-slate-950 sm:text-7xl">Bawa proyek dari <span class="bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-500 bg-clip-text text-transparent">rencana</span> menjadi hasil.</h1>
                    <p class="mt-7 max-w-xl text-lg leading-8 text-slate-600">Satu ruang kerja untuk mengatur prioritas, menghubungkan tim, dan melihat progres proyek tanpa harus berpindah-pindah alat.</p>
                    <div class="mt-9 flex flex-wrap gap-3"><a href="/admin" class="rounded-2xl bg-indigo-600 px-5 py-3.5 text-sm font-bold text-white shadow-xl shadow-indigo-300/50 transition hover:-translate-y-0.5 hover:bg-indigo-700">Mulai bekerja <span aria-hidden="true">→</span></a><a href="#fitur" class="rounded-2xl border border-slate-300 bg-white/70 px-5 py-3.5 text-sm font-semibold text-slate-700 transition hover:border-indigo-300 hover:bg-white">Jelajahi fitur</a></div>
                    <div class="mt-10 grid max-w-lg grid-cols-3 gap-4 border-t border-slate-300/70 pt-6"><div><p class="text-2xl font-black text-slate-950">12</p><p class="mt-1 text-xs font-medium text-slate-500">Proyek aktif</p></div><div><p class="text-2xl font-black text-slate-950">48</p><p class="mt-1 text-xs font-medium text-slate-500">Tugas berjalan</p></div><div><p class="text-2xl font-black text-slate-950">94%</p><p class="mt-1 text-xs font-medium text-slate-500">On-track</p></div></div>
                </div>

                <div class="relative lg:pl-8">
                    <div class="absolute -inset-5 rounded-[2.5rem] bg-gradient-to-br from-indigo-300/40 via-violet-200/30 to-amber-200/40 blur-2xl"></div>
                    <div class="relative rounded-[2.5rem] border border-white/80 bg-white/80 p-5 shadow-2xl shadow-indigo-200/50 backdrop-blur-xl sm:p-7">
                        <div class="flex items-center justify-between"><div><p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-600">Weekly pulse</p><p class="mt-1 text-sm text-slate-500">Ringkasan ritme kerja tim</p></div><span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">● On track</span></div>
                        <div class="mt-6 rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-600 p-5 text-white shadow-xl shadow-indigo-300/40"><div class="flex items-start justify-between"><div><p class="text-sm font-medium text-indigo-100">Project Atlas</p><p class="mt-2 text-3xl font-black">72%</p><p class="mt-1 text-xs text-indigo-100">Target rilis dalam 9 hari</p></div><span class="rounded-xl bg-white/15 px-3 py-2 text-xs font-bold">Sprint 08</span></div><div class="mt-5 h-2 rounded-full bg-white/20"><div class="h-2 w-[72%] rounded-full bg-amber-300"></div></div></div>
                        <div class="mt-5 grid gap-3 sm:grid-cols-2"><div class="rounded-2xl border border-slate-200 bg-white p-4"><div class="flex items-center justify-between"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Prioritas hari ini</p><span class="text-indigo-600">✦</span></div><p class="mt-3 text-2xl font-black text-slate-900">08</p><p class="mt-1 text-xs text-slate-500">Tugas perlu ditindaklanjuti</p></div><div class="rounded-2xl border border-slate-200 bg-white p-4"><div class="flex items-center justify-between"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kolaborasi</p><span class="text-violet-600">◌</span></div><p class="mt-3 text-2xl font-black text-slate-900">24</p><p class="mt-1 text-xs text-slate-500">Pembaruan minggu ini</p></div></div>
                        <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50/80 p-4"><p class="text-sm font-bold text-amber-900">Agenda berikutnya</p><p class="mt-1 text-sm text-amber-800">Review milestone bersama tim Finance · 14.00</p></div>
                    </div>
                </div>
            </section>

            <section id="fitur" class="mt-28"><div class="flex flex-col justify-between gap-4 border-b border-slate-300/70 pb-7 sm:flex-row sm:items-end"><div><p class="text-sm font-bold uppercase tracking-[0.2em] text-indigo-600">Cara kerja lebih sederhana</p><h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Semua tim bergerak dalam satu arah.</h2></div><p class="max-w-sm text-sm leading-6 text-slate-500">Dari backlog sampai laporan, setiap langkah terlihat dan dapat dipertanggungjawabkan.</p></div><div class="mt-8 grid gap-4 md:grid-cols-3"><div class="rounded-3xl border border-indigo-100 bg-white/75 p-6 shadow-lg shadow-indigo-100/50"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-indigo-100 text-xl text-indigo-700">✦</span><h3 class="mt-5 text-lg font-bold">Prioritas terlihat</h3><p class="mt-2 text-sm leading-6 text-slate-600">Susun pekerjaan berdasarkan dampak, urgensi, dan kapasitas tim.</p></div><div class="rounded-3xl border border-violet-100 bg-white/75 p-6 shadow-lg shadow-violet-100/50"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-violet-100 text-xl text-violet-700">◈</span><h3 class="mt-5 text-lg font-bold">Kolaborasi terhubung</h3><p class="mt-2 text-sm leading-6 text-slate-600">Komentar, assignment, dan update tersimpan pada konteks proyeknya.</p></div><div class="rounded-3xl border border-amber-100 bg-white/75 p-6 shadow-lg shadow-amber-100/50"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-amber-100 text-xl text-amber-700">↗</span><h3 class="mt-5 text-lg font-bold">Progress terukur</h3><p class="mt-2 text-sm leading-6 text-slate-600">Gunakan timeline dan ringkasan untuk mengambil keputusan lebih cepat.</p></div></div></section>
        </main>
        <footer class="relative border-t border-slate-300/70 px-6 py-7 lg:px-8"><div class="mx-auto flex max-w-7xl flex-col gap-2 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between"><span>© {{ date('Y') }} Basilindo. Semua hak dilindungi.</span><span>Project management untuk kolaborasi yang lebih fokus.</span></div></footer>
    </div>
</body>
</html>
