# Feature Checklist

Status: `[ ]` belum mulai, `[-]` berjalan, `[x]` selesai, `[!]` blocked/keputusan dibutuhkan.

## Platform

- [x] Basilindo Workspace modular monolith berbasis `workspace`
- [x] Migrasi penuh fitur bisnis yang dipertahankan dari legacy app ke `workspace`
- [x] Menghapus folder legacy `app/file-organizer` dan `app/project-management` setelah parity dan smoke test Workspace
- [x] Boundary Core/Identity, Company, CRM, Project Management, dan Document Management diimplementasikan
- [x] Shared core (`core/`) untuk model, migration, factory, dan library general
- [x] Seluruh migration aktif dipusatkan di `core/database/migrations`
- [x] Ownership data lima modul Workspace terdokumentasi
- [x] PostgreSQL terpusat dengan prefix tabel `core_` dan `ws_`
- [x] Laravel 12 modular monolith baseline
- [x] Keputusan UI: Blade + Bootstrap dengan Livewire selektif; Filament dilarang
- [x] Boundary platform Core dan modul Company, CRM, Project Management, Document Management, serta placeholder HRIS
- [x] Namespace runtime, policy, service, job, command, dan controller dipisahkan ke boundary `app/Modules/`
- [x] Card modul membuka dashboard Blade masing-masing untuk Company, CRM, Project Management, dan Document Management
- [x] Document Management memiliki halaman Blade nyata untuk daftar dokumen, tipe dokumen, dan bundel
- [x] Company memiliki CRUD perusahaan berbasis Blade dengan policy dan validasi boundary modul
- [x] Detail perusahaan lengkap dengan struktur, visibilitas, dan brand asset privat di MinIO pada prefix `app-company`
- [x] Daftar perusahaan menampilkan logo brand dengan lazy loading dan decoding asynchronous melalui endpoint privat
- [x] CRM memiliki CRUD dasar Account, Lead, Opportunity, dan Activity berbasis Blade
- [x] Project Management memiliki CRUD dasar Project, Ticket, Epic, Catatan, Status, dan Prioritas
- [x] Project Management memiliki board ticket dan timeline berbasis route Blade
- [x] Core memiliki halaman pengguna dan role dengan policy authorization dan pembuatan user tervalidasi
- [x] Core User CRUD memiliki edit, update, role assignment, dan safe archive untuk non-system account
- [x] Core Role CRUD memiliki create, edit, update, delete terproteksi, dan larangan menghapus super_admin
- [x] Core Role list memiliki filter server-side, tabel responsif, pagination, dan aturan tombol aksi
- [x] Company memiliki CRUD Vendor dengan scope company, policy, validasi, dan soft delete
- [x] Document Management memiliki form upload private storage dengan validasi file dan metadata dasar, termasuk rule database yang memakai koneksi schema pemilik tabel
- [-] Document Management memiliki halaman detail kasus verifikasi, review item, release, dan reject lifecycle (ditunda dari alur MVP sederhana)
- [x] Document Management memiliki form request bundel berbasis filter dan dispatch queued job
- [x] Document Management memiliki edit/update metadata dokumen dengan policy dan validasi
- [x] Migrasi seluruh UI Filament yang dipertahankan ke Blade/Bootstrap/Livewire selektif
- [x] PostgreSQL connection dan migrations
- [x] Redis cache/queue/lock/rate limit
- [x] MinIO private S3 disk dan bucket lifecycle
- [x] Object storage memakai bucket `app-workspace` dengan prefix aplikasi dan path dokumen berdasarkan `storage_key` perusahaan/tanggal dokumen
- [x] Temporary upload Livewire memakai folder `workspace-tmp` pada bucket MinIO private dan dihapus setelah upload permanen berhasil
- [x] Temporary upload memiliki cleanup terjadwal berdasarkan TTL environment dan berjalan setiap jam
- [x] Preview dokumen ditampilkan pada PDF viewer dalam modal dialog dengan authorization dan audit trail
- [x] Metadata pada detail dokumen ditampilkan sebagai tabel nama field dan nilai
- [x] Perusahaan memiliki `storage_key` ULID immutable untuk object storage; data lama di-backfill melalui migration
- [x] Docker Compose health checks untuk PostgreSQL, Redis, dan MinIO
- [x] `.env.example` tanpa secret
- [x] Local development command Windows-safe dengan queue worker yang berjalan terus (Pail dipisah dari `composer run dev`)
- [x] CI test/build baseline
- [x] Single deployment target `http://localhost:8080`

