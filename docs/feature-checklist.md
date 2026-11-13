# Feature Checklist
- [x] Daftar reimbursement dan cash advance menampilkan aksi Detail untuk semua pengajuan yang diizinkan policy, termasuk status menunggu persetujuan.
- [x] Reimbursement yang telah disetujui Finance menyediakan tombol cetak di daftar/detail dengan kop nama perusahaan, status workflow terkini, dan rincian pengajuan.
- [x] Dokumen cetak reimbursement menampilkan tanggal pengajuan awal, identitas/waktu persetujuan Atasan dan Finance, serta tanda persetujuan hijau pada blok tanda tangan.
- [x] Ringkasan cetak tidak mengulang rincian persetujuan dan footer menyediakan blok tanda tangan pemohon.
- [x] Nama perusahaan tidak diulang dalam ringkasan dokumen cetak karena sudah tampil sebagai kop.
- [x] Kolom tanggal pengajuan awal tersedia melalui migration incremental dan pengajuan lama non-draft di-backfill dari tanggal pembuatan.
- [x] Lebar kolom kategori daftar klaim dibatasi dan badge kategori membungkus dalam batas kolom.
- [x] Lebar kolom Status klaim dibatasi dan ruang kolom Aksi diperbesar untuk menampung tombol.
- [x] Tombol aksi baris draft klaim ditampilkan satu baris pada kolom Aksi seperempat lebar tabel dengan lebar minimum 360 px.
- [x] Aksi draft dinamis berada dalam container tombol yang sama agar tetap sebaris dan memiliki ruang vertikal.
- [x] Halaman cetak klaim menggunakan lembar A4; gambar proporsional dengan batas tinggi 90 mm dan dapat berbagi halaman, sedangkan tiap halaman PDF dirender lokal sebagai lembar A4 terpisah tanpa PDF viewer.
- [x] Lembar PDF menyesuaikan tinggi area cetak A4 setelah margin sehingga tiap halaman PDF tidak menimbulkan halaman kosong akibat overflow.
- [x] Lembar PDF dicetak sebagai elemen mandiri di luar wadah lampiran A4 agar tidak menambah halaman kosong dari overflow wadah induk.
- [x] Lampiran gambar tidak dipaksa ke halaman baru; beberapa lampiran dapat mengisi halaman A4 yang sama secara proporsional.
- [x] Gambar-gambar berurutan ditampilkan bersama pada satu lembar preview lampiran, mengikuti susunan hasil cetak.
- [x] Approval Atasan dan Finance dapat meminta revisi claim dengan tipe substansi/administrasi; pemohon dapat memperbarui dan mengirim ulang ke tahap sesuai tipe, dengan snapshot historis serta bukti lama tetap terjaga.
- [x] Riwayat revisi claim memakai permission granular `view_claim_audit_trail`, policy subject scope, dan pemeriksaan controller/Blade.
- [x] Label status claim menampilkan “Menunggu persetujuan atasan” dan “Menunggu persetujuan Finance” untuk tahap workflow terkait.
- [x] Pilih semua pada grup permission dan tiap subgrup memperbarui checkbox, penghitung jumlah pilihan, dan status terpilih sebagian secara konsisten.
- [x] Filter Role di daftar pengguna memakai floating label; tombol Reset dan Terapkan sejajar dengan field filter.
- [x] Form reimbursement menampilkan pengingat bergaya peringatan di bawah accordion informasi karyawan agar bukti fisik diserahkan kepada Finance pada akhir bulan pengajuan.
- [x] Halaman detail claim menjaga submenu Reimbursement & Cash Advance aktif dan grup Manajemen Claim terbuka.
- [x] Panel kiri detail approval klaim menampilkan konteks ringkas tanpa kartu terpisah per field; bukti nota dibuka melalui popup dua panel “Preview Bukti Dokumen”.
- [x] Filter daftar reimbursement dan cash advance merapat dalam satu baris pada desktop lebar tanpa ruang kosong setelah field jumlah baris.
- [x] Panel konteks detail reimbursement menampilkan nama client sesuai project/opportunity dan kontak opportunity beserta email jika tersedia.
- [x] Referensi opportunity reimbursement menerima UUID CRM maksimal 36 karakter; validasi client/server dan kolom database konsisten.
- [x] Halaman detail reimbursement memakai ringkasan dan tabel compact, menampilkan tanggal kegiatan, serta menyediakan Preview Bukti Dokumen dengan dialog verifikasi dua panel.
- [x] Dialog preview bukti reimbursement pada form edit menampilkan detail rincian biaya di kiri dan gambar/PDF di kanan, serta menjadi susunan vertikal pada layar kecil.
- [x] Daftar reimbursement tidak menampilkan panel draft duplikat; aksi Detail, Edit draft, dan Hapus draft tetap tersedia pada baris draft. Pengiriman dari edit draft menampilkan konfirmasi sesudah validasi client-side lolos.
- [x] Reimbursement mewajibkan tanggal nota dan bukti per rincian; file bisa dipilih dari perangkat atau kamera, diunggah per file ke MinIO private, dan temporary object dibersihkan pada TTL 30 menit.
- [x] Tombol Ambil foto membuka kamera browser dengan pratinjau langsung, fallback kamera/file native untuk konteks non-secure, thumbnail foto, ikon file, dan aksi hapus lampiran yang tetap divalidasi wajib pada client/server.
- [x] Bukti nota terlampir menyediakan tombol Preview di samping Hapus dan membuka pratinjau gambar/PDF dalam modal.
- [x] Tanggal nota wajib diisi pada setiap rincian reimbursement dan divalidasi pada client serta server.
- [x] Textarea Keterangan umum dan Deskripsi reimbursement compact dengan tinggi awal tepat dua baris dan tetap dapat diperbesar manual.
- [x] Form reimbursement memvalidasi Simpan draft dan Kirim pengajuan di client sebelum submit, serta memvalidasi ulang rincian dan referensi kondisional di server.
- [x] Submit draft mempertahankan nilai tombol aksi setelah unggah lampiran, menampilkan popup loading, dan daftar draft menyediakan detail, edit, serta hapus dengan authorization pemohon.
- [x] Dropdown Project menggunakan susunan pencarian tetap dan scrollbar tabel yang sama dengan dropdown Opportunity.
- [x] Kartu opsi Keperluan reimbursement dibuat lebih compact dengan padding, ikon, tipografi, dan jarak yang proporsional.
- [x] Header accordion informasi reimbursement memakai ikon karyawan yang relevan dan ukuran compact.
- [x] Dropdown opportunity reimbursement menjaga area pencarian tetap terlihat dan membatasi scrollbar vertikal/horizontal pada tabel hasil.
- [x] Accordion informasi karyawan dan alur persetujuan berada di kolom kiri setelah Nomor PO, dengan gradasi lembut pada header saat tertutup.

