# 2026-10-21 — Logo brand pada tabel perusahaan

## Perubahan

- Menampilkan logo brand tepat di sebelah nama perusahaan pada daftar perusahaan.
- Logo dibaca melalui route privat yang sudah melewati policy perusahaan, bukan URL object storage langsung.
- Menambahkan `loading="lazy"`, `decoding="async"`, `fetchpriority="low"`, serta dimensi tetap agar gambar dimuat saat diperlukan dan tidak menyebabkan layout shift.
- Perusahaan tanpa logo menampilkan inisial kode sebagai placeholder.

## Verifikasi

- `php artisan view:cache`
- `php artisan test --filter=Company`
- `npm.cmd run build`
- `git diff --check`
