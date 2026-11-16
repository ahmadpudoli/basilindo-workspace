# Daftar Pengajuan Lembur Pribadi

- Membatasi `/workspace/hris/overtime` ke pengajuan dengan `requester_user_id` akun aktif atau relasi karyawan yang terhubung ke akun tersebut.
- Company scope yang lebih luas tidak lagi membuat daftar pribadi menampilkan pengajuan karyawan lain.
- Filter, ringkasan, dan pagination memakai query yang sudah dibatasi pemilik.
- Verifikasi: pemeriksaan sintaks PHP dan diff; test suite tidak dijalankan.
