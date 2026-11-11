# Implementasi Integration Odoo read-only — 2026-11-09

## Perubahan

- Menambahkan modul Integration dengan halaman dashboard, uji koneksi, initial sync, incremental sync, scheduler, queue job, dan command.
- Menambahkan tabel external reference, projection record, sync run, dan sync error pada koneksi core.
- Adapter Odoo membaca `res_partner`, `crm_lead`, dan `project_project` melalui koneksi PostgreSQL terpisah.
- Tidak ada operasi tulis ke database Odoo.

## Verifikasi

- Migration `2026_11_09_000003_create_integration_tables` berhasil dijalankan.
- Initial sync read-only berhasil: 166 record diproses, 0 gagal (98 partner, 62 opportunity, 6 project).
- Route Integration, command `integration:sync-odoo`, scheduler 10 menit, PHP lint, dan Blade view cache berhasil diverifikasi.
- Permission Integration diberikan secara additive kepada `ahmad.pudoli@legospay.id`.
- Integration ditempatkan sebagai fitur Core pada Pengaturan → Integrasi Odoo; route lama tetap mengarah ke route baru untuk kompatibilitas.
- Memperbaiki submenu ganda, menghilangkan alert sukses duplikat dari halaman Integration, dan merapikan kartu ringkasan agar label tidak bertumpuk.
- Menambahkan projection `res_company`, field `company_id` pada projection project, halaman Mapping Perusahaan, serta penyimpanan mapping Odoo `res_company` ke company Workspace melalui external reference.
- Test `HrisWorkflowTest` tidak selesai dalam sesi verifikasi dan dihentikan; tidak ada indikasi perubahan Integration terhadap test tersebut.

## Risiko tersisa

- Projection belum menggantikan seluruh master CRM/Project lokal dan belum melakukan write-back.
- Normalisasi project ke `core_projects` masih menunggu mapping company; project tanpa mapping belum boleh dipakai HRIS.
- Memperbaiki query halaman mapping perusahaan dengan cast aman `core_companies.id::text` terhadap `external_references.local_id` yang bertipe string.
- Merapikan UI Mapping Perusahaan dengan ringkasan total/mapped/pending, tabel card responsif, status badge, avatar, dan form mapping yang tidak redundan.
- Credential Odoo harus diisi pada environment lokal/deployment, bukan repository.
- Mapping projection ke form lembur/reimbursement menjadi pekerjaan tahap berikutnya setelah struktur field final disepakati.
