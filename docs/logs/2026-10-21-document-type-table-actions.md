# 2026-10-21 — Tombol aksi tabel tipe dokumen

## Perubahan

- Menambahkan class `workspace-table-actions` pada kolom aksi daftar tipe dokumen agar rule compact berlaku pada tombol `Edit` dan `Hapus`.
- Tombol tabel sekarang menggunakan tinggi dan padding compact yang konsisten dengan standar `btn-sm` Workspace.

## Verifikasi

- `php artisan view:cache`
- `npm.cmd run build`
- `git diff --check`
