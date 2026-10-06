# Requirement — Kebersihan schema database

## Tujuan

Memastikan tabel PostgreSQL di `file-organizer` hanya dipertahankan apabila memiliki
consumer aplikasi, konfigurasi framework yang aktif, atau fungsi domain yang memang
disengaja.

## Acceptance criteria

- [x] Daftar tabel aktual diverifikasi terhadap migration dan konfigurasi Laravel.
- [x] `saved_searches` tidak ada di database dan orphan model `SavedSearch` dihapus
      dari source code.
- [ ] `projects` dan `project_members` tetap dipertahankan untuk scope dokumen dan
      keanggotaan proyek.
- [ ] Consumer legacy ticket/epic dihapus dari model, Resource, Page, Widget, import,
      export, policy, test, dan view.
- [ ] Tabel `tickets`, `ticket_users`, `ticket_statuses`, `ticket_histories`,
      `ticket_comments`, `ticket_priorities`, `epics`, `project_notes`, dan
      `external_access` dihapus melalui migration cleanup.
- [ ] Kolom `projects.ticket_prefix` dihapus karena hanya digunakan untuk ticket ID.
- [ ] Fitur external dashboard dan notifikasi khusus ticket dipensiunkan.
- [x] Tabel framework dipertahankan karena masih terdaftar dalam konfigurasi fallback.
- [x] Tidak ada migration lama yang diubah.
- [ ] Audit schema dijalankan kembali setiap ada perubahan driver atau modul besar.

## Keputusan scope

`projects` bukan tabel ticket-management; tabel ini menjadi katalog/scope bisnis untuk
dokumen. Karena itu hanya bagian ticket-management yang dipensiunkan. Migration cleanup
memakai urutan drop berdasarkan foreign key dan tidak mengubah migration historis.

Status implementasi 2026-10-01: consumer legacy sudah dihapus dari source code dan
migration cleanup sudah dibuat untuk menghapus tabel legacy serta permission ticket.

Status implementasi 2026-10-01: consumer legacy sudah dihapus dari source code dan
migration cleanup sudah dibuat untuk menghapus tabel legacy serta permission ticket.
