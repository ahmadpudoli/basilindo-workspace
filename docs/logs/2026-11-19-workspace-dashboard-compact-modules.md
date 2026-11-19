# Kartu launcher Workspace compact

Tanggal: 2026-11-19

## Perubahan

- Launcher `/dashboard` menggunakan enam kolom pada desktop lebar, empat kolom pada desktop menengah, tiga kolom pada tablet, dan dua kolom pada mobile.
- Kartu, ikon, jarak, dan teks diperkecil; deskripsi dibatasi dua baris agar tinggi kartu konsisten.

## Verifikasi

- `php artisan view:cache` berhasil.
- `git diff --check` tidak menemukan whitespace error; hanya terdapat peringatan konversi akhir baris pada file lain yang sudah berubah.