## Identity dan akses

- [x] Local development login
- [x] User/company/project scope
- [x] Role dan permission matrix
- [x] Policy untuk list/view/upload/download/edit/delete
- [x] Policy company scope untuk Account, Lead, Opportunity, Activity, Ticket, dan Document
- [x] Document Management memakai assignment perusahaan eksplisit sebagai scope, termasuk untuk super admin yang memiliki assignment
- [x] Daftar dokumen memiliki filter perusahaan operasional yang dibatasi scope akses pengguna
- [x] Panel filter daftar dokumen dan modul terkait memakai kontrol seragam serta grup tombol Reset/Terapkan
- [x] Daftar dokumen memiliki sorting server-side pada kolom judul, perusahaan, jenis, tanggal, dan status yang terhubung dengan filter serta pagination
- [x] Daftar dokumen memiliki sorting server-side pada kolom judul, perusahaan, jenis, tanggal, dan status yang terhubung dengan filter serta pagination
- [x] Audit login dan permission changes
- [x] Audit trail Core menyimpan konteks request, device, snapshot JSON, filter super admin, dan history edit dokumen
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
- [x] Visibilitas perusahaan kosong berarti global; visibilitas terisi dibatasi pada perusahaan operasional terpilih dan tetap tunduk pada scope user
- [x] Assignment multi-company pada user dengan scope role
- [ ] Project list dan bulk actions tersedia pada UI Laravel non-Filament
- [-] Workspace Project Management: project, member, ticket, status, priority, assignee, epic, notes, comments, board, timeline, dan reporting
- [x] Workspace Project Management domain baseline: Epic, Project Notes, Ticket Comments, dan Notification model/migration
- [x] Workspace Project Management UI parity: board, timeline, leaderboard, user contributions, dan notification resource
- [x] Project creation dibatasi pada company scope user
- [x] Detail project menampilkan members, tickets, dan dokumen terkait
- [x] Vendor management memakai master `Company` berperan `vendor` sebagai identitas vendor
- [x] Vendor management memiliki CRUD Blade, detail, scope multi-perusahaan operasional, filter multiselect, sorting, pagination, dan soft archive
- [x] Validasi client-side dan Form Request server-side pada form Company dan Vendor
- [x] Document type management
- [x] Daftar tipe dokumen memakai pagination, search, filter, sorting, dan limit server-side yang saling mempertahankan parameter
- [x] Form tambah/edit tipe dokumen memiliki validasi client-side terpusat selain validasi server-side
- [x] Typed and optional metadata per document type
- [x] Metadata Numeric integer dan Numeric Decimal dibedakan, dinormalisasi, divalidasi server, dan disanitasi pada tampilan
- [x] Metadata Numeric dan Numeric Decimal disanitasi saat input dan tetap divalidasi di server
- [x] Metadata angka menggunakan format tampilan Indonesia dan disimpan dalam format canonical di server
- [x] Nominal menggunakan format tampilan Indonesia dengan desimal dan dinormalisasi sebelum disimpan
- [x] Locale/format angka dan timezone tampilan dikonfigurasi melalui environment; timezone storage ditetapkan UTC
- [x] Jenis dokumen yang sudah digunakan menjaga scope perusahaan dan struktur metadata tetap immutable
- [x] Form dokumen reactive terhadap perusahaan internal dan jenis dokumen, dengan panel metadata dinamis (Livewire selektif)
- [x] Form dokumen memiliki tanggal wajib, client/vendor opsional, mata uang opsional berbentuk dropdown IDR/USD/SGD, dan deskripsi WYSIWYG
- [x] Pesan validasi input tersedia dalam Bahasa Indonesia dan menjelaskan tindakan perbaikan
- [x] Form dokumen memiliki validation state server-side yang konsisten dengan panel Livewire
- [x] Form upload dokumen memiliki pasangan file-preview dan deskripsi Summernote, dengan judul setelah pasangan tersebut
- [x] Pilihan proyek pada dokumen mengikuti perusahaan internal dan relasinya divalidasi di server
- [x] Pilihan jenis dokumen pada dokumen mengikuti perusahaan internal dan relasinya divalidasi di server
- [x] Form dokumen memiliki tanggal wajib, mata uang opsional berbentuk dropdown IDR/USD/SGD, dan deskripsi WYSIWYG
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
- [x] Upload baru langsung berstatus ready; quarantine/rejected tetap dibaca untuk kompatibilitas data lama
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
- [x] Panel audit subject memakai permission `view_audit_trail` dengan subject scope; member tidak melihat panel secara default

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

