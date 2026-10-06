# Arsitektur Sistem

## Prinsip

File Organizer dan Project Management adalah dua aplikasi Laravel + Filament yang berbagi `core/` sebagai source code untuk domain general. Domain tetap dipisahkan melalui service, policy, job, dan event agar kelak dapat diekstrak atau diintegrasikan dengan aplikasi grup perusahaan tanpa rewrite besar. `project-sso/` adalah sistem terpisah dan bukan bagian dari shared core.

## Komponen

```text
Browser
  -> Nginx/TLS
  -> Laravel + Filament 4
       -> PostgreSQL terpusat `db_file_organize` (tabel `core_`, `fo_`, dan `pm_`)
       -> Redis       (cache, queue, lock, rate limit)
       -> MinIO/S3    (binary file, preview, bundle ZIP)
       -> project-sso/ (internal OIDC Identity Provider)
```

- **project-sso/**: central identity provider internal untuk login dan federation beberapa aplikasi perusahaan.
- **core/**: shared code untuk model general, migration, factory, dan library yang dipakai File Organizer serta Project Management. Core bukan UI dan bukan identity provider.
- **Laravel/Filament**: UI admin, policies, validation, orchestration, dan domain services; File Organizer tidak menjadi identity provider.
- **PostgreSQL**: source of truth terpusat untuk metadata dan status bisnis. Binary tidak disimpan di database. Koneksi default setiap aplikasi memakai prefix domain (`fo_`/`pm_`), sedangkan model shared memakai koneksi `core` dengan prefix `core_`.
- **Redis**: cache hasil yang aman, queue backend, distributed lock, dan throttling. Redis bukan source of truth.
- **MinIO**: bucket private dengan object key yang tidak berasal langsung dari nama file pengguna.
- **Queue worker**: OCR, checksum, preview, matching berat, dan bundling berjalan asynchronous.

## Boundary modul

### Shared core

- `Core\\Models\\User`: identitas aplikasi dan relasi membership general.
- `Core\\Models\\Company`: master seluruh entitas perusahaan, baik Group/anak perusahaan internal maupun client/vendor eksternal.
- `Core\\Models\\ProjectParty`: hubungan perusahaan dengan project berdasarkan role `client`, `vendor`, atau `partner`.
- `Core\\Models\\Project`: project lintas aplikasi; fitur ticket atau dokumen tetap dimiliki aplikasi pemakainya.
- `Core\\Models\\Setting`, `Roles`, dan `ExternalIdentity`: fungsi general aplikasi.
- Seluruh file migration aktif berada di `core/database/migrations`, dikelompokkan menjadi `shared/`, `file-organizer/`, dan `project-management/`.
- Ownership logic tetap mengikuti domain, tetapi `core` menjadi satu-satunya source of truth untuk schema database.
- Migration `shared/` dijalankan oleh File Organizer sebagai owner core; masing-masing set aplikasi dijalankan oleh aplikasi pemiliknya melalui path di dalam `core`.

Ownership perubahan data harus mengikuti pemilik domain. Aplikasi lain menyimpan reference ID dan memakai service/API/event ketika boundary dipisahkan; tidak boleh membuat salinan model yang sama dengan aturan bisnis berbeda.

- `Identity`: user, external identity, company membership, roles, permissions.
- `Catalog`: group/anak perusahaan, client/vendor sebagai role perusahaan, project, project parties, document type, tags.
- `Documents`: upload, versions, metadata, object storage, lifecycle.
- `Search`: filter, sorting, full-text metadata, saved searches.
- `Verification`: relations, matching rules, review decisions, discrepancies.
- `Bundles`: selection snapshot, ZIP generation, expiry, download log.
- `Audit`: append-oriented activity and security events.

Gunakan `core/src` untuk shared general code dan `app/Domain` atau struktur service yang konsisten untuk domain aplikasi; jangan menaruh seluruh logic bisnis di Resource Filament atau controller.

## Aliran upload

1. User membuka form upload dan melewati policy.
2. Server memvalidasi MIME allowlist, ukuran, extension, dan batas jumlah.
3. File masuk ke quarantine/private object key dan checksum dihitung.
4. Metadata disimpan dalam transaksi PostgreSQL.
5. Job asynchronous melakukan scan/preview/OCR sesuai konfigurasi.
6. Dokumen menjadi `ready` atau `rejected`; setiap transisi tercatat di audit.

## Aliran pencarian

Search selalu membangun authorization scope lebih dahulu, kemudian filter metadata. Hasil harus paginated, memiliki index yang tepat, dan tidak mengungkap URL object permanen. Full-text isi dokumen adalah fase tambahan; metadata terstruktur tetap wajib.

## Aliran bundling

1. User memilih filter atau dokumen dan mengajukan bundle.
2. Aplikasi menyimpan snapshot kriteria dan daftar ID yang diizinkan.
3. Queue job membaca object secara streaming, membuat ZIP di bucket private, dan menulis manifest.
4. UI menampilkan progress/status.
5. User yang memiliki permission mengunduh melalui URL singkat/temporary signed URL.
6. Job cleanup menghapus bundle yang expired dan mencatat event.

## Deployment dan operasional

Development memakai Docker Compose: app, nginx, PostgreSQL, Redis, dan MinIO. `project-sso/` memiliki deployment dan database/secret lifecycle yang terpisah. Production wajib memisahkan secret, memakai TLS, backup PostgreSQL, lifecycle/backup MinIO, monitoring queue, log aggregation, dan health checks. Jangan menggunakan default credential atau exposed MinIO console tanpa kontrol jaringan.
