# Workspace preview dan confirmation dialogs

## Perubahan

- Menambahkan modal preview global untuk PDF dan gambar pada daftar dokumen.
- Preview menggunakan endpoint terotorisasi yang sudah tersedia, tanpa menyalin file ke public disk.
- Menambahkan modal konfirmasi global untuk form destruktif dan aksi yang memakai `data-confirm`.
- Form dengan method override `DELETE` otomatis diberi pesan konfirmasi, termasuk form legacy yang masih memiliki inline `onsubmit`.

## Verifikasi

- `npm.cmd run build` berhasil.
- `php artisan view:cache` berhasil.
- `git diff --check` berhasil.

## Catatan

- Preview membutuhkan browser yang mendukung PDF iframe; tombol fallback tetap tersedia untuk membuka dokumen di tab baru.
- Memperbaiki import Bootstrap JS menjadi namespace module agar `Modal.getOrCreateInstance()` benar-benar tersedia pada runtime.
