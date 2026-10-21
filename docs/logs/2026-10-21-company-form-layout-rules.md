# 2026-10-21 — Layout dan aturan form perusahaan

## Perubahan

- Menata ulang pasangan field pada form tambah/edit perusahaan.
- Menonaktifkan pilihan peran bisnis sampai jenis entitas dan klasifikasi terisi.
- Memfilter parent aktif berdasarkan klasifikasi internal/external yang dipilih.
- Menambahkan validasi backend untuk parent dengan klasifikasi yang sama dan peran yang bergantung pada field pendahulu.

## Verifikasi

- `php artisan view:cache`
- `npm.cmd run build`
- `git diff --check`