- [x] Form reimbursement mengatur kolom kiri untuk Informasi pengajuan, Keperluan, Nomor PO, dan accordion informasi; kolom kanan untuk Tujuan/keterangan di atas Rincian biaya, tanpa ruang kosong akibat tinggi baris yang dipaksa sama.

- [x] Informasi karyawan, atasan, Finance, dan approver final pada form reimbursement dikelompokkan dalam satu panel accordion.

- [x] Input nominal reimbursement menerima angka maksimal dua desimal, menampilkan format `id-ID`, dan menormalisasi nilai lokal pada server; referensi nota berlabel opsional dengan contoh nomor nota.

- [x] Hak akses Pengaturan HRIS dipisah per submenu (Parameter, Workflow Approval, Kategori biaya) dan per aksi lihat/tambah/ubah/arsip; Master penomoran dipisah untuk lihat/tambah/ubah.

- [x] Pengajuan/revisi lembur External / Project memiliki dropdown Project opsional dengan scope perusahaan dan akses project serta validasi server
- [x] Form pengajuan lembur memakai susunan dua kolom dan card/footer yang konsisten dengan form cuti/sakit
- [x] Form pengajuan lembur tidak menampilkan field visual Pemohon; employee_id tetap dikirim secara tersembunyi
- [x] Form pengajuan cuti/sakit menyamakan layout jenis pengajuan dan nomor pengajuan dengan form lembur serta memakai master penomoran perusahaan
- [x] Form pengajuan cuti/sakit menyamakan layout jenis pengajuan dan nomor pengajuan dengan form lembur serta memakai master penomoran perusahaan

