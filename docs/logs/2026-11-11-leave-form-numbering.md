# Log 2026-11-11 — Layout dan Penomoran Pengajuan Cuti/Sakit

## Perubahan

- Menyesuaikan bagian informasi nomor pengajuan menggunakan pola floating field seperti form lembur.
- Memperjelas bagian jenis pengajuan dengan pilihan kartu Cuti dan Sakit.
- Memperkaya kartu jenis pengajuan dengan ikon, state hover/fokus, indikator pilihan, dan warna pembeda untuk Cuti/Sakit.
- Mengubah preview nomor pengajuan agar membaca master `hris_leave` berdasarkan perusahaan karyawan.
- Mengalokasikan nomor final menggunakan `NumberingService::next()` saat pengajuan disimpan.
- Menghapus validasi regex nomor lama yang hanya menerima nomor acak legacy.

## Verifikasi

- PHP lint untuk controller, service, dan form request terkait.
- `php artisan view:cache`.

## Risiko tersisa

- Preview nomor dapat berubah jika ada pengajuan lain yang dikirim bersamaan; nomor final tetap dijamin oleh counter master saat transaksi penyimpanan.
