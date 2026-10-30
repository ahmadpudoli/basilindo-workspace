# Preview lampiran pada daftar pengajuan cuti

## Perubahan

- Menambahkan tombol `Lihat dokumen` pada kolom aksi daftar Cuti & Sakit dan Ringkasan Cuti Karyawan jika pengajuan memiliki `supporting_document_id`.
- Tombol memakai modal preview Workspace dan endpoint lampiran HRIS yang tetap melewati policy `LeaveRequestPolicy`.
- PDF dan gambar dipreview inline; format lain menampilkan fallback buka/unduh melalui URL tanpa mode preview.
- Requirement dan checklist HRIS diperbarui.

## Verifikasi

- `php -l app/Modules/HRIS/Application/Services/LeaveRequestController.php`: berhasil.
- `git diff --check`: berhasil.
- `npm.cmd run build`: tidak dapat berjalan karena proses Vite tidak memiliki izin menulis `workspace/node_modules/.vite-temp`.
- `php artisan view:cache` dan test filter HRIS dihentikan setelah tidak menghasilkan output dalam lingkungan lokal; tidak ada perubahan database atau reset data yang dilakukan.

## Risiko tersisa

- Build asset dan test runtime perlu dijalankan ulang pada environment dengan permission `node_modules` dan koneksi service Laravel yang normal.
