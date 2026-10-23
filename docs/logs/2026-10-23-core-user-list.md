# Daftar pengguna dengan filter dan pagination

## Perubahan

- Menambahkan filter pencarian nama/email, role, dan status akun.
- Filter role menggunakan multi-select dropdown dan mendukung pencarian beberapa role sekaligus.
- Menambahkan sorting server-side untuk kolom nama, email, dan status dengan indikator arah pada header.
- Menambahkan pagination dengan pilihan 10, 25, 50, dan 100 serta default 10.
- Menambahkan ringkasan jumlah baris dan mempertahankan query filter pada pagination.
- Menyaring tombol tambah, edit, dan arsip berdasarkan policy; akun sistem tetap tidak dapat diedit atau diarsipkan.

## Verifikasi

- `php artisan view:cache` berhasil.
- `php artisan test tests/Feature/RoleListTest.php` — 4 test lulus, 15 assertion.
