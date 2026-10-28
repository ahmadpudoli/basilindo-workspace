# Penyederhanaan status workflow cuti dan sakit

## Perubahan

- Menghapus status `Diajukan`, `Dibatalkan`, dan `Kadaluarsa` dari filter workflow
  cuti/sakit.
- Pengajuan baru langsung menggunakan status `pending_manager` / Menunggu atasan.
- Status berikutnya adalah `pending_hr`, `approved`, atau `rejected`.
- Status historis lama dinormalisasi dengan tabel repair agar dapat di-rollback.
- Waktu pembuatan dan timeline tetap menjadi sumber informasi event submit.

## Verifikasi

- Migration normalisasi status berhasil dijalankan tanpa reset database.
- Dashboard dan scope aktif tidak lagi menganggap `submitted` sebagai status aktif.
