# 2026-10-21 — Panel filter Company dan Vendor

## Perubahan

- Menyamakan panel filter daftar Company dan Vendor dengan pola panel filter Tipe Dokumen.
- Menggunakan card filter dengan susunan Bootstrap grid yang compact.
- Mengubah label tombol filter menjadi `Terapkan`.
- Mempertahankan filter pencarian, status, jumlah per halaman, jenis, klasifikasi, peran, dan perusahaan.

## Verifikasi

- `php artisan view:cache`
- `git diff --check`