- [x] Pengajuan/revisi lembur External / Project memiliki dropdown Project opsional dengan scope perusahaan dan akses project serta validasi server

- [x] Form tambah/edit dan filter master penomoran Core memakai floating label global; bantuan pola terpisah di bawah input format
- [x] Filter master penomoran Core mendukung penyaringan berdasarkan jenis form dan multi-select perusahaan/jenis form
- [x] Filter master penomoran Core mendukung penyaringan berdasarkan jenis form dan multi-select perusahaan/jenis form
- [x] Form tambah master penomoran Core memiliki dua kolom sama lebar dengan pasangan field sejajar
- [x] Bantuan pola penomoran berada setelah seluruh field pada form tambah dan edit, sebelum tombol simpan
- [x] Bantuan pola penomoran berada setelah seluruh field pada form tambah dan edit, sebelum tombol simpan
- [x] Form tambah master penomoran Core memiliki dua kolom sama lebar dengan pasangan field sejajar

- [x] Form tambah/edit dan filter master penomoran Core memakai floating label global; bantuan pola terpisah di bawah input format

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
- [x] Form Role mengelompokkan permission HRIS berdasarkan fitur halaman dan mengurutkan permission CRUD per resource
- [x] Panel permission Role dan Edit Pengguna compact serta expandable per fitur untuk seluruh modul
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
- [x] Definisi scheduler Workspace dipusatkan di routes/console.php dan command scheduler terdaftar eksplisit
- [x] Definisi scheduler Workspace dipusatkan di routes/console.php dan command scheduler terdaftar eksplisit
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
- [x] Audit seluruh CRUD resource bisnis aktif melalui observer terpusat dengan snapshot ter-redaksi
- [x] Audit akses preview/download dokumen penting tanpa menyimpan isi file
- [x] Audit trail Core menyimpan konteks request, device, snapshot JSON, filter super admin, dan history edit dokumen
- [x] Login lokal Workspace sebagai satu-satunya metode authentication runtime
- [x] Satu session dan satu deployment untuk seluruh modul
- [x] Pembuatan user Core dari Workspace Users
- [x] Application access list memakai default sort eksplisit untuk pivot composite key
- [x] OIDC userinfo membaca entitlement berdasarkan client dari API guard
- [x] Pemulihan aman untuk authorization code SSO yang expired atau sudah digunakan

## Catalog dan metadata

