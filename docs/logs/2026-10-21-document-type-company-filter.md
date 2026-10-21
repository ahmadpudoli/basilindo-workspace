# 2026-10-21 — Filter perusahaan pada daftar tipe dokumen

## Perubahan

- Menghapus filter `Urutan` dari form daftar tipe dokumen.
- Menggantinya dengan searchable multi-select `Perusahaan`.
- Memfilter tipe dokumen berdasarkan perusahaan lama pada `company_id` atau relasi baru pada pivot `document_type_companies`.
- Menjaga filter perusahaan tetap aktif saat sorting dan pagination menggunakan query string.
- Sorting tetap tersedia melalui judul kolom tabel.

## Verifikasi

- `php artisan view:cache`
- `php artisan test --filter=DocumentTypeFormViewTest`
- `npm.cmd run build`
- `git diff --check`
