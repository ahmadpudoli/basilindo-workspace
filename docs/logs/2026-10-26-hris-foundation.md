# Implementasi fondasi modul HRIS

Tanggal: 2026-10-26

## Perubahan

- Menambahkan requirement dan acceptance criteria khusus modul HRIS.
- Menetapkan HRIS sebagai modul bertahap di Workspace, tanpa login/database baru.
- Memulai fase pertama: master karyawan, parameter HRIS, cuti/sakit, kuota
  tahunan, dan pencegahan overlap tanggal.
- Menambahkan lembur, claim reimbursement/cash advance, approval event,
  pertanggungjawaban cash advance, dan notifikasi in-app.
- Menambahkan halaman pengaturan HRIS untuk kuota cuti, cut-off, maksimum
  lembur, dan ambang approval final.
- Menambahkan Web Push PWA: subscription per user/device, validasi endpoint,
  service worker, job queue terenkripsi dengan `minishlink/web-push`, dan
  pengiriman otomatis dari perubahan status workflow HRIS.
- Memusatkan halaman pengaturan HRIS ke modul Pengaturan pada route
  `/workspace/settings/hris`; URL lama dipertahankan sebagai redirect.
- Menambahkan migration lifecycle parameter HRIS, pagination 10/25/50/100,
  filter status, edit, restore, dan arsip tanpa penghapusan permanen.

## Verifikasi

- Baseline arsitektur, domain model, security, checklist, dan pola module
  Workspace telah ditinjau sebelum implementasi.
- `php -l` lulus untuk seluruh PHP baru dan route list menampilkan 7 route HRIS.
- `php artisan migrate:fresh --seed --env=testing` lulus pada database testing
  disposable.
- `php artisan test --filter=HrisLeaveRequestTest` lulus: 3 test, 4 assertion.
- `php artisan test --filter=Hris` lulus: 8 test, 12 assertion setelah migration
  testing diisolasi ke `db_workspace_test` melalui environment proses.
- Migration `web_push_subscriptions` lulus pada database testing; route push
  terdaftar dan seluruh class Web Push lulus `php -l`.
- Migration archive parameter HRIS dan enam route lifecycle settings lulus pada
  database testing; test HRIS tetap lulus 8 test dan 12 assertion.
- Setelah ditemukan error schema pada database runtime `db_basilindo_workspace`,
  migration `000004` dan `000005` dijalankan secara incremental tanpa reset
  tabel. Keduanya tercatat `Ran` pada migration status.
- Menyamakan panel filter pengaturan HRIS dengan pola UI Workspace: tombol
  `Reset` dan `Terapkan` berpasangan serta tombol Terapkan memakai warna utama.
- Menambahkan penanda field wajib/opsional pada form tambah karyawan,
  validasi client-side khusus HRIS, pesan error server-side per field, batas
  panjang input, dan validasi nomor karyawan unik per perusahaan.
- Menyempurnakan daftar karyawan dengan pagination standar, Reset/Terapkan,
  filter perusahaan operasional multiple select, sorting, dan aksi Ubah serta
  Hapus/Arsip berdasarkan keberadaan relasi.
- Menetapkan wrapper `workspace-table-actions` pada kolom aksi HRIS agar
  aturan visual `.btn-sm` benar-benar diterapkan pada ukuran tombol tabel.
- Memindahkan ukuran tombol small ke aturan global `.btn.btn-sm`, sehingga
  seluruh modul memakai ukuran tombol yang konsisten tanpa override ukuran
  khusus tabel.

## Risiko tersisa

- Kalender hari libur dan perhitungan hari kerja belum diimplementasikan.
- Delivery Web Push membutuhkan VAPID key yang valid, HTTPS, permission browser,
  dan queue worker aktif pada deployment.
- `npm run build` belum dapat dijalankan karena PowerShell execution policy
  memblokir `npm.ps1`; gunakan `npm.cmd run build` pada environment lokal.
- Menghubungkan akun Core User dengan Employee melalui `employee_id`: pilihan
  karyawan berscope perusahaan dan belum terhubung, sinkronisasi saat create/
  update, serta tampilan Nama dan NIP pada daftar user.
- Mencegah assignment `super_admin` pada akun biasa melalui validasi server
  sebelum insert/update, menyembunyikan role tersebut dari form akun biasa,
  serta menghubungkan user ke satu karyawan dengan validasi scope perusahaan.
- Memetakan permission HRIS (`access_hris`, approval, settings, employee,
  leave, overtime, dan claim) ke grup `HRIS` pada form role; role
  `hris_admin` dan `hris_member` sudah diprovision oleh seeder.
- Menambahkan migration data rekonsiliasi role/permission HRIS sebagai master
  idempotent. Migration bersifat additive, melengkapi grant `hris_member`
  untuk view lembur dan claim, serta tidak mengubah grant modul lain.
- Menghapus provisioning HRIS duplikat dari `RoleSeeder`; seeder kini tidak lagi
  menjadi sumber kedua untuk role dan permission HRIS.
- Memperbaiki guard dashboard HRIS: akses modul memakai `access_hris`, sementara
  statistik dan daftar karyawan tetap dibatasi permission granular.
