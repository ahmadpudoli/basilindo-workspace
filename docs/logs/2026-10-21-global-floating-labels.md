# 2026-10-21 — Floating label global dan alignment dropdown

## Perubahan

- Menambahkan floating label global untuk field form Workspace yang memiliki label langsung di atas input, select native, atau searchable dropdown, termasuk select yang disabled pada form edit.
- Label berubah warna dan bergerak halus saat field mendapat fokus.
- Memusatkan line-height value dropdown agar teks terlihat penuh dalam tinggi tetap 40px.

## Verifikasi

- `npm.cmd run build`
- `php artisan view:cache`
- `git diff --check`
