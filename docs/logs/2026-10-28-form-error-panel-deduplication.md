# 2026-10-28 — Menghapus panel error form duplikat

## Perubahan

- Menghapus panel error global dari layout aplikasi yang sebelumnya menampilkan `errors->first()` pada semua halaman.
- Menghapus panel error duplikat pada form create/revisi cuti aktif.
- Menampilkan error server tanggal satu kali di section tanggal pengajuan.

## Verifikasi

- Error quota tahun yang belum dikonfigurasi kini diarahkan ke area tanggal, bukan panel global tambahan.
- Kompilasi Blade akan dijalankan setelah perubahan.

## Risiko tersisa

- Halaman form lama yang tidak aktif tetap memiliki markup error lokal, tetapi tidak lagi mendapat panel global dari layout.
