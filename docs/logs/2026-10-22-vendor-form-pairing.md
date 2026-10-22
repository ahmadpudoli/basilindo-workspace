# 2026-10-22 — Pasangan field form Vendor

## Perubahan

- Field `Digunakan oleh perusahaan operasional` di form tambah dan edit Vendor diubah menjadi kolom `col-md-6`.
- Field tersebut sekarang berpasangan dengan field `Status` pada layar desktop dan tetap responsif pada layar kecil.

## Verifikasi

- `php artisan view:cache`
- `git diff --check`
