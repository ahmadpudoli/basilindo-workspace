# 2026-10-21 — Proteksi penghapusan tipe dokumen

## Perubahan

- Menolak penghapusan tipe dokumen yang sudah dipakai oleh satu atau lebih dokumen.
- Mengubah respons penolakan menjadi redirect dengan pesan error yang dapat dibaca pengguna.
- Menonaktifkan tombol `Hapus` pada daftar untuk tipe yang sudah digunakan, dengan keterangan melalui tooltip.
- Menambahkan test yang memastikan tipe dan dokumen tetap ada setelah percobaan penghapusan.

## Verifikasi

- `php artisan view:cache`
- `php artisan test --filter=DocumentTypeFormViewTest`
- `npm.cmd run build`
- `git diff --check`
