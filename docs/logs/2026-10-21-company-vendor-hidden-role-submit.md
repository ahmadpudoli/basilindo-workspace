# 2026-10-21 — Pengiriman role Vendor yang dikunci

## Perubahan

- Memperbaiki JavaScript form perusahaan agar hanya checkbox role di dropdown yang diberi status disabled.
- Hidden input `roles[]=vendor` sebagai fallback saat role Vendor dikunci tetap aktif dan ikut dikirim ke server.
- Update perusahaan yang sudah menjadi master vendor sekarang dapat dilanjutkan tanpa error role Vendor terhapus.

## Verifikasi

- `php artisan view:cache`
- `php artisan test --filter=Company`
- `npm.cmd run build`
- `git diff --check`