- [x] CRM minimum memiliki configurable pipeline stage, closing status, board drag-and-drop, dan konteks client/Sales/project untuk HRIS
- [x] Integration MVP memiliki koneksi Odoo PostgreSQL read-only, external reference, projection, queue sync, scheduler, riwayat sinkronisasi, dan normalisasi upsert CRM/project berbasis company mapping
- [x] Scheduler sinkronisasi Odoo hanya dispatch job dan queue worker menjalankan service sync dengan proteksi unique lock
- [x] Job asynchronous memiliki queue name eksplisit per domain dan worker development memproses seluruh queue aplikasi
- [x] Infrastruktur Web Push lintas modul berada di Core Notifications, sedangkan pemicu notifikasi tetap dimiliki modul bisnis
- [x] Infrastruktur Web Push lintas modul berada di Core Notifications, sedangkan pemicu notifikasi tetap dimiliki modul bisnis
- [x] Normalisasi CRM membedakan account perusahaan/perorangan, menghubungkan perusahaan ke `core_companies`, dan membiarkan status account kosong bila belum pasti
- [x] Daftar CRM Account memiliki filter tipe/perusahaan, pagination 10/25/50/100, sorting server-side, serta menampilkan kontak dan perusahaan terkait; status account tidak ditampilkan
- [x] Tabel CRM Account tidak menampilkan kolom status; status tetap tersedia sebagai filter
- [x] Tabel CRM Account tidak menampilkan kolom status; status tetap tersedia sebagai filter
- [x] Normalisasi account perorangan mengikuti parent company Odoo, menyimpan alamat account/perusahaan, dan mendukung banyak person pada perusahaan yang sama
- [x] Tabel hasil normalisasi CRM, project, dan company memiliki penanda sumber seragam: source system/database, external ID, integration flag, dan last synced at
- [x] Tabel hasil normalisasi CRM, project, dan company memiliki penanda sumber seragam: source system/database, external ID, integration flag, dan last synced at
- [x] Normalisasi account perorangan mengikuti parent company Odoo, menyimpan alamat account/perusahaan, dan mendukung banyak person pada perusahaan yang sama
- [x] Daftar CRM Account memiliki filter tipe/status/perusahaan, pagination 10/25/50/100, sorting server-side, serta menampilkan kontak dan perusahaan terkait
- [x] Core memiliki monitor queue/job dengan status antre, proses, berhasil, gagal, pagination, dan error log

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
- [x] Binding model Project Management pada Core menggunakan model runtime modul, bukan class legacy `App\Models\Ticket`
- [x] Pengguna dengan `view_any_project` dapat melihat detail project tanpa anggota; perubahan tetap dibatasi scope
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
- [x] Lifecycle pipeline Odoo: full sync menandai record yang hilang sebagai soft-deleted/source deleted dan pipeline menyediakan tampilan arsip
- [x] Menu Konfigurasi CRM ditempatkan di posisi paling bawah setelah resource operasional
- [x] Menu Konfigurasi CRM ditempatkan di posisi paling bawah setelah resource operasional
- [x] Menu Konfigurasi CRM ditempatkan di posisi paling bawah setelah resource operasional
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

- [x] Dokumen kontrak/personal karyawan memiliki domain dan menu khusus HRIS,
  terpisah dari Document Management umum.
- [x] Dokumen karyawan diunggah ke private MinIO/S3 dengan prefix HRIS,
  metadata checksum, MIME, ukuran, periode berlaku, policy scope, dan download
  terotorisasi.
- [x] Daftar dokumen karyawan memiliki filter, sorting server-side, pagination,
  pilihan 10/25/50/100 baris, upload, download, dan arsip soft delete.
