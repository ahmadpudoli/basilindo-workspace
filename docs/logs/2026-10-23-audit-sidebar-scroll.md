# 2026-10-23 — Audit sidebar scroll

## Perubahan

- Menetapkan tinggi maksimum sidebar audit subject berdasarkan viewport: maksimal 720px desktop dan 620px pada layar lebih kecil.
- Menambahkan vertical scroll internal pada isi history audit agar sidebar tidak memanjang melebihi area layar.
- Header audit tetap terlihat saat daftar event digulir.
- Menambahkan aturan reusable pada requirement audit trail untuk sidebar audit berikutnya.

## Verifikasi

- CSS diterapkan pada sidebar audit reusable serta sidebar edit/detail dokumen.
- Build frontend dijalankan setelah perubahan.

## Risiko tersisa

- Tinggi maksimum dapat disesuaikan jika perangkat target Workspace memiliki viewport khusus yang berbeda.
