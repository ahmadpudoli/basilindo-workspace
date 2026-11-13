# 2026-11-12 — Panel detail approval klaim dan preview bukti

## Perubahan

- Memperbarui panel detail approval claim dengan kartu konteks beraksen, kartu keperluan, ringkasan jenis/cakupan, referensi project dan PO, kategori, serta total pengajuan.
- Menata rincian biaya dan mengganti tautan unduh bukti nota dengan tombol preview.
- Menambahkan modal “Preview Bukti Dokumen” dengan rincian item di kiri dan viewer dokumen di kanan; preview tetap melalui endpoint privat yang sudah memeriksa otorisasi.
- Memuat relasi project untuk menampilkan nama referensi saat snapshot tidak tersedia.
- Menambahkan acceptance criteria dan memperbarui checklist.

## Verifikasi

- Belum menjalankan build atau test. Perubahan perlu ditinjau melalui halaman approval pada browser, termasuk tampilan responsif dan preview bukti.

## Risiko tersisa

- Rendering PDF/gambar di iframe mengikuti dukungan browser dan MIME type object storage.
