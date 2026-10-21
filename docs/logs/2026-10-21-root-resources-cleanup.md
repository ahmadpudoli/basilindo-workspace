# Pembersihan root `resources/`

## Perubahan

- Menghapus tiga view legacy dari root `resources/`.
- Menetapkan `app/workspace/resources/` sebagai resource runtime Basilindo Workspace.
- Menambahkan requirement dan acceptance criteria untuk pembersihan folder legacy.
- Memperbarui feature checklist.

## Verifikasi

- `git grep` tidak menemukan referensi runtime ke root `resources/`.
- Konfigurasi Vite dan view aktif berada di `app/workspace/`.
- Working tree diperiksa setelah perubahan.

## Perbaikan bootstrap cache view

- Menambahkan konfigurasi config/view.php yang diperlukan Laravel.
- Menambahkan placeholder storage/framework agar path cache Blade valid setelah clone atau instalasi Composer.
- Menambahkan fallback path valid ketika cache view belum dibuat.

## Risiko tersisa

- Tidak ada risiko runtime yang teridentifikasi; file yang dihapus merupakan view legacy yang tidak direferensikan.
