# 2026-10-21 — Permission role collapsible

## Perubahan

- Mengubah daftar Permission role pada form tambah dan edit role menjadi panel collapsible per modul.
- Panel default tertutup untuk mengurangi tinggi halaman.
- Mempertahankan checkbox `Pilih semua` dan sinkronisasi permission yang sudah tersedia.

## Verifikasi

- `php artisan view:cache`
- `npm.cmd run build`
- `git diff --check`
