# 2026-10-21 — Immutability perusahaan terkait vendor

## Perubahan

- Mendeteksi relasi vendor melalui `vendor_company_id` maupun anchor `company_id`.
- Mengunci peran `vendor`, jenis entitas, klasifikasi, dan status pada form edit perusahaan terkait.
- Menambahkan validasi backend agar perubahan langsung melalui request juga ditolak.
- Nilai field terkunci tetap dikirim melalui hidden input saat form disubmit.

## Verifikasi

- `php artisan view:cache`
- `npm.cmd run build`
- `git diff --check`
