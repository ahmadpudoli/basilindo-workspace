# Feature Checklist

Status: `[ ]` belum mulai, `[-]` berjalan, `[x]` selesai, `[!]` blocked/keputusan dibutuhkan.

## Platform

- [x] Basilindo Workspace modular monolith berbasis `app/workspace`
- [x] Migrasi penuh runtime dari legacy app ke `app/workspace`
- [ ] Menghapus folder legacy `app/file-organizer` dan `app/project-management` setelah smoke test Workspace
- [x] Boundary Core/Identity, Company, CRM, Project Management, dan Document Management diimplementasikan
- [x] Shared core (`core/`) untuk model, migration, factory, dan library general
- [x] Seluruh migration aktif dipusatkan di `core/database/migrations`
- [x] Ownership data lima modul Workspace terdokumentasi
- [x] PostgreSQL terpusat dengan prefix tabel `core_` dan `ws_`
- [x] Laravel 12 + Filament 4 baseline
- [x] PostgreSQL connection dan migrations
- [x] Redis cache/queue/lock/rate limit
- [x] MinIO private S3 disk dan bucket lifecycle
- [x] Docker Compose health checks untuk PostgreSQL, Redis, dan MinIO
- [x] `.env.example` tanpa secret
- [x] Local development command Windows-safe (Pail dipisah dari `composer run dev`)
- [x] CI test/build baseline
- [x] Single deployment target `http://localhost:8080`

## Identity dan akses

- [x] Local development login
- [x] User/company/project scope
- [x] Role dan permission matrix
- [x] Policy untuk list/view/upload/download/edit/delete
- [x] Policy company scope untuk Account, Lead, Opportunity, Activity, Ticket, dan Document
- [x] Audit login dan permission changes
- [x] Login lokal Workspace sebagai satu-satunya metode authentication runtime
- [x] Satu session dan satu deployment untuk seluruh modul
- [x] Pembuatan user Core dari Workspace Users
- [x] Application access list memakai default sort eksplisit untuk pivot composite key
- [x] OIDC userinfo membaca entitlement berdasarkan client dari API guard
- [x] Pemulihan aman untuk authorization code SSO yang expired atau sudah digunakan

## Catalog dan metadata

- [x] Company management (Group, anak perusahaan, client/vendor sebagai role)
- [x] Project management
- [x] Master perusahaan menyatukan internal, client, vendor, dan partner
- [x] Assignment multi-company pada user dengan scope role
- [x] Project list dan bulk actions kompatibel dengan Filament 4
- [x] Workspace Project Management: project, member, ticket, status, priority, dan assignee
- [x] Project creation dibatasi pada company scope user
- [x] Detail project menampilkan members, tickets, dan dokumen terkait
- [x] Vendor management (legacy view; canonical entity tetap `companies`)
- [x] Document type management
- [x] Required metadata per document type
- [x] Tags dan reference normalization
- [x] Duplicate detection dasar
- [x] CRM schema baseline untuk account, contact, lead, opportunity, dan activity
- [x] CRM account/customer resource dengan company scope
- [x] CRM contact management pada account detail
- [x] CRM lead resource dengan company scope
- [x] CRM opportunity resource dengan pipeline stage
- [x] CRM activity resource dengan company scope
- [x] Konversi opportunity menjadi project dan assignment owner sebagai member
- [x] Activity CRM dengan due date dan completion state
- [x] Account detail menampilkan contact, opportunity, activity, dan dokumen terkait
- [x] CRM pipeline workflow dan dashboard ringkas pada Workspace
- [x] CRM hub pada navigation dengan empty state saat company assignment belum tersedia
- [x] Project dibuat dari opportunity CRM
- [x] Dokumen dapat dikaitkan dengan account/opportunity CRM
- [x] Dokumen dan ticket dapat diakses dari detail project
- [x] Account detail menyediakan shortcut membuat opportunity/activity dan upload dokumen

## Document lifecycle

- [x] Secure upload validation
- [x] Quarantine/ready/rejected status
- [x] Private MinIO object
- [x] Checksum dan file version
- [x] Detail dan preview inline untuk PDF/image; thumbnail belum diaktifkan
- [x] Authorized download
- [x] Soft delete/restore
- [x] Retention/legal hold design

