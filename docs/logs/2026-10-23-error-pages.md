# Halaman error HTTP

## Perubahan

- Menambahkan template error terpadu untuk HTTP 400, 401, 403, 404, 405, 408, 419, 422, 429, 500, 502, 503, dan 504.
- Menggunakan ilustrasi CSS ringan, animasi float/pulse, tombol navigasi, dan dukungan reduced motion.
- Aset referensi lokal ditinjau; karena tidak ada ilustrasi error yang sesuai, tiap kode memakai SVG inline spesifik (kunci, pencarian, jam, checklist, antrean, alat, koneksi, dan peringatan) tanpa request aset tambahan.
- Tidak menambahkan aset gambar atau dependency eksternal.

## Verifikasi

- `php artisan view:cache` berhasil.