- [x] Form dokumen karyawan memiliki UI modern dan nomor dokumen otomatis unik
  yang tampil pada daftar serta dapat digunakan untuk pencarian.

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
- [x] Pengajuan Lembur multi-rincian dengan snapshot divisi/jabatan dan durasi server-side
- [x] Setiap rincian lembur menentukan jenis hari Hari Kerja atau Hari Libur dengan validasi dan penyimpanan per baris
- [x] Form lembur memiliki validasi client-side berbasis JavaScript dan validasi server-side untuk field utama, tanggal, waktu, referensi, dan rincian
- [x] Lembur lintas tengah malam wajib dipisah menjadi rincian pada tanggal berikutnya; jam selesai tidak boleh sama atau lebih awal dari jam mulai
- [x] Halaman detail lembur compact tanpa panel alasan/konteks serta menampilkan ringkasan, realisasi, dan workflow secara hierarkis
- [x] Halaman detail lembur membagi konten menjadi area utama kiri dan sidebar riwayat workflow kanan yang responsive
- [x] Form pengajuan/revisi lembur menampilkan HR approver aktif berdasarkan perusahaan karyawan
- [x] Resubmit revisi administrasi mengirim notifikasi kembali ke HR peminta revisi dengan redaksi revisi, bukan pengajuan baru
- [x] Workflow lembur Atasan lalu HR mendukung setujui, tolak, revisi substansi/administrasi, dan kirim ulang pemohon
- [x] Master struktur organisasi Core untuk perusahaan operasional internal dengan hierarchy, scope, permission, filter, sorting, dan pagination
- [x] Data karyawan dapat dikaitkan ke divisi/unit organisasi Core
- [x] Pengajuan Lembur multi-rincian dengan snapshot divisi/jabatan dan durasi server-side
- [x] Workflow lembur Atasan lalu HR mendukung setujui, tolak, revisi substansi/administrasi, dan kirim ulang pemohon
- [x] Master struktur organisasi Core untuk perusahaan operasional internal dengan hierarchy, scope, permission, filter, sorting, dan pagination
- [x] Data karyawan dapat dikaitkan ke divisi/unit organisasi Core
- [x] Reimbursement dan workflow Finance
- [x] Cash advance dan pertanggungjawaban
- [x] Reimbursement multi-rincian dengan approval Finance, threshold final per perusahaan, dan pencatatan pembayaran terpisah
- [x] In-app notification
- [x] Nomor pengajuan otomatis HRIS tampil pada form, daftar, detail, dan notifikasi
- [x] Tautan notifikasi approval memiliki fallback aman setelah pengajuan selesai diproses
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
- [x] Empat permission summary HRIS memiliki menu dan daftar scoped perusahaan
- [x] Ringkasan Cuti dan Ringkasan Lembur memakai halaman terpisah
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
- [x] Daftar Cuti & Sakit dan Ringkasan Cuti Karyawan memiliki tombol preview lampiran pada kolom aksi melalui dialog privat
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
- [x] Approval reimbursement/cash advance pada tahap Atasan dan Finance menyediakan aksi Minta revisi dari tabel inbox
- [x] Aksi Setujui pada approval HRIS menggunakan modal konfirmasi sebelum submit
- [x] Popup konfirmasi Setujui menyediakan catatan persetujuan opsional
- [x] Approval inbox HRIS memiliki filter, sorting, dan pagination dengan state query string
- [x] Header Workspace memiliki dropdown notifikasi unread, halaman daftar, detail, dan link target
- [x] Detail notifikasi memiliki kartu informasi modern dan tombol kembali yang konsisten
- [x] Tooltip jenis revisi di popup approval diinisialisasi saat modal ditampilkan
- [x] Popup minta revisi menampilkan panduan jenis revisi secara langsung
- [x] Sidebar HRIS mendukung menu bertingkat untuk Biodata, Kehadiran, dan Claim
- [x] Sidebar HRIS menampilkan menu Pegawai berdasarkan permission `view_any_employee`
- [x] Preview bukti foto reimbursement menyediakan zoom in/out pada form dan detail tanpa mengubah preview PDF
- [x] Modal preview bukti reimbursement pada detail approval manager/Finance memakai kontrol zoom gambar yang sama
- [x] Semua modal preview gambar Workspace memakai kontrol zoom bersama; toolbar tersembunyi untuk PDF dan format non-gambar
- [x] Label menu Pegawai menggunakan nama bisnis Daftar Karyawan
- [x] Dashboard HRIS menampilkan early warning semua tahap approval (atasan, HR, Finance, final) sesuai permission dan scope inbox
- [x] Foto bukti reimbursement dari kamera dan galeri dikompresi di client sebelum upload, dengan PDF tetap utuh dan fallback ke file asli
- [x] Foto bukti reimbursement memiliki kompresi bertahap dengan target 1,5 MB; zoom 100% menyesuaikan gambar ke area preview
- [x] Detail approval reimbursement menampilkan accordion riwayat tertutup di bawah rincian biaya dan tidak menampilkan panel dokumen pendukung umum
- [x] Timeline claim pada detail persetujuan tampil tanpa permission audit tambahan dan tetap mengikuti otorisasi halaman approval
- [x] Accordion timeline approval claim memakai ikon, header compact, dan latar gradasi
- [x] Timestamp event HRIS disimpan konsisten dalam UTC dan ditampilkan dalam zona waktu aplikasi
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
- [x] Relasi pengguna-karyawan ditampilkan sebagai kolom Karyawan pada tabel utama
- [x] Feedback login kredensial invalid tampil inline dekat form dengan pesan generik yang jelas
- [x] Halaman error HTTP utama memiliki template modern, responsif, dan animasi CSS ringan
- [x] Logo Basilindo digunakan pada identitas aplikasi dan favicon multi-resolusi tersedia
- [x] Feedback login kredensial invalid tampil inline dekat form dengan pesan generik yang jelas
- [x] Akses tingkat modul (`access_company`, `access_crm`, `access_project_management`, `access_documents`) menyaring kartu, launcher, sidebar, dan halaman ringkasan
- [x] Form tambah/edit dokumen memiliki pasangan field, counterparty searchable, filter tipe dokumen berdasarkan perusahaan, loading metadata, nominal lokal, dan tag chip
- [x] Kalender Hari Libur HRIS multi-company dengan cakupan global/perusahaan, cuti bersama, dampak kuota cuti, CRUD, permission, dan audit trail
- [x] Semua panel filter GET Workspace otomatis menjadi expandable dan collapsed pada perangkat mobile
- [x] Kalender Hari Libur HRIS multi-company dengan cakupan global/perusahaan, cuti bersama, dampak kuota cuti, CRUD, permission, dan audit trail
- [x] Filter Account CRM mendukung multiple select untuk tipe, status, perusahaan pemilik, dan client/partner
- [x] Data perusahaan hasil integrasi Odoo mengunci field sumber, menyediakan edit terbatas untuk field lokal, dan tidak menyediakan aksi arsip
- [x] Data CRM Account, Contact, Opportunity, dan Project hasil integrasi Odoo menolak perubahan field sumber pada backend
- [x] Filter Account CRM mendukung multiple select untuk tipe, status, perusahaan pemilik, dan client/partner
- [x] Pengajuan lembur External / Project dapat mereferensikan project atau CRM opportunity dengan snapshot nama/status dan validasi scope
- [x] Dropdown referensi pekerjaan memiliki input pencarian tetap dan overflow vertikal/horizontal hanya pada tabel
- [x] Daftar klaim memiliki filter multi-select status, jenis, kategori, rentang tanggal default bulan berjalan, dan menampilkan kategori distinct per pengajuan
- [x] Dropdown Opportunity reimbursement identik dengan pola lembur: tabel searchable, tanpa tombol Pilih, klik baris memilih data, dan detail status/client/kontak tampil setelah pemilihan
- [x] Renderer dropdown Opportunity reimbursement ditandai siap agar tidak membuat wrapper dropdown ganda setelah perubahan pilihan
- [x] Dropdown native dan fallback Opportunity reimbursement disembunyikan/dicegah agar hanya satu komponen tampil
- [x] Dropdown native dan fallback Opportunity reimbursement disembunyikan/dicegah agar hanya satu komponen tampil
- [x] Renderer dropdown Opportunity reimbursement ditandai siap agar tidak membuat wrapper dropdown ganda setelah perubahan pilihan
- [x] Dropdown Opportunity reimbursement identik dengan pola lembur: tabel searchable, tanpa tombol Pilih, klik baris memilih data, dan detail status/client/kontak tampil setelah pemilihan
- [x] Form lembur, reimbursement/cash advance, dan cuti/sakit mendukung draft, konfirmasi submit, penghapusan draft, serta alokasi nomor master saat dikirim
- [x] Form revisi cuti/sakit mempertahankan layout dua kolom dan panel alasan revisi tidak masuk ke grid field
- [x] Form edit draft cuti/sakit merender atribut form dengan benar dan tidak menampilkan directive method sebagai teks
- [x] Edit draft cuti/sakit menampilkan dokumen pendukung tersimpan, menyediakan preview/unduh, dan hanya mewajibkan upload jika dokumen belum ada
- [x] Preview dokumen pendukung pada edit draft cuti/sakit tampil menggunakan popup dialog
- [x] Panel dokumen tersimpan pada edit draft cuti/sakit menggunakan layout compact dan responsif
- [x] Menyimpan ulang draft cuti/sakit tanpa upload baru mempertahankan dokumen pendukung lama
- [x] Penyimpanan draft cuti/sakit menerapkan validasi tanggal aktif dan hari libur yang sama dengan pengiriman pengajuan
- [x] Form revisi cuti/sakit memakai grid dua kolom yang konsisten; panel peringatan tidak mengambil posisi sebagai kolom field
- [x] Draft dan revisi cuti/sakit memakai template layout form yang sama dengan halaman create
- [x] Field kontrol form `action` tidak ikut dipersist sebagai atribut model saat resubmit revisi cuti/sakit
- [x] Daftar lembur hanya menampilkan tombol Revisi untuk status `needs_revision` dan memakai badge kuning untuk Draft
- [x] Tombol pada kolom Aksi daftar HRIS memiliki jarak horizontal yang konsisten
- [x] Filter status pengajuan lembur menggunakan dropdown multiple select dan query server-side `status[]`
- [x] Filter jenis dan status pengajuan cuti/sakit menggunakan dropdown multiple select dan query server-side
- [x] Daftar lembur memakai rentang tanggal default awal bulan sampai hari ini dan panel ringkasan jumlah serta total durasi sesuai filter
- [x] Ringkasan lembur memecah jumlah dan durasi menjadi total, disetujui, dalam proses, ditolak, dan draft
- [x] Form reimbursement/cash advance memakai layout dua kolom dan pola informasi pengajuan, rincian, karyawan, serta approver yang konsisten dengan form cuti/sakit
- [x] Form reimbursement/cash advance menghapus field Department dan memakai pilihan keperluan Internal atau External / Project beserta referensi pekerjaan berscope perusahaan
- [x] Cash advance pada form claim ditandai sebagai tahap pengembangan berikutnya dan referensi Project/Opportunity ditampilkan sebagai dropdown tabel seperti form lembur
- [x] Backend claim mengunci pengajuan baru pada Reimbursement sampai workflow Cash Advance dikembangkan
- [x] Form pengajuan reimbursement menampilkan jenis transaksi sebagai informasi tetap pada panel kiri dan menggunakan kartu pilihan keperluan yang lebih jelas serta responsif
- [x] Nomor PO pada form reimbursement hanya tampil untuk keperluan project/client; keterangan umum berada setelah Keperluan di panel kanan dan lokasi kegiatan dihapus
- [x] Informasi jenis reimbursement memakai ikon struk, bukan huruf inisial
- [x] Kategori biaya reimbursement berasal dari master HRIS dengan dropdown, migrasi backfill kategori lama, serta pengelolaan terotorisasi di Pengaturan
- [x] Permission menu Pengaturan yang belum terkatalog, Modul aktif, dan kategori reimbursement tersedia di Core & Pengaturan; permission Integrasi Odoo dikelompokkan sebagai submodul Integrasi Odoo
- [x] Dropdown referensi claim menampilkan pencarian dan tabel kolom referensi, perusahaan, client, status, serta aksi pilih seperti form lembur
- [x] Template form claim tidak memakai ekspresi Blade nested yang menyebabkan ParseError saat dibuka
- [x] Pembentukan metadata dropdown claim memakai array biasa agar kompatibel dengan Eloquent Collection
- [x] Dropdown Project/Opportunity claim mengikuti komponen lembur dan menampilkan panel detail referensi setelah dipilih
- [x] Input pencarian dropdown referensi claim tetap satu baris dan mencari nama, perusahaan, client, serta status; Project dan Opportunity memakai renderer yang sama
- [x] Dropdown Opportunity claim tetap menggunakan renderer tabel yang sama saat field awalnya tersembunyi dan baru dipilih
- [x] Opportunity claim memiliki fallback renderer agar native select tidak tampil ketika inisialisasi komponen terlambat
- [x] Opportunity claim memiliki fallback saat window load untuk menjamin komponen tabel tampil seperti form lembur
- [x] Opportunity claim memiliki fallback saat window load untuk menjamin komponen tabel tampil seperti form lembur
- [x] Opportunity claim memiliki fallback renderer agar native select tidak tampil ketika inisialisasi komponen terlambat
- [x] Dropdown Opportunity claim tetap menggunakan renderer tabel yang sama saat field awalnya tersembunyi dan baru dipilih
- [x] Input pencarian dropdown referensi claim tetap satu baris dan mencari nama, perusahaan, client, serta status; Project dan Opportunity memakai renderer yang sama
- [x] Dropdown Project/Opportunity claim mengikuti komponen lembur dan menampilkan panel detail referensi setelah dipilih
- [x] Pembentukan metadata dropdown claim memakai array biasa agar kompatibel dengan Eloquent Collection
- [x] Template form claim tidak memakai ekspresi Blade nested yang menyebabkan ParseError saat dibuka
- [x] Dropdown referensi claim menampilkan pencarian dan tabel kolom referensi, perusahaan, client, status, serta aksi pilih seperti form lembur
- [x] Backend claim mengunci pengajuan baru pada Reimbursement sampai workflow Cash Advance dikembangkan
- [x] Cash advance pada form claim ditandai sebagai tahap pengembangan berikutnya dan referensi Project/Opportunity ditampilkan sebagai dropdown tabel seperti form lembur
- [x] Form reimbursement/cash advance menghapus field Department dan memakai pilihan keperluan Internal atau External / Project beserta referensi pekerjaan berscope perusahaan
- [x] Form reimbursement/cash advance memakai layout dua kolom dan pola informasi pengajuan, rincian, karyawan, serta approver yang konsisten dengan form cuti/sakit
- [x] Filter jenis dan status pengajuan cuti/sakit menggunakan dropdown multiple select dan query server-side
- [x] Form edit draft lembur tidak menampilkan judul atau catatan revisi; pesan revisi hanya tampil untuk status non-draft yang perlu diperbaiki
- [x] Simpan draft lembur kembali ke daftar lembur dengan pesan draft, bukan ke detail dengan pesan revisi
- [x] Tombol Kirim pengajuan lembur menggunakan dialog konfirmasi template, bukan native browser confirm
- [x] Submit draft lembur selalu mengalokasikan nomor baru dan melewati nomor master yang sudah terpakai
- [x] Panel ringkasan draft di atas daftar lembur dihilangkan karena aksi draft tersedia langsung pada baris tabel
- [x] Panel ringkasan draft di atas daftar lembur dihilangkan karena aksi draft tersedia langsung pada baris tabel
- [x] Filter status pengajuan lembur menggunakan dropdown multiple select dan query server-side `status[]`
- [x] Draft dan revisi cuti/sakit memakai template layout form yang sama dengan halaman create
- [x] Preview dokumen pendukung pada edit draft cuti/sakit tampil menggunakan popup dialog
- [x] Edit draft cuti/sakit menampilkan dokumen pendukung tersimpan, menyediakan preview/unduh, dan hanya mewajibkan upload jika dokumen belum ada
- [x] Form lembur, reimbursement/cash advance, dan cuti/sakit mendukung draft, konfirmasi submit, penghapusan draft, serta alokasi nomor master saat dikirim
- [x] Dropdown referensi pekerjaan memiliki input pencarian tetap dan overflow vertikal/horizontal hanya pada tabel
- [x] Pemohon dapat menyimpan perubahan revisi claim tanpa mengirimkannya; Kirim revisi tetap memakai dialog konfirmasi dan memulai ulang approval setelah disetujui
- [x] Detail claim menampilkan semua siklus workflow secara kronologis; identitas pelaku, komentar, dan snapshot dibatasi permission audit trail claim
