# Login UI Redesign

## Perubahan

- Mendesain ulang halaman login menjadi layout dua sisi pada desktop/tablet.
- Menambahkan panel branding Basilindo, gradient biru, decorative motion, dan animasi masuk.
- Menyeragamkan label branding login menjadi “Basilindo Workspace”.
- Menyesuaikan pesan global loading agar membedakan proses tambah, update, hapus, arsip, dan proses umum.
- Menyesuaikan grid dashboard agar menampilkan lima card modul per baris pada layar besar.
- Menambahkan toggle tampil/sembunyikan kata sandi.
- Menambahkan layout responsif satu kolom untuk layar kecil.
- Memperluas pesan login agar mencerminkan platform multi-modul: Document Management, HRIS, CRM, dan Project Management.
- Menghaluskan pembagian dua sisi dengan panel login overlap, sudut melengkung, gradient, dan bayangan lembut tanpa garis pemisah solid.

## Verifikasi

- `php artisan view:cache` berhasil.
- `npm.cmd run build` berhasil.
- `git diff --check` berhasil.
