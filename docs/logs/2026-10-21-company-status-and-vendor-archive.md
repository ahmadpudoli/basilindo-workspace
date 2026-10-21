# 2026-10-21 — Status perusahaan dan arsip master vendor

## Perubahan

- Status perusahaan tetap dapat diubah ketika perusahaan sudah menjadi master vendor.
- Jenis entitas, klasifikasi, dan peran Vendor tetap dilindungi selama relasi vendor masih ada.
- Menambahkan tombol `Arsipkan` pada setiap baris daftar perusahaan, termasuk perusahaan yang memiliki peran Vendor.
- Pengarsipan menggunakan soft delete melalui policy dan confirmation dialog.

## Verifikasi

- `php artisan view:cache`
- `php artisan test --filter=Company`
- `npm.cmd run build`
- `git diff --check`
