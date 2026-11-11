# Penanda sumber data hasil integrasi Odoo

## Perubahan

- Menambahkan kolom `source_system`, `source_database`, `external_id`, `is_integrated`, dan `last_synced_at` pada account, contact, opportunity, project, dan company.
- Proses normalisasi Odoo sekarang mengisi penanda tersebut pada setiap upsert.
- Data lama hasil integrasi di-backfill berdasarkan `metadata` dan `core_external_references`.
- Model Workspace diperbarui agar flag integrasi dapat dibaca konsisten.

## Verifikasi

- Migration berhasil dijalankan.
- Initial sync ulang berhasil: 172 record diproses dan 0 gagal.
- Jumlah record bertanda integrasi: 90 account, 22 contact, 124 opportunity, 5 project, dan 50 company.
- Data Workspace murni tidak diberi flag Odoo secara otomatis.
