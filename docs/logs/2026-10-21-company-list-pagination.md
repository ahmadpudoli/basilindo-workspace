# Company list pagination dan compact table

Tanggal: 2026-10-21

## Perubahan

- Menambahkan filter jenis entitas, klasifikasi, dan status pada query daftar perusahaan.
- Menambahkan pilihan limit 10, 15, 25, atau 50 record per halaman.
- Mempertahankan search, filter, sorting, dan pagination dalam satu query server-side serta request AJAX tanpa reload penuh.
- Menetapkan template Bootstrap pagination Workspace tanpa ringkasan ganda.
- Menyelaraskan ringkasan record dengan kontrol pagination pada footer.
- Mengecilkan tombol aksi tabel dan padding baris agar daftar lebih compact.

## Verifikasi

- Blade cache berhasil.
- Frontend build berhasil.
- PHP lint controller berhasil.
- `git diff --check` berhasil.