- [x] Konfigurasi PDO MySQL kompatibel dengan PHP 8.5 tanpa deprecated constant warning
- [x] Dependency audit Composer: `maatwebsite/excel` 4.x dan PhpSpreadsheet 5.x kompatibel dengan PHP 8.5 tanpa advisory Composer
- [ ] Stabilitas lifecycle assignment perusahaan dan bulk selection pada UI baru
- [x] Tooltip perbedaan role aplikasi dan peran user di perusahaan
- [x] Project Management memiliki assignment perusahaan dan launcher aplikasi yang konsisten dengan File Organizer
- [x] Hanya satu akun sistem dengan role `super_admin`
- [x] Master data Basilindo, role/permission baseline, dan akun `admin@basilindo.co.id` diprovision melalui migration incremental

## Dashboard dan branding

- [x] `pos-laravel-admin` diaudit dan ditetapkan sebagai referensi UI/UX; template bukan runtime/domain source
- [x] Shared Workspace UI shell mengikuti acuan template: topbar, sidebar modul, account menu, theme toggle, responsive offcanvas, cards, tables, forms, modal, badge, dan Bootstrap pagination
- [x] Sidebar desktop dapat diminimize/dibuka kembali dan menyimpan preferensi pengguna
- [x] Header memiliki module launcher/dropdown bergaya Google Workspace untuk berpindah antar modul
- [x] Seluruh input text, email, password, select, dan textarea Workspace memakai floating-label compact yang konsisten dengan template
- [x] Dashboard Company, CRM, Project Management, dan Document Management memakai pola page header, action, summary cards, dan table template
- [x] Ringkasan Company memiliki hero dashboard, metrik berbasis scope, daftar perusahaan informatif, dan akses cepat
- [x] Ringkasan Document Management memiliki hero modern, card statistik animatif, dan daftar dokumen terbaru yang responsif
- [x] Ringkasan Document Management memiliki hero modern, card statistik animatif, dan daftar dokumen terbaru yang responsif

- [x] Project Management memakai branding Basilindo dan landing page publik tanpa DewaKoding
- [x] Project Management memakai layout panel konsisten dengan File Organizer
- [x] Project Management memiliki konsep visual berbeda: gradien terang dan tata letak weekly pulse
- [x] Project Management login lokal pada Workspace; SSO legacy tidak dipindahkan
- [x] Project Management notification memakai koneksi shared `core` dan tabel `core_notifications`
- [x] Landing page publik Basilindo Workspace lintas modul dengan CTA login
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
- [x] Vite development server dan HMR dikunci pada port 5173
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

- [x] Laravel memiliki konfigurasi compiled view dan placeholder cache framework agar Composer package discovery berjalan pada instalasi baru

- [x] Laravel memiliki konfigurasi compiled view dan placeholder cache framework agar Composer package discovery berjalan pada instalasi baru

- [x] Root `resources/` legacy diverifikasi tidak digunakan dan dihapus; resource runtime tetap berada di `workspace/resources/`

- [x] Konfigurasi PDO MySQL kompatibel dengan PHP 8.5 tanpa deprecated constant warning
- [x] Dependency audit Composer: `maatwebsite/excel` 4.x dan PhpSpreadsheet 5.x kompatibel dengan PHP 8.5 tanpa advisory Composer

- [x] Konfigurasi PDO MySQL kompatibel dengan PHP 8.5 tanpa deprecated constant warning
- [!] Dependency audit Composer: `maatwebsite/excel` 3.x/PhpSpreadsheet 1.30.0 masih memiliki advisory; upgrade major ke Excel 4.x memerlukan regression test import/export

