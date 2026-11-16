# Pilihan tipe pengajuan pada daftar claim

- Mengganti tombol Buat Pengajuan menjadi dropdown dengan pilihan Reimbursement dan Cash Advance.
- Reimbursement membuka form aktif; Cash Advance membuka halaman khusus yang menjelaskan bahwa form/workflow belum tersedia sehingga pengguna tidak terjebak pada validasi penyimpanan yang menolak tipe tersebut.
- Pilihan tipe memakai otorisasi `create` klaim saat ini. Permission terpisah belum dibuat.
- Memperbarui requirement dan checklist.
- Verifikasi: pemeriksaan sintaks PHP/Blade dan `git diff --check`; test belum dijalankan.
