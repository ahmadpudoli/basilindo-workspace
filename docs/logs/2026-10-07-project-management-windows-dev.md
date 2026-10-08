# Log pekerjaan — Windows development Project Management

## Perubahan

- Memisahkan Laravel Pail dari `composer run dev` pada Project Management karena ekstensi `pcntl` tidak tersedia pada PHP Windows.
- Menambahkan command opsional `composer run dev:logs` untuk environment yang mendukung Pail.
- Mengurangi proses `concurrently` agar server, queue, dan Vite tetap berjalan ketika Pail gagal.

## Verifikasi

- Struktur `composer.json` diperbarui dengan script `dev` dan `dev:logs` yang terpisah.
- Port Vite dinamis (`5173`, `5174`, lalu `5175`) merupakan fallback normal ketika proses Vite lain masih aktif.
