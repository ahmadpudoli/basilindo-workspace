# Requirement Integration Odoo Read-only

## Tujuan

Menyediakan boundary integrasi untuk mengambil referensi Odoo yang dibutuhkan CRM, project, lembur, dan reimbursement tanpa menulis ke database Odoo.

Fitur ini dimiliki platform Core dan diakses melalui **Pengaturan → Integrasi Odoo**, bukan sebagai modul launcher bisnis terpisah.

## Scope MVP

- koneksi PostgreSQL Odoo melalui environment, dengan akses database read-only;
- scheduler hanya dispatch job sinkronisasi melalui queue; service sync dijalankan oleh queue worker;
- job sinkronisasi menggunakan queue bernama odoo-sync;
- initial sync dan incremental sync melalui queue;
- projection `res_partner`, `crm_lead`, dan `project_project` ke database Workspace;
- projection `res_company` dan mapping manual `res_company` Odoo ke `core_companies` Workspace;
- normalisasi runtime berbasis upsert untuk pipeline CRM ke satu tabel `crm_opportunities` dengan `record_type` `lead` atau `opportunity`;
- normalisasi runtime project Odoo ke `core_projects` hanya setelah company mapping tersedia;
- external reference berdasarkan source system, database, entity type, dan external ID;
- riwayat proses dan error tanpa menyimpan credential atau isi sensitif;
- scheduler dapat diaktifkan melalui `ODOO_SYNC_ENABLED`;
- transaksi HRIS tetap menjadi source of truth Workspace.

## Kebijakan data integrasi

- Migration hanya membuat struktur tabel integrasi, index, constraint, dan permission yang diperlukan.
- Data perusahaan, contact, opportunity, dan project dari Odoo tidak boleh ditanam sebagai seed/master migration.
- Data Odoo masuk melalui proses sync runtime dan disimpan sebagai projection/reference record.
- Mapping perusahaan Odoo ke perusahaan Workspace adalah data operasional yang disimpan melalui menu Integrasi Odoo, bukan data migration.
- Jika kelak project diproyeksikan ke tabel domain Workspace, prosesnya tetap dilakukan oleh service sync/normalisasi setelah mapping tersedia; bukan melalui migration atau seeder.

## Boundary

Odoo adalah source of truth CRM/Sales/Project. Workspace hanya menyimpan projection referensi untuk kebutuhan pemilihan project dan konteks pengajuan. Tidak ada INSERT, UPDATE, atau DELETE ke database Odoo.

## Acceptance criteria

1. User dengan permission Integration dapat menguji koneksi dan menjalankan initial/incremental sync.
2. Job yang diulang tidak membuat duplicate karena unique external reference.
3. Kegagalan pada satu record dicatat tanpa menghentikan seluruh batch.
4. Form HRIS tetap menggunakan transaksi lokal dan tidak bergantung pada query langsung saat halaman dibuka.
5. Adapter data Odoo dapat diganti dari PostgreSQL read-only ke API tanpa mengubah service sync.
6. Project Odoo membawa `company_id` pemilik; project yang belum memiliki mapping perusahaan tidak boleh dinormalisasi sebagai project HRIS.
7. Sync berulang mempertahankan mapping perusahaan dan tidak membuat duplicate CRM pipeline item atau project.
8. Halaman riwayat sinkronisasi menyediakan pencarian, filter jenis/status, pagination 10/25/50/100, dan sorting server-side melalui query string.
9. Status pipeline hasil sync merepresentasikan closing status; jika Odoo belum menunjukkan closing/lost/closed, `status` dan `closing_status_id` tetap `null`.
10. Field closing dan komersial (`lost_reason`, `won_at`, `closed_at`, `po_number`, `po_document_id`, `team_id`) hanya diisi dari sumber atau mapping yang tersedia; field tanpa sumber tidak boleh diisi dengan asumsi.
11. Mapping perusahaan menggunakan external key Odoo; pencocokan nama hanya menghasilkan kandidat dan tidak menyimpan mapping tanpa konfirmasi user.
12. Satu perusahaan Workspace tidak boleh dipetakan ke lebih dari satu perusahaan Odoo dalam source database yang sama.
13. Command scheduler tidak menjalankan service sinkronisasi secara langsung; command hanya dispatch RunOdooReferenceSync dan queue worker menjalankannya.
14. Job sync incremental dan full tidak boleh berjalan bersamaan dengan jenis yang sama karena job memiliki unique lock.
