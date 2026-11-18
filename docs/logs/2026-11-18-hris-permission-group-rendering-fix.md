# Perbaikan tampilan grup permission HRIS

Tanggal: 2026-11-18

## Perubahan

- Mengembalikan `Approval & ringkasan` ke struktur daftar datar yang didukung komponen permission.
- Menambahkan empat permission Cetak ke grup yang sama tanpa menyembunyikan permission summary sebelumnya.
- Grant role yang tersimpan tidak dihapus oleh migration; masalah sebelumnya hanya pada rendering grup.

## Verifikasi

- `php artisan view:cache`.
- `git diff --check`.
