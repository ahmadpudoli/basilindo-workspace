# Log 2026-11-09 — Dashboard dan riwayat integrasi

## Perubahan

- Dashboard statistik integrasi diubah menjadi empat kartu per baris pada layar besar.
- Riwayat sinkronisasi menambahkan pencarian pesan/sumber, filter jenis dan status, pilihan 10/25/50/100 baris, pagination, dan sorting server-side.
- State filter, sorting, dan jumlah baris dipertahankan melalui query string.
- Tampilan riwayat dan kartu statistik diberi styling responsif.

## Verifikasi

- Blade view berhasil dicache.
- Controller berhasil melewati pemeriksaan sintaks PHP.
- Vite production build berhasil.
