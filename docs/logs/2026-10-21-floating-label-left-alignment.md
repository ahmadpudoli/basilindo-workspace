# 2026-10-21 — Penyelarasan awal label floating

## Perubahan

- Menggeser label floating global ke `left: 1rem` dengan `padding-left: 0`, sehingga border hanya tertutup tepat di belakang teks label.
- Menetapkan posisi global dengan `left: 0`, `margin-left: .75rem`, dan `padding-left: 0` agar awal label sama dengan awal value input.
- Menambahkan translasi horizontal `.625rem` pada label dan mempertahankannya saat field focus.
- Menyamakan helper global agar input, select, textarea, dan dropdown custom memakai wrapper `.workspace-floating-field` yang sama.
- Berlaku untuk input, select, dropdown multiselect, dan field floating baru yang memakai class global.
- Menyamakan titik acuan label pada wrapper otomatis JavaScript dan wrapper dropdown manual dengan `display: contents`.

## Verifikasi

- `php artisan view:cache`
- `npm.cmd run build`
- `git diff --check`
