# Feedback autentikasi login

## Perubahan

- Mengubah error kredensial invalid menjadi error umum `authentication` dengan pesan `Email atau kata sandi tidak ditemukan.`.
- Menampilkan feedback inline bergaya card kecil di bawah heading form login.
- Menonaktifkan alert validasi global pada route login agar tidak muncul di bagian atas halaman.

## Verifikasi

- `php artisan view:cache` berhasil.
- `php artisan test --filter=Login` berhasil.
- Test khusus feedback login mencakup session error dan render inline.
