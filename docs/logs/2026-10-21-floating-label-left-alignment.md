# 2026-10-21 — Penyelarasan awal label floating

## Perubahan

- Menyetarakan posisi awal label floating global dengan posisi awal value input menggunakan offset `0.8rem`.
- Berlaku untuk input, select, dropdown multiselect, dan field floating baru yang memakai class global.
- Menyamakan titik acuan label pada wrapper otomatis JavaScript dan wrapper dropdown manual dengan `display: contents`.

## Verifikasi

- `php artisan view:cache`
- `npm.cmd run build`
- `git diff --check`
