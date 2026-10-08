# File Organizer — Agent Instructions

## Tujuan proyek

Bangun aplikasi manajemen dokumen perusahaan untuk membantu tim Finance dan admin:

- menyimpan dokumen secara terpusat;
- mencari dokumen dengan banyak kriteria sekaligus;
- menghubungkan dokumen berdasarkan proyek, perusahaan, vendor, periode, dan nomor referensi;
- melakukan verifikasi dan validasi dokumen;
- mengunduh kumpulan dokumen sebagai bundel ZIP;
- menyediakan audit trail dan kontrol akses yang dapat dipertanggungjawabkan.

Implementasi runtime aplikasi berada di `app/workspace/`. Basilindo Workspace adalah satu aplikasi Laravel Filament modular monolith yang memuat Core, Company, CRM, Project Management, dan Document Management. Dokumentasi, keputusan arsitektur, log pekerjaan, dan checklist berada di `docs/`. `app/file-organizer/`, `app/project-management/`, dan `app/project-sso/` adalah sumber legacy selama migrasi dan tidak boleh dijalankan sebagai runtime.

## Kontrak teknologi

- PHP 8.2+ dan Laravel 12.
- Filament 4 sebagai admin application/UI.
- PostgreSQL sebagai database utama.
- Redis sebagai cache, queue, lock, dan rate limiting jika sesuai kebutuhan.
- MinIO sebagai object storage S3-compatible untuk file dokumen dan hasil bundling sementara.
- Docker Compose untuk lingkungan pengembangan lokal.
- Filament Shield atau mekanisme permission setara untuk role dan permission.
- Gunakan pola, kualitas UI, warna, dan pengalaman pengguna dari `project-management`, tetapi buat domain model dan navigasi khusus untuk document management.

## Aturan kerja agen

1. Baca `docs/README.md`, dokumen arsitektur yang relevan, dan checklist sebelum mengubah kode.
2. Baca kode di `app/project-management/` hanya sebagai referensi. Jangan menyalin konfigurasi secara membabi buta, terutama kredensial, migration domain, dan data proyek.
3. Sebelum implementasi fitur, tulis atau perbarui requirement dan acceptance criteria di `docs/`.
4. Setiap pekerjaan harus memperbarui checklist fitur dan menambahkan entri pada `docs/logs/`.
5. Jangan menyimpan file dokumen di local public disk. Semua file bisnis harus melalui MinIO/private S3 disk.
6. Jangan menaruh isi file sensitif di log. Log hanya boleh memuat ID, tipe aktivitas, status, dan metadata non-rahasia.
7. Semua query yang menampilkan atau mengunduh dokumen wajib melewati authorization policy dan scope organisasi/perusahaan.
8. Jangan menghapus dokumen secara permanen tanpa kebijakan retensi, permission khusus, audit event, dan mekanisme soft delete/quarantine.
9. Validasi input di boundary, gunakan Form Request/Filament validation, service class untuk proses domain, dan job untuk pekerjaan berat.
10. Hindari query N+1, akses object storage yang tidak perlu, dan pemuatan file penuh ke memory untuk proses bundling.
11. Tambahkan test untuk rule bisnis, authorization, upload, pencarian, matching, download bundle, dan failure path.
12. Jangan menambahkan library baru sebelum memeriksa apakah Laravel/Filament atau package yang sudah tersedia dapat menyelesaikan kebutuhan.
13. Gunakan Bahasa Indonesia untuk label antarmuka bisnis, tetapi gunakan nama class, table, enum, dan kode internal dalam Bahasa Inggris yang konsisten.
14. Bila ada keputusan yang belum pasti, catat asumsi dan alternatifnya di `docs/decisions.md`; jangan menyembunyikan keputusan di dalam kode.

## Urutan agen dan fase pekerjaan

### Agent 0 — Discovery dan baseline

- Audit isi `app/workspace/` dan sumber legacy hanya sebagai referensi migrasi.
- Pastikan versi Laravel, Filament, PHP, dan paket yang dipakai terdokumentasi.
- Buat `.env.example`, Docker Compose, health checks, dan README setup tanpa secret.
- Output: baseline yang dapat dijalankan, dokumentasi setup, dan log.

### Agent 1 — Domain model dan database

- Definisikan organisasi/perusahaan, user, project, vendor, document type, document, document relation, verification, bundle, dan audit event.
- Gunakan UUID/ULID untuk identifier publik dan index PostgreSQL yang sesuai.
- Simpan object key, checksum, MIME, ukuran, versi, dan status file; jangan simpan binary di PostgreSQL.
- Output: migration, model, enum/value object, factory/seeder minimal, dan test integritas.

