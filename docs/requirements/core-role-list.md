# Requirement daftar Role Core

## Tujuan

Admin dapat menemukan role dengan cepat dan mengelola role melalui daftar tabel yang konsisten dengan pola pagination Workspace.

## Acceptance criteria

- Daftar role memiliki filter nama role, guard, dan jumlah baris per halaman.
- Filter diproses server-side, mempertahankan query string saat berpindah halaman, dan halaman kembali ke halaman pertama saat filter baru diterapkan.
- Tabel menampilkan nama role, jumlah pengguna, guard, dan kolom aksi dalam keadaan responsif.
- Pagination menampilkan ringkasan rentang baris dan total role.
- Tombol Edit membuka form perubahan role/permission.
- Tombol Hapus hanya tersedia untuk role non-sistem, meminta konfirmasi, dan tidak tersedia untuk `super_admin`.
- Empty state menjelaskan bahwa hasil tidak ditemukan berdasarkan filter aktif.
- Akses halaman dan aksi tetap melewati policy Role yang sudah ada.

## Aturan penggunaan tombol

- `Tambah role`: membuat role baru dan hanya digunakan ketika membutuhkan kelompok akses baru.
- `Edit`: mengubah identitas role dan permission; periksa dampak ke pengguna sebelum menyimpan.
- `Hapus`: hanya untuk role non-sistem yang sudah tidak digunakan; konfirmasi wajib dilakukan.
- `Hapus` pada `super_admin`: disabled karena role sistem dilindungi.
- Permission pada form role dikelompokkan berdasarkan fitur halaman; permission
  CRUD untuk satu resource ditampilkan berurutan dalam satu subkelompok.
- Permission `access_hris` dijelaskan sebagai akses modul utama; permission
  granular HRIS tidak menggantikan permission pembuka modul.
- Form Role dan Edit Pengguna menggunakan grouping fitur/halaman yang sama untuk
  seluruh modul, dengan urutan permission CRUD per resource.
- Subkelompok fitur menggunakan panel expandable yang tertutup secara default,
  menampilkan jumlah permission dan jumlah pilihan secara ringkas.