## Modul Pengaturan Workspace

- [x] Modul Pengaturan hanya terlihat untuk `super_admin` atau permission `access_settings`
- [x] Pengaturan modul aktif global dengan dashboard/launcher filtering
- [x] Pengguna dapat dikelola dengan role dan direct permission tambahan
- [x] Pengguna dapat diberi scope akses ke beberapa perusahaan internal
- [x] Relasi employee satu-perusahaan melalui pilihan karyawan pada form user, dengan scope perusahaan

## Modul HRIS

- [x] Dokumentasi requirement dan acceptance criteria modul HRIS
- [x] Master karyawan dengan company scope dan relasi akun user
- [x] Parameter HRIS per perusahaan/periode
- [x] Pengajuan cuti dan sakit fase pertama
- [x] Form pengajuan cuti/sakit memiliki validasi client-side terstruktur dan validasi server-side
- [x] Input tanggal Workspace menggunakan tampilan global `dd/mm/yyyy` dengan nilai server ISO
- [x] Form pengajuan cuti/sakit memiliki validasi client-side terstruktur dan validasi server-side
- [x] Kuota cuti tahunan configurable dengan default 12 hari
- [x] Validasi overlap tanggal pengajuan aktif
- [x] Pengajuan cuti/sakit lintas tahun ditolak dan harus dibuat sebagai pengajuan terpisah
- [x] Detail tanggal cuti/sakit disimpan sebagai multiple row dan tidak bergantung pada `start_date`/`end_date`
- [x] Pengajuan cuti/sakit menyimpan `requester_user_id` selain relasi `employee_id`
- [x] Primary key ULID pengajuan HRIS disimpan sebagai string agar detail tanggal terhubung ke request yang benar
- [x] Status workflow cuti/sakit disederhanakan menjadi Menunggu atasan, Menunggu HR, Disetujui, dan Ditolak
- [x] Workflow revisi pengajuan cuti/sakit dengan klasifikasi substansi/administrasi, resubmit pemohon, dan approval event
- [x] Primary key ULID pengajuan HRIS disimpan sebagai string agar detail tanggal terhubung ke request yang benar
- [x] Pengajuan cuti/sakit menyimpan `requester_user_id` selain relasi `employee_id`
- [x] Tabel HRIS menggunakan koneksi/prefix `hris_` dengan migrasi rename dan backfill aman
- [x] Approval manager/HR/Finance/final dan approval event
- [ ] Kalender hari libur dan perhitungan hari kerja
- [x] Lembur dengan pembulatan, maksimum, dan post-submission policy
- [x] Reimbursement dan workflow Finance
- [x] Cash advance dan pertanggungjawaban
- [x] In-app notification
- [x] Web Push PWA subscription, service worker, queue job, dan VAPID configuration
- [x] Pengaturan HRIS dipusatkan di modul Pengaturan
- [x] Cut-off pengajuan cuti unik satu konfigurasi aktif per tahun lintas scope
- [x] Tanggal berlaku parameter HRIS ditampilkan dengan format lokal Indonesia
- [x] Warning validasi penyimpanan parameter HRIS tampil di halaman Pengaturan HRIS
- [x] Aksi arsip parameter HRIS memakai modal konfirmasi Workspace
- [x] Konflik tanggal saat submit revisi tampil sebagai validasi pada form revisi
- [x] Pesan konflik tanggal membedakan status disetujui dan masih diproses
- [x] Submit revisi mencatat approval event tanpa error namespace model
- [x] Tanggal pengajuan awal tidak berubah setelah submit revisi
- [x] Notifikasi approver membuka halaman detail pada child Persetujuan HRIS
- [x] Detail approval terpisah dari detail pemohon dan menyediakan tindakan workflow
- [x] Blok tindakan approval memiliki judul dan penjelasan yang jelas
- [x] Panduan jenis revisi tersedia dalam panel collapsible pada detail approval
- [x] Tolak dan Minta revisi pada detail approval menggunakan modal dan kembali ke daftar
- [x] Pagination, filter, edit, dan arsip parameter HRIS
- [x] Workflow approval HRIS memiliki soft archive dan dapat diaktifkan kembali tanpa duplikasi constraint
- [x] Workflow approval HRIS memiliki soft archive dan dapat diaktifkan kembali tanpa duplikasi constraint
- [x] Daftar karyawan dengan filter perusahaan multiple select, pagination, dan aksi ubah/hapus/arsip
- [x] Relasi akun Core User dengan karyawan (Nama dan NIP)
- [x] Permission HRIS dikelompokkan pada bagian HRIS di form role dan user, termasuk role hris_admin dan hris_member
- [x] Master role dan permission HRIS diprovision melalui migration data idempotent, termasuk approval dan permission ringkasan
- [x] Dashboard HRIS dapat diakses dengan `access_hris` dan menghormati permission granular pada data/tombol
- [x] Sidebar menampilkan Ringkasan HRIS berbasis `access_hris` dengan mode ringkasan pengajuan pribadi
- [x] Daftar cuti memiliki resume tahunan, filter, pagination, detail, dan timeline approval
- [x] Badge status pada detail cuti mengikuti lebar konten label
- [x] Form revisi cuti memakai struktur layout yang sama dengan form create
- [x] Pengajuan cuti menolak tahun yang belum memiliki konfigurasi kuota
- [x] Pesan error form HRIS tidak menampilkan panel global duplikat
- [x] Cut-off pengajuan cuti wajib dikonfigurasi per tahun dan ditegakkan server-side
- [x] Tabel parameter HRIS menampilkan nama bisnis dan kode teknis parameter
- [x] Field nilai parameter HRIS menyesuaikan tipe parameter yang dipilih
- [x] Setiap parameter HRIS memiliki deskripsi panduan pengguna
- [x] Maksimum lembur bersifat opsional tanpa batas jika belum dikonfigurasi
- [x] Filter daftar cuti mendukung opsi Semua tahun
- [x] Approval inbox HRIS memiliki permission khusus dan cakupan bawahan langsung
- [x] Approval inbox HRIS menggunakan satu tabel terpadu dengan modal alasan untuk tolak dan minta revisi
- [x] Aksi Setujui pada approval HRIS menggunakan modal konfirmasi sebelum submit
- [x] Popup konfirmasi Setujui menyediakan catatan persetujuan opsional
- [x] Approval inbox HRIS memiliki filter, sorting, dan pagination dengan state query string
- [x] Header Workspace memiliki dropdown notifikasi unread, halaman daftar, detail, dan link target
- [x] Detail notifikasi memiliki kartu informasi modern dan tombol kembali yang konsisten
- [x] Tooltip jenis revisi di popup approval diinisialisasi saat modal ditampilkan
- [x] Popup minta revisi menampilkan panduan jenis revisi secara langsung
- [x] Sidebar HRIS mendukung menu bertingkat untuk Biodata, Kehadiran, dan Claim
- [x] Biodata karyawan menampilkan atasan dan treeview bawahan bertingkat
- [x] Biodata personal mengambil data berdasarkan relasi akun tanpa scope daftar karyawan/company
- [x] Dashboard HRIS dapat diakses dengan `access_hris` dan menghormati permission granular pada data/tombol
- [x] Daftar karyawan dengan filter perusahaan multiple select, pagination, dan aksi ubah/hapus/arsip
- [x] Pagination, filter, edit, dan arsip parameter HRIS
- [x] Pengaturan HRIS dipusatkan di modul Pengaturan

