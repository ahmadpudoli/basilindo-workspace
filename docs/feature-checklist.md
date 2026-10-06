# Feature Checklist

Status: `[ ]` belum mulai, `[-]` berjalan, `[x]` selesai, `[!]` blocked/keputusan dibutuhkan.

## Platform

- [x] Shared core (`core/`) untuk model, migration, factory, dan library general
- [x] Seluruh migration aktif dipusatkan di `core/database/migrations`
- [x] Ownership data dan boundary File Organizer/Project Management terdokumentasi
- [x] PostgreSQL terpusat dengan prefix tabel `core_`, `fo_`, dan `pm_`
- [x] Laravel 12 + Filament 4 baseline
- [x] PostgreSQL connection dan migrations
- [x] Redis cache/queue/lock/rate limit
- [x] MinIO private S3 disk dan bucket lifecycle
- [ ] Docker Compose health checks
- [x] `.env.example` tanpa secret
- [x] Local development command Windows-safe (Pail dipisah dari `composer run dev`)
- [ ] CI test/build baseline

## Identity dan akses

- [x] Local development login
- [x] User/company/project scope
- [x] Role dan permission matrix
- [ ] Policy untuk list/view/upload/download/edit/delete
- [ ] Audit login dan permission changes
- [x] OIDC provider abstraction
- [x] Register File Organizer sebagai client di `project-sso/`
- [x] OIDC discovery, PKCE, state, nonce, dan callback validation
- [x] Group/role mapping SSO
- [x] Logout/session revocation dengan `project-sso/`
- [x] Session cookie SSO dan File Organizer terisolasi
- [x] Linking akun recovery lokal ke subject SSO dengan flag development eksplisit
- [x] Application access list memakai default sort eksplisit untuk pivot composite key
- [x] OIDC userinfo membaca entitlement berdasarkan client dari API guard

## Catalog dan metadata

- [x] Company management (Group, anak perusahaan, client/vendor sebagai role)
- [x] Project management
- [x] Master perusahaan menyatukan internal, client, vendor, dan partner
- [x] Assignment multi-company pada user dengan scope role
- [x] Project list dan bulk actions kompatibel dengan Filament 4
- [x] Vendor management (legacy view; canonical entity tetap `companies`)
- [x] Document type management
- [ ] Required metadata per document type
- [ ] Tags dan reference normalization
- [x] Duplicate detection dasar

## Document lifecycle

- [x] Secure upload validation
- [ ] Quarantine/ready/rejected status
- [x] Private MinIO object
- [x] Checksum dan file version
- [ ] Detail, preview/thumbnail bila aman
- [x] Authorized download
- [x] Soft delete/restore
- [ ] Retention/legal hold design

## Search dan discovery

- [x] Search keyword metadata
- [x] Filter project/company/vendor/type
- [x] Filter date/year/amount/status
- [x] Sorting dan pagination
- [x] Authorization-aware query
- [ ] Saved search/filter
- [ ] OCR/full-text extraction (fase lanjutan)

## Verification dan matching

- [x] Generic document relation
- [x] PO-invoice-receipt relation
- [x] Configurable matching rules
- [x] Discrepancy reasons
- [x] Review/approve/reject workflow
- [x] Evidence dan reviewer comments
- [x] Verification audit trail

## Bundling

- [x] Bundle dari selected documents
- [x] Bundle dari filter/search
- [x] Queue job dan progress status
- [x] ZIP manifest
- [x] Private bundle object dan expiry
- [x] Authorized download
- [x] Cleanup job
- [x] Bundle audit events

## Quality dan operations

## Dashboard dan branding

- [x] Landing page publik khusus manajemen dokumen Basilindo
- [x] Dashboard Finance dengan metrik dokumen, review, dan aktivitas audit
- [x] Branding DewaKoding dibersihkan dari aplikasi File Organizer
- [x] Landing page publik responsif dengan CTA, focus state, dan copy domain Basilindo
- [x] Background halaman admin setelah login konsisten dengan halaman Basilindo Access
- [x] Kontras state aktif navbar publik dan focus-visible state

- [x] Unit/feature/integration tests
- [x] Authorization negative tests
- [x] Upload/download security tests
- [ ] Queue retry/idempotency tests
- [ ] Backup/restore runbook
- [ ] Logging tanpa data sensitif
- [ ] Monitoring queue/storage/database
- [ ] Release checklist dan rollback plan

## Administrasi akun sistem dan kebersihan schema

- [x] Consumer dan tabel legacy project-management dipensiunkan dari File Organizer

- [x] Akun `admin@example.com` dibuat sebagai `super_admin` dan ditandai sebagai system account
- [x] System account tidak dapat dihapus melalui policy, model, atau database trigger
- [x] Tabel `saved_searches` yang belum memiliki consumer dihapus melalui migration reversible
- [x] Audit tabel database terhadap consumer aplikasi dan konfigurasi framework
- [x] Orphan model `SavedSearch` dihapus setelah tabel `saved_searches` dipastikan tidak ada

## Administrasi SSO

- [x] Application access dapat diedit, dihapus, dan diaktif/nonaktifkan
- [x] Unique constraint user–application–role dan identifier record untuk Filament
- [x] Audit metadata perubahan application access
