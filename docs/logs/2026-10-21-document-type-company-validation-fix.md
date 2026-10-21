# 2026-10-21 — Perbaikan validasi perusahaan tipe dokumen

## Perubahan

- Rule validasi perusahaan diarahkan ke koneksi `core` dengan format `exists:core.companies,id`.
- Submit multi-perusahaan menyimpan relasi pada pivot `document_type_companies`.
- Pilihan perusahaan kosong tetap menghasilkan tipe dokumen global.
- Ditambahkan feature test untuk submit multi-perusahaan dan global.

## Verifikasi

- `php artisan test --filter=DocumentTypeFormViewTest`
- `php artisan view:cache`
- `git diff --check`
