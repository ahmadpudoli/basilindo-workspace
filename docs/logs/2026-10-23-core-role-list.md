# 2026-10-23 — Core role list

## Perubahan

- Menambahkan filter nama role, guard, dan jumlah baris per halaman pada `workspace/core/roles`.
- Menyempurnakan tabel menjadi responsif dengan ringkasan pagination dan empty state berbasis filter.
- Menetapkan aturan tombol: Edit untuk perubahan role/permission, Hapus dengan konfirmasi untuk role non-sistem, dan Hapus disabled untuk `super_admin`.
- Menambahkan acceptance criteria pada `docs/requirements/core-role-list.md` dan checklist fitur.

## Verifikasi

- Menambahkan feature test untuk filter server-side, query string jumlah baris, dan perlindungan tombol hapus role sistem.
- `php artisan test tests/Feature/RoleListTest.php` — lulus, 2 test dan 8 assertion.
- `php -l` pada controller dan test — lulus.
- `npm.cmd run build` — lulus.
- `git diff --check` — lulus; perubahan lain yang sudah ada di working tree tidak disentuh.

## Risiko tersisa

- Daftar guard saat ini hanya `web`, sesuai konfigurasi authentication Workspace. Jika guard baru ditambahkan, opsi filter perlu diperluas.
