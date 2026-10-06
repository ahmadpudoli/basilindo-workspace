# Architecture Decision Records

## ADR-007 — Admin console dan recovery password lokal

Status: accepted.

Admin console SSO berada di `project-sso` pada `/admin/login` dan menjadi tempat pengelolaan user, role, serta akses aplikasi internal. Seeder development memakai `LOCAL_ADMIN_EMAIL` dan `LOCAL_ADMIN_PASSWORD`, lalu memperbarui password seluruh user SSO dengan hash. Default tersebut hanya untuk recovery environment lokal dan wajib diganti sebelum deployment.

## ADR-001 — PostgreSQL sebagai source of truth

Status: accepted.

Metadata, workflow, permission scope, dan audit disimpan di PostgreSQL. Object binary disimpan di MinIO dan direferensikan melalui object key, checksum, serta version metadata.

## ADR-002 — MinIO private object storage

Status: accepted.

Dokumen keuangan tidak disimpan di public disk. Akses diberikan melalui policy check lalu signed URL sementara atau streaming response.

## ADR-003 — Redis untuk asynchronous work

Status: accepted.

Redis dipakai sebagai cache, queue backend, lock, dan rate limiter. Data penting tidak boleh hanya ada di Redis.

## ADR-004 — Modular monolith terlebih dahulu

Status: accepted.

Laravel + Filament cukup untuk MVP dan memudahkan transaksi/audit. Boundary domain dijaga agar integrasi atau pemisahan service dapat dilakukan kemudian bila skala membutuhkannya.

## ADR-005 — `project-sso/` sebagai internal Identity Provider

Status: accepted.

`project-sso/` disiapkan sebagai SSO internal perusahaan dan menjadi Identity Provider OIDC untuk File Organizer serta aplikasi internal lain. File Organizer hanya menjadi relying party/client dan tetap mengelola permission bisnis, company scope, dan audit domain. Engine/implementasi detail SSO mengikuti dokumentasi di project tersebut.

## ADR-008 — Akun admin sistem dan tabel tanpa consumer

Status: accepted.

`admin@example.com` direpresentasikan oleh role aplikasi `super_admin` dan flag
`users.is_system_account`. Akun ini dilindungi oleh policy/model dan trigger PostgreSQL
agar tidak dapat dihapus melalui jalur aplikasi maupun query massal. Password berasal dari
`LOCAL_ADMIN_PASSWORD` pada environment development.

Audit schema menemukan `saved_searches` belum memiliki consumer aplikasi, sedangkan tabel
project/ticket dan infrastructure masih dipakai. Karena itu hanya `saved_searches` yang
dihapus; tabel legacy lain ditunda sampai seluruh consumer dipensiunkan.

## ADR-009 — Identifier application access untuk admin UI

Status: accepted.

Tabel `application_user_access` tetap menjaga unique constraint pada kombinasi
`application_id`, `user_id`, dan `role_code`, tetapi memakai surrogate `id` sebagai
primary key. Filament membutuhkan identifier tunggal yang stabil untuk route edit,
delete, dan action per-record; composite key tidak cukup aman untuk kebutuhan tersebut.

## ADR-010 — Hasil audit schema File Organizer

Status: accepted.

Audit database pada 2026-10-01 menemukan 39 tabel aktual dan tidak menemukan tabel
`saved_searches`. Tabel domain ticket/project tetap dipakai oleh Resource, Page, Widget,
import/export, dan relasi model sehingga tidak dihapus. Tabel infrastruktur Laravel
dipertahankan karena masih menjadi target konfigurasi fallback, walaupun environment
lokal memakai Redis untuk cache, queue, dan session. Orphan model `SavedSearch` dihapus
dari source code.
# Shared core dan ownership data

- **Status:** Accepted — 2026-10-02
- **Keputusan:** `core/` menjadi source code bersama untuk model general, migration, factory, dan library yang dipakai File Organizer serta Project Management. Model domain khusus tetap berada di aplikasi masing-masing. `project-sso/` tetap terpisah.
- **Alasan:** menghindari duplikasi model dan aturan bisnis, menjaga referensi project/company/vendor konsisten, serta tetap menyediakan jalur ekstraksi menjadi package/service di masa depan.
- **Alternatif ditolak:** model duplikat di setiap aplikasi dan shared database access tanpa data ownership. Penggunaan tabel ber-prefix kemudian dipilih karena database kini dipusatkan.
- **Konsekuensi:** perubahan schema core harus diuji terhadap kedua aplikasi; relasi ke domain khusus memakai extension point; migration existing harus diverifikasi per environment.


# Database terpusat dan prefix tabel

- **Status:** Accepted � 2026-10-02
- **Keputusan:** File Organizer dan Project Management memakai PostgreSQL terpusat `db_file_organize`. Tabel shared memakai prefix `core_`, File Organizer memakai `fo_`, dan Project Management memakai `pm_`.
- **Alasan:** data project, company, vendor, dan user perlu konsisten serta dapat digunakan lintas aplikasi, sementara tabel domain tetap terisolasi.
- **Implementasi:** seluruh migration aktif disimpan di `core/database/migrations` dalam kelompok `shared`, `file-organizer`, dan `project-management`. File Organizer menjalankan migration `shared`; setiap aplikasi menjalankan set domain miliknya dari `core`.
- **Konsekuensi:** foreign key lintas prefix tidak dibuat melalui koneksi domain karena Laravel otomatis menambahkan prefix koneksi. Reference ID lintas domain divalidasi di service/policy; foreign key internal masing-masing prefix tetap digunakan.