- [x] Consumer dan tabel legacy project-management dipensiunkan dari File Organizer

- [x] Akun `admin@example.com` dibuat sebagai `super_admin` dan ditandai sebagai system account
- [x] System account tidak dapat dihapus melalui policy, model, atau database trigger
- [x] Tabel `saved_searches` yang belum memiliki consumer dihapus melalui migration reversible
- [x] Audit tabel database terhadap consumer aplikasi dan konfigurasi framework
- [x] Orphan model `SavedSearch` dihapus setelah tabel `saved_searches` dipastikan tidak ada

## Administrasi SSO

## Parity migrasi Blade/Bootstrap

- [x] CRM Account, Contact, Lead, Opportunity, dan Activity memiliki CRUD dengan field relasi yang sesuai model.
- [x] Project Management memiliki CRUD komentar ticket dan notifikasi selain project, ticket, epic, catatan, status, dan prioritas.
- [x] Project Management memiliki halaman kontribusi pengguna dan leaderboard.
- [x] Document Type memiliki CRUD, definisi metadata JSON, policy, dan proteksi penghapusan ketika masih dipakai.
- [x] Project memiliki halaman detail dengan daftar/tambah/hapus anggota, ticket, epic, dan catatan.
- [x] Ticket memiliki status, prioritas, assignee multi-pilih, dan sinkronisasi assignment.
- [x] Ticket memiliki import CSV tervalidasi dan export CSV dengan authorization project scope.
- [x] Komponen Livewire selektif untuk memilih tipe dokumen dan merender metadata wajib sesuai definisi tipe.
- [x] Detail project dan account menampilkan dokumen terkait serta shortcut upload dengan scope relasi.

