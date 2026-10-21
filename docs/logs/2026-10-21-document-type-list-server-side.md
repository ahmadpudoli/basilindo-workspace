# 2026-10-21 — Daftar tipe dokumen server-side

## Perubahan

- Menambahkan pencarian nama/kode dan filter status pada daftar tipe dokumen.
- Menambahkan sorting server-side untuk nama, kode, status, dan waktu pembuatan dengan kolom yang di-whitelist.
- Menambahkan pilihan jumlah baris 10, 15, 25, atau 50 per halaman.
- Mempertahankan seluruh parameter pencarian, filter, sorting, dan limit pada link pagination.
- Menggunakan `table-sm` dan tombol aksi `btn-sm` agar tabel lebih compact.
- Memperjelas aturan standar pagination dan daftar CRUD server-side di `workspace-ui-pagination.md`.

## Verifikasi

- `php artisan view:cache`
- `php artisan test --filter=DocumentTypeFormViewTest`
- `npm.cmd run build`
- `git diff --check`

## Risiko tersisa

- Tabel saat ini memakai reload GET server-side; pola AJAX partial tetap dapat ditambahkan pada daftar yang membutuhkan interaksi tanpa reload penuh.
