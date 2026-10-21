# Sidebar navigation icons

## Perubahan

- Mengganti bullet sidebar dengan ikon SVG berdasarkan fungsi menu untuk seluruh modul.
- Menambahkan state aktif, hover animation, keyboard focus, dan dukungan sidebar collapsed.
- Menambahkan partial ikon navigasi terpusat agar konsisten dan mudah dikembangkan.

## Verifikasi

- `npm.cmd run build` berhasil.
- `php artisan view:cache` berhasil.
- `git diff --check` berhasil.
