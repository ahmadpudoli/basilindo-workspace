# Pembatasan akses detail pengajuan cuti/sakit — 2026-11-17

## Perubahan

- Menambahkan ability policy `viewOwner` untuk pemohon, pembuat pengajuan, atau akun karyawan yang memiliki pengajuan.
- Route detail `/workspace/hris/leave-requests/{id}` kini memakai ability khusus pemilik; permission HRIS perusahaan tidak lagi cukup untuk membuka detail pemohon lain.
- Route detail approval tetap terpisah dan memakai authorization approval yang sudah ada.

## Verifikasi

- Pemeriksaan sintaks PHP dan diff whitespace dilakukan.
- Test suite tidak dijalankan.

## Catatan

- Route ringkasan dan summary detail tetap memakai scope/permission summary yang berlaku; perubahan ini khusus route detail pengajuan pemilik.
