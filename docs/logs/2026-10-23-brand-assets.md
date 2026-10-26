# Brand assets Basilindo Workspace

## Perubahan

- Menambahkan `public/images/logo-basilindo.png` dari logo yang diberikan.
- Menambahkan `public/images/logo-favicon.png` sebagai sumber favicon terbaru.
- Membuat ulang `public/favicon.ico` multi-resolusi 16/32/48px menggunakan file favicon terbaru dan PHP GD.
- Menggunakan logo pada header aplikasi, login, landing page, dan komponen login terkait.
- Menambahkan favicon dan apple touch icon pada layout utama.

## Verifikasi

- `php artisan view:cache` berhasil.
- Favicon terdeteksi sebagai `image/vnd.microsoft.icon`.
