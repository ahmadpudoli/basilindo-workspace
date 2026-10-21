# Modern Workspace modals

## Perubahan

- Menambahkan visual modal terpusat dengan gradient biru, backdrop biru transparan, rounded corner, shadow, dan animasi masuk.
- Menambahkan ikon konteks serta kicker pada modal preview dan konfirmasi.
- Menata ulang tombol, area isi, close button, dan footer agar lebih modern serta konsisten.
- Berlaku untuk seluruh modal Bootstrap yang dirender melalui layout Workspace.
- Mencerahkan gradient header modal dan membuat backdrop lebih transparan dengan efek blur/saturate agar halaman di belakang tetap terlihat.

## Verifikasi

- `npm.cmd run build` berhasil.
- `php artisan view:cache` berhasil.
- `git diff --check` berhasil.
- Build frontend terbaru berhasil setelah penyesuaian backdrop blur dan warna modal.
