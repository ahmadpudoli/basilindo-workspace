# 2026-10-21 — Perbaikan alignment dropdown perusahaan

## Perubahan

- Memusatkan teks terpilih secara vertikal pada dropdown perusahaan.
- Menghapus padding vertikal yang menyebabkan bagian bawah teks terpotong.
- Mempertahankan tinggi trigger dropdown pada 40px.

## Verifikasi

- `npm.cmd run build`
- `php artisan view:cache`
- `git diff --check`
