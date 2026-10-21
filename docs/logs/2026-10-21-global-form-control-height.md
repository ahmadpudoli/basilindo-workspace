# 2026-10-21 — Standar tinggi input global Workspace

## Perubahan

- Menyamakan tinggi input satu baris, select, file input, dan custom dropdown menjadi 40px, mengikuti tinggi dropdown sebelumnya.
- Menerapkan aturan pada stylesheet global Workspace agar berlaku lintas modul.
- Textarea tetap menggunakan tinggi sesuai kebutuhan konten multi-baris.

## Verifikasi

- `npm.cmd run build`
- `php artisan view:cache`
- `git diff --check`
