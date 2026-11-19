# Requirement: Permission role collapsible

## Tujuan

Membuat daftar permission pada form tambah dan edit role lebih ringkas dengan mengelompokkan permission berdasarkan modul.

## Acceptance criteria

- Form tambah role dan edit role menampilkan satu panel untuk setiap grup modul.
- Setiap panel dapat dibuka dan ditutup melalui tombol header.
- Semua panel dalam kondisi tertutup ketika halaman pertama kali dibuka.
- Checkbox `Pilih semua` tetap tersedia pada header setiap panel dan tetap menyinkronkan seluruh permission dalam grup.
- Nilai permission yang sudah dipilih pada form edit tetap terjaga walaupun panel awalnya tertutup.
- Permission `process_hris_payment` ditampilkan pada grup HRIS, subgrup Approval & ringkasan, pada form role dan pengguna.
- Permission `manage_hris_expense_categories` dan `manage_hris_settings` ditampilkan pada grup Core & Pengaturan, subgrup Pengaturan HRIS, pada form role dan pengguna.
