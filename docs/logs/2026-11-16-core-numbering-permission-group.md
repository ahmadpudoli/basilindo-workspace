# Pengelompokan permission master penomoran

- Memasukkan permission legacy `manage_numbering_rules` ke subgrup Master penomoran pada grup Core & Pengaturan di form Role.
- Memperbarui requirement Pengaturan Workspace dan checklist fitur agar permission legacy yang masih dimiliki role tetap ditampilkan di grup yang sesuai.
- Verifikasi: pemeriksaan diff dan lint Blade statis belum dijalankan; perubahan hanya pada katalog tampilan permission dan dokumentasi.
- Risiko: tidak ada perubahan permission, role assignment, atau migrasi database; permission hanya berpindah kelompok tampilan.
