# 2026-10-21 — Perbaikan overflow dropdown vendor

## Perubahan

- Form yang memiliki searchable dropdown tidak lagi memotong menu pada batas card.
- Daftar pilihan tetap memakai area scroll internal ketika item melebihi tinggi menu.
- Perbaikan berlaku global untuk dropdown Workspace, termasuk form vendor.

## Verifikasi

- `npm.cmd run build`
- `php artisan view:cache`
- `git diff --check`
