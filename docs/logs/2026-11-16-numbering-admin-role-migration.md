# Akses role untuk master penomoran

- Memastikan kebijakan akses `manage_numbering_rules` terbatas pada role `admin` dan `super_admin`.
- Migration yang sudah tersedia adalah `2026_11_03_000006_provision_numbering_permissions`; migration `2026_11_12_000003_granularize_settings_permissions` mewariskan akses kedua role tersebut ke permission view/create/update.
- Tidak menambah migration duplikat karena kedua migration existing sudah menyiapkan assignment untuk deploy.
- Verifikasi: inspeksi statis isi migration; migration tidak dijalankan.
