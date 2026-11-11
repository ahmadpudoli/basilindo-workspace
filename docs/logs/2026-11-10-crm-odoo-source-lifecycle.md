# Log — CRM Odoo source lifecycle

Status: selesai

## Perubahan

- Menambahkan kolom incremental `source_record_status` pada opportunity CRM.
- Full sync Odoo merekonsiliasi `crm.lead` aktif; item yang tidak lagi ada/aktif diberi status `deleted`, `deleted_at`, dan external reference `sync_status=deleted`.
- Record Odoo yang muncul kembali dipulihkan berdasarkan external reference yang sama.
- Pipeline menyembunyikan item source-deleted secara default dan menyediakan opsi `Tampilkan yang dihapus di Odoo` dengan badge `Dihapus di Odoo`.
- Tidak ada penghapusan permanen dan tidak ada perubahan pada binary/dokumen terkait.

## Verifikasi

- Migration `2026_11_10_000001_add_crm_pipeline_source_lifecycle` berhasil berstatus `Ran`.
- PHP lint untuk service integrasi, controller pipeline, dan model opportunity berhasil.
- View cache berhasil dibangun.

## Risiko tersisa

- Rekonsiliasi penghapusan hanya dapat dipastikan pada full sync; incremental sync tidak dapat mengetahui record yang hilang dari result Odoo.
- Soft-deleted item tetap memerlukan kebijakan retensi/purge terpisah bila suatu saat ingin dihapus permanen.