- [ ] Workspace menampilkan login lokal Laravel dan tidak mengarahkan pengguna ke SSO legacy.
- [x] Project Management mempertahankan board, timeline, epic, notes, comments,
  status/priority, import/export ticket template, dashboard widgets, dan
  relation managers di Workspace.

- [x] Project Management memakai port terisolasi: Artisan `8002` dan Vite `5175`.
- [x] Port lokal terisolasi: Project SSO `8000`, File Organizer `8001`, Project Management `8002`.
- [x] Endpoint OIDC, sesi login, dan URL lokal SSO mengarah ke `localhost:8000`.

- [x] Application access dapat diedit, dihapus, dan diaktif/nonaktifkan
- [x] Unique constraint user–application–role dan identifier record untuk Filament
- [x] Audit metadata perubahan application access
- [x] Lokasi runtime Workspace dicatat konsisten pada `workspace/`; CI dan dokumentasi aktif tidak lagi menunjuk `app/workspace/`
- [x] Development server Laravel tidak menetapkan `PHP_CLI_SERVER_WORKERS` saat memakai mode reload
- [x] Form edit dokumen mengikuti susunan form tambah dan menampilkan panel metadata tambahan berdasarkan tipe dokumen
- [x] Tipe dokumen dapat dikaitkan dengan beberapa perusahaan melalui searchable multi-select dan pivot relation
- [x] Validasi perusahaan tipe dokumen memakai koneksi Core dan mendukung tipe global
- [x] Teks dropdown perusahaan tipe dokumen ter-align vertikal tanpa menambah tinggi input
- [x] Floating label dan animasi fokus diterapkan secara global pada field form Workspace
- [x] Tinggi kontrol input satu baris Workspace diseragamkan secara global menjadi 40px
- [x] Jarak vertikal antar field form Workspace diseragamkan secara global menjadi compact (`.75rem`)
- [x] Form perusahaan memiliki pasangan field dan aturan dependensi peran/parent berdasarkan klasifikasi
- [x] Searchable dropdown tidak terpotong oleh overflow card pada form Workspace
- [x] Perusahaan yang memiliki relasi vendor mengunci peran vendor, jenis entitas, klasifikasi, dan status
- [x] Permission role dikelompokkan per modul dalam panel collapsible dengan kondisi awal tertutup
- [x] Daftar pengguna memiliki filter nama/email, role, status, pagination 10/25/50/100, dan aturan tombol berbasis permission
- [x] Daftar pengguna memiliki sorting server-side pada kolom nama, email, dan status
- [x] Feedback login kredensial invalid tampil inline dekat form dengan pesan generik yang jelas
- [x] Halaman error HTTP utama memiliki template modern, responsif, dan animasi CSS ringan
- [x] Logo Basilindo digunakan pada identitas aplikasi dan favicon multi-resolusi tersedia
- [x] Feedback login kredensial invalid tampil inline dekat form dengan pesan generik yang jelas
- [x] Akses tingkat modul (`access_company`, `access_crm`, `access_project_management`, `access_documents`) menyaring kartu, launcher, sidebar, dan halaman ringkasan
- [x] Form tambah/edit dokumen memiliki pasangan field, counterparty searchable, filter tipe dokumen berdasarkan perusahaan, loading metadata, nominal lokal, dan tag chip