### Agent 2 — Identity, SSO internal, dan authorization

- Bangun login lokal Laravel/Filament pada `app/workspace/` sebagai satu-satunya authentication runtime.
- Jangan menambahkan SSO, OIDC, Google login, atau login lintas aplikasi tanpa ADR baru dan persetujuan arsitektur.
- Terapkan role, permission, company/project scope, session security, dan audit login.
- Output: policy, role matrix, session security, audit login, dan test authorization.

### Agent 3 — Object storage dan dokumen

- Konfigurasikan private MinIO disk S3-compatible.
- Bangun upload aman, checksum, nama object key acak/terstruktur, download melalui temporary signed URL atau streamed response terotorisasi.
- Tambahkan versioning metadata, soft delete, quarantine, dan retry job bila diperlukan.
- Output: document service, upload UI, detail/preview metadata, dan test storage.

### Agent 4 — Search dan metadata

- Bangun filter berdasarkan project, company, vendor, document type, date range, year, amount, status, tags, reference number, dan full-text metadata/OCR bila sudah tersedia.
- Gunakan PostgreSQL search terlebih dahulu; siapkan interface agar search engine khusus dapat ditambahkan tanpa mengubah domain.
- Pastikan semua hasil search sudah terfilter authorization scope.
- Output: halaman pencarian utama, saved filters jika diperlukan, pagination, sorting, dan query tests.

### Agent 5 — Matching, verification, dan validation

- Definisikan rule pencocokan sebagai rule yang dapat dijelaskan, bukan skor AI yang tidak transparan.
- Tahap awal mendukung relasi PO–invoice–receipt/payment, tetapi domain harus dapat diperluas.
- Simpan hasil, rule yang terpenuhi/gagal, reviewer, waktu, komentar, dan evidence.
- Pisahkan `system_match_result` dari keputusan manual reviewer.
- Output: workflow verifikasi, discrepancy list, approval/review action, dan audit events.

### Agent 6 — Bundling dan export

- Sediakan bundling berdasarkan project, vendor, company, periode, status, atau hasil pencarian tersimpan.
- Gunakan queued job, manifest file, batas ukuran/jumlah, expiry, dan status progress.
- Buat ZIP di storage private, berikan download authorization dan automatic cleanup.
- Output: bundle request UI, job, manifest, download, expiry/cleanup, dan test concurrency.

### Agent 7 — Dashboard, reporting, dan UX

- Ikuti gaya visual `app/project-management`: sidebar Filament, cards, tables, filters, status colors, dan responsive layout.
- Prioritaskan dashboard: dokumen masuk, belum lengkap, perlu review, mismatch, dan aktivitas terbaru.
- Sediakan empty state, error state, confirmation, dan bahasa yang mudah dipahami admin.
- Output: dashboard dan resource/page lengkap untuk MVP.

### Agent 8 — Security, quality, dan release

- Jalankan test suite, lint/format, static checks bila tersedia, dependency audit, dan security review.
- Verifikasi secret hygiene, object access, tenant isolation, rate limits, upload limits, backup/restore, dan retention.
- Buat deployment checklist, observability, health endpoint, dan rollback plan.
- Output: release candidate dan laporan verifikasi di `docs/logs/`.

## Definition of Done

Fitur dianggap selesai apabila:

- acceptance criteria terdokumentasi;
- migration dan rollback aman;
- policy/authorization sudah diuji;
- happy path dan failure path memiliki test;
- tidak ada secret atau binary sensitif di repository;
- status checklist diperbarui;
- log pekerjaan berisi perubahan, verifikasi, dan risiko tersisa;
- UI memiliki loading, empty, validation, dan error state yang layak.

## Perintah verifikasi minimum

Sesuaikan dengan isi `app/workspace/`, tetapi target akhirnya adalah:

```bash
php artisan test
php artisan migrate:fresh --seed
php artisan optimize:clear
npm run build
```

Untuk pekerjaan yang mengubah storage, queue, auth, atau permission, verifikasi juga dengan stack Docker Compose dan test integrasi yang relevan.

## Dokumentasi wajib

- `docs/README.md` — peta dokumentasi dan cara kerja.
- `docs/architecture.md` — arsitektur, boundary, dan aliran data.
- `docs/domain-model.md` — entitas dan relasi utama.
- `docs/security.md` — threat model dan kontrol keamanan.
- `docs/sso.md` — strategi SSO/OIDC dan migrasi identitas.
- `docs/roadmap.md` — fase delivery.
- `docs/feature-checklist.md` — checklist status fitur.
- `docs/decisions.md` — keputusan arsitektur penting.
- `docs/logs/` — log kronologis pekerjaan agen.
