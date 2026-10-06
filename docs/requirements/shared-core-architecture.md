# Requirement: Shared Core Antaraplikasi

## Tujuan

Menyediakan satu sumber kode untuk model, migration, factory, dan library general yang digunakan bersama oleh `file-organizer/` dan `project-management/`, menggunakan PostgreSQL terpusat tanpa menjadikan `project-sso/` sebagai bagian dari shared domain tersebut.

## Scope

- Shared code berada di `core/` dan menggunakan namespace `Core`.
- Model general: `User`, `Company`, `ProjectParty`, `Project`, `Roles`, `Setting`, dan `ExternalIdentity`. Vendor/client adalah role pada master `Company`, bukan entity perusahaan duplikat.
- Seluruh migration aktif berada di `core/database/migrations`, dikelompokkan menjadi `shared/`, `file-organizer/`, dan `project-management/`.
- Domain khusus tetap dimiliki aplikasi: dokumen/verifikasi/bundling di File Organizer dan ticket/epic/notification di Project Management.
- Database aplikasi menggunakan PostgreSQL terpusat `db_file_organize` pada `127.0.0.1:5435`.
- Prefix tabel: `core_` untuk shared core, `fo_` untuk File Organizer, dan `pm_` untuk Project Management.
- Migration `shared/` hanya memiliki satu owner, yaitu File Organizer (`CORE_MIGRATIONS_OWNER=true`); set migration domain dijalankan dari `core` sesuai `APP_MIGRATION_SET` masing-masing aplikasi.

## Acceptance criteria

- [x] Kedua aplikasi memuat `Core` melalui PSR-4 `../core/src/`.
- [x] `CoreServiceProvider` memuat migration shared dan alias kompatibilitas `App\\Models` selama masa transisi.
- [x] Model shared tidak lagi disimpan sebagai implementasi duplikat di `file-organizer/app/Models` atau `project-management/app/Models`.
- [x] Seluruh migration aktif dipusatkan di `core` dan set migration tidak dieksekusi dua kali.
- [x] Relasi ke domain khusus menggunakan extension point konfigurasi, bukan duplikasi model shared.
- [x] Ownership data dan aturan pemisahan domain terdokumentasi.
- [x] Kedua aplikasi memakai database PostgreSQL terpusat dan prefix tabel `core_`, `fo_`, dan `pm_`.
- [x] Master company mendukung Group Basilindo, anak perusahaan internal, dan perusahaan eksternal dengan parent hierarchy, classification, serta business roles.
- [x] Project dan dokumen dapat menyimpan client/vendor/partner melalui relasi party.
- [x] Seed development menyediakan Group Basilindo dan enam anak perusahaan internal.
- [ ] Migrasi database existing pada environment staging dijalankan dan diverifikasi; belum dijalankan karena membutuhkan database aktif.

## Catatan implementasi

Saat aplikasi berkembang menjadi service terpisah, package `core/` dapat dipublikasikan sebagai package Composer internal. Untuk saat ini source path lokal dipakai agar dua aplikasi tetap dapat berbagi kode tanpa repository/package registry tambahan.
