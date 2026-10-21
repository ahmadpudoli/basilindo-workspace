# ADR-012 - `pos-laravel-admin` sebagai referensi UI/UX

- **Status:** Accepted - 2026-10-20
- **Keputusan:** `pos-laravel-admin/` digunakan sebagai template/acuan visual dan pola UI/UX untuk Basilindo Workspace. Implementasi runtime tetap berada di `workspace/` dengan domain, route, permission, data, storage, dan konfigurasi Basilindo sendiri.
- **Implementasi UI:** Blade + Bootstrap menjadi fondasi. Livewire digunakan selektif untuk tabel, filter, pagination, atau interaksi dinamis yang bernilai; komponen Blade biasa tetap menjadi pilihan default bila sudah memadai.
- **Alasan:** template menyediakan pola yang konsisten untuk layout, sidebar, header, card, table, filter, pagination, modal, status badge, responsive behavior, dan theme tanpa memindahkan domain POS yang tidak relevan.
- **Alternatif ditolak:** menyalin template menjadi runtime kedua; menyalin model, migration, payment, atau configuration POS; atau menjadikan seluruh halaman Livewire tanpa kebutuhan interaksi.
- **Konsekuensi:** setiap konversi UI harus memetakan ulang label ke Bahasa Indonesia, authorization ke policy/company scope Workspace, dan business action ke service/job Workspace. Perubahan template perlu diadaptasi secara selektif dan diuji ulang.
