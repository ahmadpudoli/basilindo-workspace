# Akses menu HRIS dan urutan permission role

## Perubahan

- Menambahkan penjelasan bahwa `access_hris` wajib untuk menampilkan modul HRIS.
- Permission HRIS pada form Role dikelompokkan menjadi Modul & workflow, Pegawai, Dokumen karyawan, Cuti & sakit, Lembur, Claim, serta Approval & ringkasan.
- Permission CRUD dalam setiap resource ditampilkan berurutan.

## Temuan

- User `fadlal@quinsis.co.id` pada screenshot belum mencentang `access_hris`. Permission granular yang dipilih belum cukup untuk melewati filter modul di layout.

## Verifikasi

- Pemeriksaan kode layout mengonfirmasi root modul HRIS menggunakan `access_hris`.
- `git diff --check` dijalankan setelah perubahan.
