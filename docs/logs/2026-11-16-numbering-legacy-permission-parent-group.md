# Perbaikan grup utama permission master penomoran

- Memetakan `manage_numbering_rules` ke grup utama Core & Pengaturan pada form Role dan Pengguna.
- Katalog subgrup Master penomoran sudah mencakup permission tersebut; mapping grup utama yang sebelumnya hilang menyebabkan permission jatuh ke Lainnya sebelum katalog subgrup dijalankan.
- Memperbarui requirement dan checklist.
- Verifikasi: pemeriksaan sintaks Blade dan `git diff --check`; test belum dijalankan.
