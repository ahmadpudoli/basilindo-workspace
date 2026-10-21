# Implementasi baseline UI/UX Workspace

## Perubahan

- Mengganti shell `resources/views/layouts/app.blade.php` menjadi layout Workspace dengan topbar, brand, account menu, module context, responsive sidebar/offcanvas, flash/error state, dan Livewire hooks.
- Menambahkan icon partial untuk modul Company, CRM, Project Management, dan Document Management.
- Menambahkan design system Bootstrap pada `resources/css/app.css`: warna template, typography, card, table, form, button, badge, dropdown, modal, pagination, responsive layout, serta light/dark theme.
- Menambahkan theme toggle berbasis local storage pada `resources/js/app.js`.
- Mengatur paginator Laravel menggunakan Bootstrap 5.
- Mengonversi view `external-login` dari utility class Tailwind ke Bootstrap.
- Menyesuaikan dashboard Company, CRM, Project Management, dan Document Management dengan page header, action button, summary, status badge, dan table pattern.

## Verifikasi

- `npm.cmd run build` berhasil.
- `php artisan view:cache` berhasil.
- `php artisan test` berhasil: 35 test, 91 assertion.

## Risiko tersisa

- Halaman resource yang masih memakai markup satu baris perlu refactor bertahap bila membutuhkan filter/modal dinamis per record; visual dasarnya sudah ditangani oleh shared shell dan design system.
- Interaksi Bootstrap modal di dalam Livewire perlu regression test ketika komponen baru ditambahkan.
- Perubahan visual harus diuji pada browser desktop/mobile dan dibandingkan dengan template pada setiap halaman baru.
