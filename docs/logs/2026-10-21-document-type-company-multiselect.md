# 2026-10-21 — Multi-perusahaan pada tipe dokumen

## Perubahan

- Menambahkan migration pivot `document_type_companies` dan backfill relasi dari `document_types.company_id`.
- Menambahkan penyimpanan relasi many-to-many pada pivot `document_type_companies` dengan pembacaan ID pivot melalui koneksi dokumen dan nama perusahaan melalui Core.
- Mengubah form tambah/edit tipe dokumen menjadi searchable multi-select perusahaan.
- Menyesuaikan daftar tipe dokumen agar menampilkan seluruh perusahaan terkait.
- Memperbarui validasi dokumen agar memakai relasi multi-perusahaan.
- Menambahkan style dropdown compact dengan line-height dan ellipsis agar teks tidak terpotong.

## Verifikasi

- `php artisan view:cache`
- `php artisan test --filter=DocumentTypeFormViewTest`
- `npm.cmd run build`
- `git diff --check`

## Risiko tersisa

- Kolom `document_types.company_id` dipertahankan untuk kompatibilitas data lama; relasi baru menjadi sumber utama setelah migration dijalankan.
