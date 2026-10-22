# Modernisasi dashboard Document Management

## Perubahan

- Menambahkan hero area dengan CTA upload dan daftar dokumen.
- Menambahkan card statistik dengan ikon, aksen warna, hover motion, dan animasi masuk bertahap.
- Memperbarui daftar dokumen terbaru menjadi card dengan tabel compact, status badge, dan empty state.
- Menambahkan dukungan responsive layout serta `prefers-reduced-motion` agar animasi tetap nyaman digunakan.
- Menetapkan hero sebagai banner compact setinggi 120px, dengan judul dan deskripsi satu baris pada desktop.

## Verifikasi

- `php artisan view:cache`
- `npm.cmd run build`
- `git diff --check`

## Risiko tersisa

- Animasi dan dekorasi menggunakan CSS; tidak ada dependensi runtime baru.