- Menampilkan menu Ringkasan HRIS untuk pemegang `access_hris`; user tanpa akses
  data karyawan mendapat halaman ringkasan pribadi untuk cuti/sakit, lembur,
  serta reimbursement/cash advance miliknya.
- Menambahkan daftar cuti dengan resume kuota/sakit tahun aktif, filter,
  pagination, detail timeline, serta approval inbox bawahan langsung dengan
  permission `view_hris_approvals`.
- Membatasi form dan endpoint pengajuan personal agar user tanpa akses data
  karyawan hanya dapat memilih karyawan yang terhubung dengan akunnya sendiri.
- Menyeragamkan tombol filter global: label `Reset` dan `Terapkan` tetap jelas,
  tetapi lebar tombol tidak lagi membagi penuh kolom filter.
- Menyelaraskan teks tombol filter secara horizontal/vertikal dan memposisikan
  grup tombol selalu rata kanan pada area filter.
- Menyeimbangkan grid filter Daftar Cuti agar input jenis, status, tahun, dan
  jumlah baris memakai ruang lebih luas, sedangkan area tombol tetap compact.
- Menambahkan navigasi sidebar recursive hingga tiga tingkat, menu Biodata
  dengan struktur atasan/bawahan, serta permission ringkasan HRIS terpisah.
- Menyederhanakan Manajemen Claim menjadi satu menu `Reimbursement & Cash
  Advance` karena halaman dan workflow keduanya masih berada dalam satu resource.
- Menggabungkan halaman claim dengan tab/filter `Semua`, `Reimbursement`, dan
  `Cash Advance`, termasuk filter status, pagination, dan sorting nominal.
- Menambahkan ikon dashboard, kalender, biodata, dan dokumen pada navigasi
  serta animasi ringan untuk hover, menu aktif, dan interaksi sidebar.
- Pest menampilkan warning cache hasil test di `vendor` tidak writable; hasil test
  tetap lulus dan warning tidak berasal dari kode HRIS.
- `php artisan view:cache` belum dapat diverifikasi karena `storage/logs/laravel.log`
  pada environment sandbox tidak writable; ini masalah permission environment.
- Verifikasi awal memakai `migrate:fresh --env=testing` saat `.env.testing` belum
  tersedia, sehingga Laravel memakai koneksi default `db_basilindo_workspace`;
  schema tersebut ter-reset dan seed berhenti pada constraint satu super admin.
  Test berikutnya sudah dipindahkan ke `db_workspace_test` melalui environment
  proses tanpa seed. Restore data default/backup perlu dilakukan di luar agent
  bila database tersebut berisi data penting.
- Menyatukan struktur atasan, karyawan login, dan seluruh bawahan pada satu
  tree organisasi recursive. Cabang dapat dibuka/tutup dengan `<details>`,
  jalur menuju pengguna login otomatis terbuka, dan node pengguna diberi
  penanda `Anda`.
- Membersihkan tampilan node tree organisasi dengan menghapus separator glyph
  yang berpotensi tampil sebagai karakter rusak, serta menormalkan marker
  bawaan elemen `summary` untuk browser yang berbeda.
- Memodernisasi UI tree organisasi dengan avatar inisial, metadata NIP yang
  lebih jelas, kartu node, state hover/current, garis hierarki, dan animasi
  ringan saat cabang dibuka.
- Memperbaiki error ParseError pada halaman Biodata HRIS dengan mengganti
  partial treeview yang memiliki nested directive Blade tidak valid menjadi
  partial recursive dengan struktur `@if/@elseif/@endif` yang seimbang.
- Lint controller HRIS berhasil; verifikasi `view:cache` tetap terhalang
  permission `storage/logs/laravel.log` pada environment sandbox.
- Menyempurnakan pengajuan cuti/sakit: karyawan otomatis mengikuti akun login,
  tanggal dipilih per baris dan disimpan pada tabel detail tanggal, overlap
  diperiksa per tanggal, cuti dibatasi sisa kuota, sakit tidak mengurangi kuota,
  back date diperbolehkan, dan dokumen pendukung wajib untuk sakit.
- Merapikan form pengajuan menjadi alur enam tahap: tanggal, jenis, dokumen,
  alasan, atasan, dan informasi HR. Tombol tambah tanggal ditempatkan di dalam
  panel tanggal, sementara pengajuan dinonaktifkan jika approver HR belum ada.
- Menyamakan lebar area judul tanggal, tombol tambah tanggal, dan daftar input
  agar tombol tambah rata kanan dengan tombol hapus pada baris tanggal.
- Mengoptimalkan layout form pengajuan untuk layar lebar dengan grid dua kolom:
  tanggal di kiri, jenis/dokumen di kanan, serta alasan dan informasi approval
  pada kolom yang seimbang; layar kecil otomatis kembali menjadi satu kolom.
- Mengembalikan gaya form pengajuan menjadi compact dengan menghapus nomor
  langkah dan deskripsi panjang, serta mengurangi padding agar tetap menyerupai
  form awal tanpa mengorbankan layout dua kolom pada layar lebar.
- Menambahkan master approver HR workflow cuti & sakit per perusahaan operasional
  dengan multi-select perusahaan, tampilan approver pada form pengajuan, validasi
  actor approval HR, dan notifikasi setelah approval manager.
