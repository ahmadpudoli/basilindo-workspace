# 2026-10-22 — Lifecycle upload dokumen sederhana

## Keputusan

- Upload baru langsung menjadi `ready`, ditampilkan sebagai **Tersedia**, setelah file dan metadata berhasil divalidasi.
- Status `quarantine` dan `rejected` tetap dibaca sebagai kompatibilitas data lama dan tidak digunakan untuk upload baru.
- UI daftar dokumen dan bundling hanya menampilkan dokumen `ready`; status verifikasi lama disembunyikan dari filter dan label bisnis.
- Relasi pihak luar dokumen menggunakan `counterparty_company_id`; `vendor_id` dihapus setelah verifikasi kolom tersebut tidak memiliki data.
- Arsip dilakukan melalui soft delete.
- Halaman, menu, dan route verifikasi manual dikeluarkan dari alur aktif. Data verifikasi lama tidak dihapus.
- Metadata bertipe angka menggunakan input dan normalisasi format Indonesia sebelum validasi server-side.
- Formatting angka berjalan langsung saat pengguna mengetik, bukan hanya saat field kehilangan focus.
- Titik pada live formatter selalu diperlakukan sebagai pemisah ribuan; koma menjadi satu-satunya pemisah desimal agar pengetikan berurutan tidak berubah menjadi koma secara keliru.
- Formatter dipasang melalui event delegation sehingga field metadata yang dirender ulang oleh Livewire tetap hanya menerima angka.

## Verifikasi

- `php artisan view:cache`
- `php artisan test --filter=Document`
- `npm.cmd run build`
- `git diff --check`
