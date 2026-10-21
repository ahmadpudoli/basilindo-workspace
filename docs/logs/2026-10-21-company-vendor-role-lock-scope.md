# 2026-10-21 — Scope penguncian role Vendor perusahaan

## Perubahan

- Mengubah pengecekan relasi vendor agar hanya membaca `vendors.vendor_company_id` sebagai master perusahaan client/vendor.
- Tidak lagi mengunci klasifikasi, jenis entitas, status, dan role pada perusahaan operasional yang hanya tersimpan sebagai `vendors.company_id` legacy anchor.
- Memastikan checkbox role Vendor tampil terpilih dan tetap terkunci ketika perusahaan memang menjadi master vendor.

## Verifikasi

- `php artisan view:cache`
- `php artisan test --filter=Company`
- `npm.cmd run build`
- `git diff --check`
