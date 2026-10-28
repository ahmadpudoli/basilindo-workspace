# 2026-10-28 — Batas maksimum lembur opsional

## Perubahan

- Menghapus fallback maksimum lembur 480 menit.
- Jika `hris.overtime_maximum_minutes` belum dikonfigurasi, durasi lembur tidak dibatasi oleh parameter maksimum.
- Jika parameter diisi, nilai maksimum tetap diberlakukan setelah pembulatan durasi.
- Deskripsi parameter Pengaturan HRIS diperjelas sebagai opsional.

## Verifikasi

- Validasi durasi nol tetap aktif.
- Validasi server tetap menolak durasi yang melewati maksimum jika parameter tersedia.
