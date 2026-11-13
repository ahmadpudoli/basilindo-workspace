# 2026-11-12 — Aksi detail pada daftar klaim

## Perubahan

- Menampilkan tombol Detail pada setiap baris klaim yang boleh dilihat pengguna menurut ClaimPolicy.
- Mempertahankan aksi pembayaran dan aksi edit/hapus draft berdampingan dengan Detail.
- Menghapus injeksi tombol Detail draft dari JavaScript agar tombol tidak ganda.
- Memperbarui requirement dan checklist.

## Verifikasi

- Kompilasi view Blade dan `git diff --check`.

## Risiko tersisa

- Akses halaman detail tetap diperiksa ulang melalui policy pada controller.
