# Detail tanggal pengajuan HRIS

## Perubahan

- Memperbaiki error submit karena kolom `start_date` dan `end_date` masih wajib
  diisi pada record utama.
- Pengajuan cuti/sakit sekarang hanya membuat record utama dan banyak detail
  pada `leave_request_dates`; overlap dan kuota membaca detail tersebut.
- Kolom `start_date` dan `end_date` lama dibuat nullable untuk kompatibilitas
  data historis dan tidak lagi diisi oleh alur baru.
- Memindahkan seluruh tabel HRIS dari tabel `ws_hris_*` ke prefix modul
  `hris_*` melalui migrasi incremental, termasuk backfill detail tanggal lama.
- Model, koneksi transaksi, filter tahun, sorting tanggal, dashboard, approval,
  dan validasi nomor karyawan disesuaikan dengan koneksi HRIS.

## Verifikasi

- `php artisan migrate --no-interaction` berhasil menjalankan migrasi prefix
  HRIS dan backfill tanpa reset database.
- PHP lint seluruh model/service/controller HRIS yang berubah berhasil.
- Syntax check JavaScript dan `git diff --check` berhasil.

## Catatan kompatibilitas

- `start_date`/`end_date` dipertahankan sementara sebagai kolom nullable agar
  data lama dapat dibaca dan rollback tetap aman; sumber kebenaran pengajuan
  baru adalah detail tanggal.