## Search dan discovery

- [x] Search keyword metadata
- [x] Filter project/company/vendor/type
- [x] Filter date/year/amount/status
- [x] Sorting dan pagination
- [x] Authorization-aware query
- [x] Saved search/filter
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

- [x] Stabilitas lifecycle Filament pada assignment perusahaan dan bulk selection
- [x] Tooltip perbedaan role aplikasi dan peran user di perusahaan
- [x] Project Management memiliki assignment perusahaan dan launcher aplikasi yang konsisten dengan File Organizer
- [x] Hanya satu akun sistem dengan role `super_admin`

## Dashboard dan branding

- [x] Project Management memakai branding Basilindo dan landing page publik tanpa DewaKoding
- [x] Project Management memakai layout panel konsisten dengan File Organizer
- [x] Project Management memiliki konsep visual berbeda: gradien terang dan tata letak weekly pulse
- [x] Project Management login melalui SSO dan logout lokal kembali ke landing page
- [x] Project Management notification memakai koneksi shared `core` dan tabel `core_notifications`
- [x] Landing page publik khusus manajemen dokumen Basilindo
- [x] Dashboard Finance dengan metrik dokumen, review, dan aktivitas audit
- [x] Dashboard Workspace dengan ringkasan CRM account, opportunity, dan ticket sesuai scope user
- [x] Referensi model lintas boundary memakai Core Models untuk Company, Project, dan User
- [x] Form dokumen membatasi pilihan company, project, CRM account, dan opportunity sesuai scope user
- [x] Role matrix awal untuk Core, CRM, Project Management, dan Document Management tersedia di seeder
- [x] Role matrix CRM/Project diprovision secara additive pada existing install
- [x] Test isolation mencakup database domain Workspace dan verifikasi SSO system account
- [x] Authorization test CRM company scope dan Project ticket membership
- [x] SSO application access projection dengan client credentials dan payload minim
- [x] Migration dan command sinkronisasi projection access lokal
- [x] Scheduler sinkronisasi projection access dengan lock overlap
- [x] Safe transition state ketika tabel projection belum dimigrasikan
- [x] GitHub Actions CI untuk migration, test suite, dan frontend build
- [x] Asset frontend Workspace berhasil dibuild untuk production
- [x] Branding DewaKoding dibersihkan dari aplikasi File Organizer
- [x] Landing page publik responsif dengan CTA, focus state, dan copy domain Basilindo
- [x] Background halaman admin setelah login konsisten dengan halaman Basilindo Access
- [x] Kontras state aktif navbar publik dan focus-visible state

- [x] Unit/feature/integration tests (31 test, 94 assertion)
- [x] Authorization negative tests
- [x] Upload/download/preview security tests
- [x] Queue retry/idempotency tests
- [x] Backup/restore runbook
- [x] Logging tanpa data sensitif
- [x] Readiness monitoring queue/storage/database dan failure response 503
- [x] Release checklist dan rollback plan

## Administrasi akun sistem dan kebersihan schema

- [x] Consumer dan tabel legacy project-management dipensiunkan dari File Organizer

- [x] Akun `admin@example.com` dibuat sebagai `super_admin` dan ditandai sebagai system account
- [x] System account tidak dapat dihapus melalui policy, model, atau database trigger
- [x] Tabel `saved_searches` yang belum memiliki consumer dihapus melalui migration reversible
- [x] Audit tabel database terhadap consumer aplikasi dan konfigurasi framework
- [x] Orphan model `SavedSearch` dihapus setelah tabel `saved_searches` dipastikan tidak ada

## Administrasi SSO

- [x] Project Management memakai port terisolasi: Artisan `8002` dan Vite `5175`.
- [x] Port lokal terisolasi: Project SSO `8000`, File Organizer `8001`, Project Management `8002`.
- [x] Endpoint OIDC, sesi login, dan URL lokal SSO mengarah ke `localhost:8000`.

- [x] Application access dapat diedit, dihapus, dan diaktif/nonaktifkan
- [x] Unique constraint user–application–role dan identifier record untuk Filament
- [x] Audit metadata perubahan application access
