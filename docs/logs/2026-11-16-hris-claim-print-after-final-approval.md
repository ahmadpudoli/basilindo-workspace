# Cetak reimbursement setelah seluruh approval

- Menyembunyikan tombol Cetak selama reimbursement berada pada status `pending_final`.
- Membatasi endpoint cetak agar hanya melayani reimbursement setelah seluruh approval selesai; URL langsung juga ditolak selama menunggu approver final.
- Memperbarui requirement dan checklist untuk menegaskan approval final harus selesai sebelum cetak.
- Verifikasi: pemeriksaan sintaks PHP/Blade dan `git diff --check`; test belum dijalankan.
