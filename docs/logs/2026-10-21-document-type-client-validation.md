# 2026-10-21 — Validasi client-side tipe dokumen

## Perubahan

- Mengaktifkan hook validasi client terpusat pada form tambah dan edit tipe dokumen.
- Menambahkan pemeriksaan nama, kode, panjang field, nama metadata kosong, format nama metadata, duplikasi nama metadata, dan tipe metadata yang tidak dikenal.
- Menambahkan panduan validasi form CRUD agar implementasi berikutnya tetap memakai validasi client dan server secara berlapis.

## Verifikasi

- `php artisan view:cache`
- `php artisan test --filter=DocumentTypeFormViewTest`
- `npm.cmd run build`
- `git diff --check`

## Risiko tersisa

- Validasi client bergantung pada JavaScript yang berhasil dimuat; server tetap menjadi sumber kebenaran untuk validasi, authorization, scope, dan pemeriksaan database.
