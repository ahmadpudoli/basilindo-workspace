# Dashboard module cards

## Perubahan

- Mengubah card modul pada dashboard menjadi launcher card yang lebih informatif dengan ikon, deskripsi, aksen warna per modul, dan CTA.
- Menambahkan hover/focus animation berupa lift, glow, accent bar, icon emphasis, dan arrow transition.
- Menambahkan dukungan dark theme serta `prefers-reduced-motion` untuk aksesibilitas.

## Verifikasi

- `npm.cmd run build` berhasil.
- `php artisan view:cache` berhasil.
- `git diff --check` berhasil.
