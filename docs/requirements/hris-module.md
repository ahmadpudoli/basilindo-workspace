# Requirement Modul HRIS

Status: implementasi inti berjalan; verifikasi unit/feature tersedia.

## Tujuan

Menyediakan sumber data karyawan dan alur awal pengajuan cuti/sakit dalam
Basilindo Workspace. Modul memakai login, user, company scope, role, permission,
audit, dan layout aplikasi yang sudah tersedia.

## Keputusan requirement

- Dashboard `/workspace/hris` memakai satu Blade utama dengan susunan visual dasar yang sama bagi semua pengguna. KPI pengajuan pribadi ditampilkan untuk setiap pengguna HRIS dan hanya menghitung pengajuan milik akun tersebut; KPI tenaga kerja, tabel karyawan, CTA, dan panel approval mengikuti permission. Tidak memilih dashboard berbeda berdasarkan akses daftar karyawan.
- Panel Early Warning Approval pada dashboard HRIS hanya ditampilkan kepada pengguna yang berhak melihat approval dan ketika terdapat pengajuan yang menunggu persetujuan pengguna tersebut; panel disembunyikan saat kosong.
- Judul notifikasi HRIS untuk approval, penolakan, permintaan revisi, dan tindakan yang menunggu harus menyebut tahap/role terkait (Atasan, HR, Finance, atau Approver final); notifikasi lama dengan metadata tahap menampilkan judul yang sama jelasnya.
- Istilah bisnis yang digunakan adalah **karyawan**, bukan mahasiswa.
- Satu karyawan terhubung ke satu perusahaan operasional dan dapat dikaitkan
  dengan satu akun Workspace.
- Karyawan memiliki atasan langsung untuk kebutuhan workflow fase berikutnya.
- Kuota cuti tahunan dapat dikonfigurasi per perusahaan dan tahun; default
  baseline adalah 12 hari.
- Sakit tidak memiliki kuota; sistem hanya mencatat jumlah hari dan riwayatnya.
- Pengajuan cuti/sakit dapat menggunakan tanggal lampau (backdate), sesuai
  kebijakan yang dikonfigurasi.
- Halaman detail `/workspace/hris/leave-requests/{id}` dan revisi pengajuan hanya dapat diakses pemilik/pemohon yang terkait dengan pengajuan (`requester_user_id`, `created_by`, atau akun karyawan pemilik); akses approver tetap melalui halaman detail approval khusus.
- Pengajuan aktif tidak boleh overlap dengan pengajuan aktif lain milik karyawan
  yang sama. Satu tanggal yang beririsan sudah cukup untuk menolak pengajuan.
- Pengajuan ditolak, dibatalkan, kadaluarsa, atau draft tidak memblokir tanggal.
- Parameter hari libur, cut-off, aturan approval, dan kebijakan lembur akan
  disimpan sebagai pengaturan, bukan hardcode.
- Kalender hari libur dikelola pada Manajemen Kehadiran dan mendukung cakupan
  global atau beberapa perusahaan sekaligus melalui assignment multi-company.
- Hari libur aktif tidak dapat dipilih pada pengajuan cuti maupun sakit;
  cuti bersama mengurangi kuota cuti tahunan efektif dan daftar cuti bersama
  tahun berjalan ditampilkan pada form pengajuan.
- Reimbursement dan cash advance menggunakan requirement khusus pada
  `requirements/hris-reimbursement.md`; reimbursement memiliki rincian biaya,
  approval Finance, threshold final per perusahaan, dan pembayaran terpisah.
- Notifikasi in-app dikirim dengan metadata ID saja; Web Push PWA memakai
  subscription browser terenkripsi, job queue, service worker, dan VAPID.
- Pengajuan lembur merupakan laporan realisasi setelah pekerjaan selesai, dapat
  memuat beberapa rincian tanggal/jam dalam satu nomor pengajuan.
- Workflow lembur adalah Karyawan -> Atasan -> HR. Atasan memverifikasi pekerjaan
  dan waktu realisasi; HR menetapkan kelayakan serta durasi yang diakui untuk
  penggajian. Setiap tahap mendukung setujui, tolak, dan minta revisi.
- Persetujuan Head Finance merupakan workflow rekap penggajian terpisah. Hanya
  lembur berstatus disetujui HR yang boleh menjadi sumber rekap tersebut.
- Unit organisasi perusahaan operasional internal dimiliki Core. HRIS hanya
  menyimpan referensi unit pada karyawan dan snapshot identitas organisasi pada
  pengajuan agar histori tidak berubah ketika karyawan dimutasi.

### Master struktur organisasi Core

- Unit organisasi hanya dapat dibuat untuk perusahaan internal berperan sebagai
  perusahaan operasional.
- Unit mendukung kode, nama, tipe (direktorat/divisi/departemen/tim/unit), induk,
  status aktif/nonaktif, serta urutan tampilan.
- Induk unit wajib berasal dari perusahaan yang sama dan tidak boleh membentuk
  siklus. Unit yang sudah dipakai karyawan dinonaktifkan, bukan dihapus.
- Daftar unit mengikuti company scope, memiliki pencarian, filter perusahaan dan
  status, sorting server-side, serta pagination 10/25/50/100.

### Pengajuan Lembur

- Layout form pengajuan lembur mengikuti pola form cuti/sakit: satu card utama,
  dua kolom informasi, panel informasi karyawan/atasan, rincian terstruktur, dan
  footer aksi konsisten.
- Identitas pemohon tetap berasal dari akun aktif dan dikirim sebagai referensi
  tersembunyi; field visual Pemohon tidak ditampilkan pada form.
- External / Project menampilkan dropdown Project opsional dengan floating label;
  Internal menyembunyikan dan menonaktifkan dropdown serta menyimpan project kosong.
- Pada form pengajuan/revisi lembur, pilihan Project dan Opportunity memuat data
  lintas perusahaan tanpa membatasi company scope atau membership. Permission lihat
  modul (`view_project` atau `view_crm_opportunity`) tetap diwajibkan.
- Validasi client-side lembur hanya memastikan tipe dan ID referensi terpilih valid
  secara format; validasi server memastikan record ada dan permission lihat terkait
  dimiliki, tanpa menolak karena company scope. Aturan ini hanya berlaku pada
  pengajuan/revisi lembur.
- Acceptance criteria: external tanpa referensi tetap dapat dikirim; referensi lintas
  perusahaan tersimpan jika permission terkait dimiliki; referensi tidak ada atau
  permission tidak dimiliki ditolak; pergantian ke internal mengosongkan relasi.
  Kolom nullable incremental menjaga semua pengajuan lama.
- Form menggunakan identitas karyawan dari akun pengguna yang sedang login; pemohon tidak memilih karyawan lain.
- Alasan umum tidak wajib diisi terpisah karena uraian pekerjaan dicatat pada setiap rincian lembur.
- Nomor pengajuan berasal dari master penomoran Core per perusahaan dan jenis form, dengan token `[no_urut]`, `[tahun]`, `[bulan]`, `[bulan_romawi]`, `[hari]` serta periode counter tahunan, bulanan, harian, atau global.
- Isi kolom nomor pengajuan pada tabel Cuti & Sakit memakai gaya elemen kode yang sama dengan tabel Lembur.
- Kolom nomor pengajuan pada Ringkasan Lembur Karyawan memakai gaya elemen kode yang sama dengan kolom nomor di daftar Lembur.
- Kolom nomor pengajuan pada Ringkasan Cuti Karyawan memakai warna, font monospace, dan ukuran teks yang sama dengan kolom nomor pengajuan pada daftar Cuti & Sakit.
- Form rincian lembur memberi ruang cukup untuk tombol Hari Kerja/Hari Libur di dalam panel; field waktu dibuat lebih ringkas pada layout desktop dan seluruh elemen tetap responsif.
- Halaman detail lembur mempertahankan posisi panel, memberi identitas visual berwarna lembut pada informasi, ringkasan, dan rincian; riwayat workflow memakai accordion berikon yang terbuka secara default dan tetap aksesibel melalui keyboard.
- Informasi pengajuan detail lembur memakai grid datar dengan pemisah tipis dan ikon kecil; setiap field tidak dibungkus kartu terpisah.
- Panel Informasi pengajuan detail lembur menampilkan nama karyawan, NIP, dan perusahaan tempat karyawan terdaftar, disertai divisi/unit, jabatan saat pengajuan, serta jenis/lokasi kerja dalam grid compact yang responsif.
- Header panel Informasi pengajuan, Ringkasan, dan Rincian realisasi dibuat compact; judul Rincian realisasi dan badge jumlah rincian tetap dalam satu baris.
- Referensi pekerjaan pada detail lembur harus menyebutkan jenis referensi secara jelas dan menampilkan nama yang mudah dibaca. Untuk Project, tampilkan Client dan perusahaan internal pemilik Project; Opportunity menampilkan Client dan perusahaan internal terkait. Informasi referensi dimuat secara eager agar tidak menambah query per relasi.
- Badge jenis referensi (Project, Opportunity, atau Internal / tanpa referensi) berada di sisi kanan header accordion Referensi pekerjaan. Accordion ini tertutup secara default dan dapat dibuka pengguna.
- Metadata di dalam accordion Referensi pekerjaan tidak memakai garis horizontal atau ruang atas berlebih sebelum daftar Client, pemilik perusahaan, dan status referensi.
- Pada layar mobile, jarak vertikal antara panel Referensi pekerjaan, Riwayat workflow, dan Rincian realisasi harus compact; gutter kolom tidak boleh menimbulkan ruang kosong berlebihan.
- Pada detail lembur, Informasi pengajuan dan Ringkasan berada di bagian atas area utama. Referensi pekerjaan berbentuk accordion berada di bawah keduanya dan selebar area utama; Riwayat workflow menjadi sidebar kanan yang dimulai sejajar dengan Ringkasan dan membentang di samping referensi serta rincian realisasi. Pada layar sempit, konten utama dan sidebar bertumpuk.
- Jarak vertikal antara baris Informasi pengajuan/Ringkasan dan accordion Referensi pekerjaan pada desktop harus rapat (maksimal 8 px selain border); jarak horizontal sidebar tetap mengikuti gutter 16 px.
- Halaman approval lembur tidak menampilkan bagian atau modal Dokumen pendukung. Informasi permohonan disajikan sebagai grid dua kolom yang compact, dengan hierarki label/nilai, separator halus, dan alasan selebar baris; field tidak dibuat sebagai kartu individual. Grid harus menumpuk dengan rapi pada layar kecil dan tabel rincian tetap berada di bawah informasi.
- Ringkasan informasi approval lembur memiliki jarak bawah yang tampak sebelum tabel rincian, serta label status berbahasa Indonesia yang konsisten dengan detail lembur (Menunggu Atasan, Menunggu HR, Perlu revisi, Disetujui HR, Ditolak).
- Detail lembur memakai resource Blade yang sama pada route Pengajuan Lembur dan Ringkasan Lembur. Route Pengajuan Lembur memeriksa owner scope (`requester_user_id`, `created_by`, atau user pemilik Employee), setara dengan detail dari menu Pengajuan Cuti; route Ringkasan Lembur memakai permission `view_overtime` dan scope perusahaan. Navigasi aktif serta tombol Kembali pada route ringkasan tetap mengikuti halaman pemanggil.
- Form pengajuan lembur menempatkan jenis lembur dan accordion informasi karyawan/alur approval pada kolom kiri, serta rincian lembur pada kolom kanan. Pilihan jenis lembur menggunakan kartu compact berikon yang konsisten dengan form reimbursement.
- Form create/edit cuti/sakit menempatkan nomor pengajuan, jenis pengajuan, dokumen pendukung, serta accordion informasi karyawan/alur persetujuan pada kolom kiri. Panel kuota/tanggal dan field alasan berada di kolom kanan. Accordion informasi karyawan dan approver tertutup secara default dan mengikuti pola form lembur; kartu pilihan jenis dibuat compact, separator dekoratif yang tidak perlu dihilangkan, dan pada layar kecil kolom ditumpuk dengan urutan yang sama.
- Baris tanggal pada form cuti/sakit menyelaraskan tinggi nomor urut, input tanggal, dan tombol hapus.
- Pesan validasi tanggal ditampilkan di bawah field tanpa mengubah posisi vertikal field, badge nomor, atau tombol hapus.
- Halaman daftar Cuti & Sakit tidak menampilkan panel terpisah untuk draft; penyimpanan draft baru maupun perubahan draft mengarahkan pengguna ke halaman edit draft.
- Perubahan draft cuti/sakit mempertahankan ID record draft sehingga tautan edit tetap valid setelah penyimpanan.
- Panel detail cuti/sakit tidak menampilkan garis horizontal antara daftar tanggal dan alasan pengajuan.
- Label Alasan pengajuan pada detail cuti/sakit ditampilkan tebal untuk membedakannya dari isi alasan.
- Ringkasan cuti bersama pada form cuti/sakit dapat dibuka/tutup dan default tertutup; judul panel tetap menampilkan jumlah hari yang mengurangi kuota.
- Pada kolom kiri, panel nomor pengajuan harus berada paling atas sebelum pilihan jenis lembur.
- Label Jenis lembur harus memiliki jarak yang jelas dari kartu pilihan; kartu pilihan dibuat ringkas tanpa mengurangi keterbacaan.
- Isi accordion informasi karyawan dan alur persetujuan pada form lembur harus compact, memakai aksen warna lembut untuk membedakan ringkasan karyawan, atasan, dan HR approver, serta tetap terbaca pada layar kecil.
- Form create/edit lembur tidak memakai garis bawah pada tiap section; jarak setelah informasi nomor pengajuan dan setelah section jenis/lokasi dirapatkan agar tidak menyisakan ruang kosong berlebih.
- Form create/edit lembur selalu menyediakan aksi Simpan draft. Pada pengajuan berstatus `needs_revision`, aksi ini menyimpan perubahan sambil mempertahankan status, alasan, dan metadata permintaan revisi; Kirim ulang revisi tetap menjadi aksi terpisah.
- Tombol aksi footer form lembur (Batal, Simpan draft, dan Kirim) menggunakan ukuran small agar proporsional terhadap form.
- Tombol Ajukan Lembur pada halaman daftar memakai ikon tambah yang mudah dikenali, gaya modern, dan state hover/focus yang terlihat.
- Urutan kolom tabel Cuti & Sakit menempatkan Nomor pengajuan sebelum Karyawan, dengan layout tabel native.

- Pemohon memilih karyawan yang dapat diakses, jenis lembur internal atau
  external/project, lokasi kerja, alasan umum, serta satu atau lebih rincian.
- Setiap rincian berisi tanggal kerja, waktu mulai, waktu selesai, dan uraian
  pekerjaan. Waktu selesai yang sama/lebih awal dari waktu mulai dianggap melewati
  tengah malam.
- Durasi dihitung server-side per rincian, menerapkan aturan pembulatan perusahaan,
  lalu dijumlahkan. Batas maksimum diterapkan pada total satu pengajuan.
- Pengajuan baru langsung berstatus `pending_manager`; pengajuan tanpa atasan aktif
  ditolak pada boundary agar tidak tertahan tanpa approver.
- Revisi substansi selalu kembali ke Atasan. Revisi administratif kembali ke tahap
  yang meminta revisi. Pemohon adalah satu-satunya pihak yang dapat mengirim ulang.
- Daftar lembur pada `/workspace/hris/overtime` hanya menampilkan pengajuan milik
  akun yang sedang login (berdasarkan pemohon atau relasi akun karyawan), meskipun
  pengguna memiliki company scope yang lebih luas. Halaman tetap menyediakan filter
  status/periode/karyawan, pagination 10/25/50/100, sorting server-side, detail
  rincian, badge status berwarna sesuai kondisi, serta menu aksi dropdown untuk detail,
  revisi, dan pengelolaan draft.
- Panel metrik lembur pada halaman pengajuan disajikan menyatu dalam satu wadah;
  tampilannya lembut dengan warna pembeda tiap metrik (disetujui, proses, ditolak,
  draft) tetap dipertahankan.
- Data lembur lama dibackfill menjadi satu rincian tanpa menghapus kolom atau data
  lama; kolom lama dipertahankan sebagai kompatibilitas sementara.

## Acceptance criteria fase 1

### Biodata personal

- Halaman Biodata menampilkan identitas karyawan dan data organisasi dalam
  layout compact dengan hierarki label/nilai yang mudah dipindai.
- Tree organisasi mempertahankan relasi atasan-bawahan dan interaksi buka/tutup,
  dengan akun aktif terlihat jelas, fokus keyboard tersedia, dan node tetap
  terbaca pada layar kecil tanpa ruang kosong berlebih.
- Tree organisasi memakai satu panel luar; wadah tree di dalam dan baris node
  tidak diberi kotak individual. Hierarki ditunjukkan melalui indentasi dan
  garis cabang yang lembut.
- Aksen warna dan gradasi bersifat lembut serta menjaga kontras teks; sumber data
  tetap terbatas pada karyawan yang terhubung ke akun aktif.
- Field biodata tidak ditampilkan sebagai kartu individual; gunakan satu panel
  dengan pemisah halus dan ruang yang konsisten agar permukaan visual terasa
  ringan.

### Ringkasan HRIS

- Dashboard `/workspace/hris` menyajikan ringkasan pengelola dengan hierarki
  informasi yang jelas: pengantar modul, aksi sesuai permission, metrik utama,
  daftar karyawan terbaru, dan approval yang memerlukan tindakan.
- Metrik memakai warna semantik lembut, angka mudah dipindai, serta keterangan
  singkat yang menjelaskan konteksnya; warna tidak menjadi satu-satunya penanda.
- Tampilan responsif pada layar kecil, memakai kontras teks yang terbaca,
  state kosong yang informatif, dan tetap mempertahankan authorization yang ada.
- Ringkasan personal tetap tersedia bagi pengguna tanpa permission daftar
  karyawan; hero, metrik, dan ajakan tindakan memakai hierarki visual yang sama
  baik pada tampilan pengelola maupun personal. Data perusahaan tidak boleh
  dibuka lewat ringkasan personal.
- Ringkasan personal menampilkan pintasan Cuti/Sakit, Lembur, dan Reimbursement
  hanya jika pengguna memiliki permission create untuk jenis pengajuan tersebut.
- Ringkasan personal memakai satu kartu ringkas untuk tiap jenis pengajuan.
  Kartu menampilkan jumlah, ikon, tautan jumlah/judul ke daftar jika list
  permission tersedia, serta tombol Ajukan di dalam kartu jika create permission
  tersedia. Tidak ada panel tindakan terpisah yang menduplikasi pintasan.
- KPI ringkasan personal mengikuti periode masing-masing: cuti/sakit memakai
  tahun aktif HRIS; lembur dan reimbursement memakai bulan kalender berjalan.
- KPI cuti/sakit menampilkan jumlah pengajuan pada tahun aktif, hari cuti dan
  sakit yang berstatus disetujui, serta sisa kuota cuti setelah pengurangan
  cuti bersama sesuai aturan perusahaan.
- Panel KPI daftar cuti/sakit memakai satu wadah gradasi biru–putih–pastel
  dengan metrik datar, pemisah yang jelas di mobile, tanpa kartu bersarang;
  setiap metrik tetap membuka rincian. Panel mobile terbuka secara default dan
  dapat diciutkan melalui kontrol yang state aksesibelnya diperbarui. Tinggi
  panel dan padding metrik dijaga compact pada desktop maupun mobile.
- KPI lembur menampilkan jumlah pengajuan dengan rincian tanggal kerja pada
  bulan berjalan, jumlah pengajuan yang disetujui, dan durasi approved dari
  rincian lembur pada bulan tersebut.
- KPI reimbursement menampilkan jumlah klaim reimbursement yang tercatat pada
  bulan berjalan (berdasarkan tanggal pencatatan pengajuan), jumlah yang telah
  disetujui penuh, dan total nominal pengajuan yang disetujui. Draft dan cash
  advance tidak masuk KPI ini.
- Pintasan pengajuan memakai ikon yang membedakan jenisnya dan label yang
  eksplisit. Dashboard pribadi menjaga kepadatan informasi, mengurangi ruang
  vertikal yang tidak perlu, serta menyusun tombol dalam grid yang tetap mudah
  digunakan pada viewport mobile.
- Panel early warning approval hanya dirender jika pengguna berhak melihat
  approval dan terdapat pengajuan dalam cakupan approver pengguna; jika kosong,
  panel tidak ditampilkan.

### Master karyawan

- Pengguna berizin dapat melihat karyawan sesuai company scope.
- Tombol Tambah Karyawan memiliki ikon orang-plus yang jelas dan gaya aksi utama modern, dengan fokus keyboard yang terlihat.
- Data karyawan memiliki nomor karyawan, nama, perusahaan, jabatan, atasan,
  tanggal mulai, dan status.
- Karyawan yang sudah memiliki relasi tidak dihapus permanen; arsip memakai
  soft delete/status archived.
- Nomor karyawan unik dalam perusahaan.

### Dokumen karyawan

- Tombol Upload dokumen memakai class aksi utama global dengan ikon dokumen-upload, hover, dan indikator fokus keyboard.

### Pengajuan cuti/sakit

- Pengguna dapat membuat pengajuan dengan tipe `leave` atau `sick`, satu atau
  beberapa tanggal pengajuan, alasan, dan lampiran reference opsional.
- Setiap tanggal disimpan sebagai detail `leave_request_dates`; jumlah hari
  dihitung server-side dari jumlah detail tanggal yang unik.
- Pengajuan lintas tahun kalender ditolak pada fase pertama karena saldo kuota
  dihitung per tahun; dukungan split-year menjadi requirement lanjutan.
- Cuti memeriksa saldo tahun berjalan; sakit tidak mengurangi saldo.
- Pengajuan baru ditolak jika salah satu tanggal overlap dengan detail tanggal
  pada pengajuan aktif.
- Setelah validasi berhasil, pengajuan langsung masuk status `pending_manager`
  (Menunggu atasan).
- Status workflow cuti/sakit yang didukung hanya Menunggu atasan, Menunggu HR,
  Revisi, Disetujui, dan Ditolak. Event submit tetap direpresentasikan oleh
  waktu dibuat dan timeline, bukan sebagai status terpisah.
- Atasan atau HR dapat meminta revisi dengan alasan wajib dan klasifikasi
  `substantive` atau `administrative`; pemohon menjadi satu-satunya pihak yang
  mengubah isi pengajuan.
- Atasan dan Finance dapat meminta revisi reimbursement/cash advance dari tabel
  inbox maupun detail approval. Aksi inbox wajib menampilkan dialog jenis dan
  alasan revisi, dan backend memvalidasi tahap serta approver aktif.
- Revisi substansi selalu kembali ke approval atasan. Revisi administratif
  kembali ke pihak yang meminta revisi; untuk HR berarti langsung ke HR.
- Setiap permintaan revisi dan pengiriman ulang disimpan sebagai approval event
- Setiap pengajuan cuti/sakit, lembur, reimbursement, dan cash advance memiliki nomor pengajuan unik otomatis yang tampil pada form, daftar, detail, dan notifikasi.
- Tautan notifikasi approval hanya membuka detail aksi saat status masih menunggu tahap tersebut; notifikasi lama setelah keputusan mengarah ke daftar approval dan tidak menghasilkan 404.
- Notifikasi aplikasi, Web Push, dan email untuk pengajuan menyertakan tujuan langsung ke detail pengajuan; notifikasi yang meminta tindakan approval membuka detail approval pada tahap yang sesuai.
- Detail approval cuti/sakit mempertahankan posisi panel informasi, tindakan, dan timeline; panel informasi menyajikan identitas pemohon, status/tahap, tanggal, durasi, dan alasan dengan hierarki visual yang mudah dibaca dan compact, tanpa memecah setiap informasi menjadi kartu terpisah.
- Timeline pada detail approval cuti/sakit dan lembur disajikan sebagai accordion yang aksesibel, dengan ikon/status berwarna untuk pengajuan dibuat, persetujuan, penolakan, dan revisi.
  beserta aktor, waktu, jenis revisi, dan alasan.
- Status dan tanggal pengajuan disimpan untuk kebutuhan timeline approval.
- Halaman daftar menyediakan filter personal berdasarkan jenis, status, dan
  periode, pagination standar, serta akses detail pengajuan.
- Daftar Cuti & Sakit menampilkan nomor pengajuan dan mendukung pencarian kata
  kunci berdasarkan nomor pengajuan, nama karyawan, atau NIP; tabel memakai lebar
  kolom proporsional dan kolom aksi dropdown dengan detail, pratinjau lampiran,
  revisi, serta kelola draft sesuai status.
- Tabel daftar Cuti & Sakit dan Ringkasan Cuti Karyawan menampilkan tombol
  Lihat dokumen pada kolom aksi jika pengajuan memiliki lampiran. Tombol
  membuka dialog preview melalui endpoint privat terotorisasi; PDF dan gambar
  ditampilkan inline, sedangkan format lain tetap menyediakan fallback buka/
  unduh.
- Ringkasan tahun aktif menampilkan kuota, penggunaan, dan sisa cuti; sakit
  hanya dihitung sebagai riwayat tanpa mengurangi kuota.
- Detail pengajuan menampilkan timeline dibuat, verifikasi manager/atasan,
  verifikasi HR, dan keputusan akhir beserta waktu, aktor, serta komentar.
- Detail cuti/sakit menyediakan preview dialog untuk PDF/gambar dan unduhan untuk
  jenis file lain melalui private storage dengan authorization policy pemohon atau
  scope HRIS yang sesuai.
- Mengunduh dokumen pendukung tidak menampilkan overlay pemuatan halaman karena
  unduhan tidak mengganti halaman yang sedang dibuka.
- Preview gambar pada form edit draft/revisi dan detail pengajuan merender partial
  viewer gambar melalui directive Blade terpisah dari kondisi PDF; source Blade
  tidak boleh tampil sebagai teks di dalam dialog.
- Daftar approval HRIS menggunakan dialog preview untuk dokumen PDF/gambar;
  jenis file lain tetap tersedia sebagai unduhan.
- Detail approval HRIS menggunakan dialog preview untuk dokumen pendukung PDF/gambar;
  tipe file lain tetap menggunakan unduhan.
- Dialog preview gambar pada daftar/detail approval menggunakan toolbar zoom bersama;
  PDF tetap memakai viewer PDF tanpa toolbar zoom gambar.
- Header halaman persetujuan menggunakan label “Kehadiran & Claim”.
- Detail dari Ringkasan Cuti Karyawan menggunakan route dan controller khusus
  Ringkasan, tetapi tetap memakai resource view detail cuti/sakit yang sama.
  Aksi pengajuan/pemohon disembunyikan dan sidebar tetap berada pada parent
  Ringkasan Cuti Karyawan.
- Semua timeline pengajuan menampilkan alasan awal pada aktivitas pengajuan dibuat
  dan keterangan pada setiap aktivitas approval jika tersedia.
- Ringkasan pada halaman Cuti & Sakit ditampilkan sebagai empat badge informasi
  dalam satu panel: kuota, cuti terpakai, sisa cuti, dan sakit tercatat.
- Badge status pada ringkasan detail pengajuan mengikuti lebar konten label dan
  tidak melebar memenuhi kolom.
- Form revisi pengajuan memakai struktur visual dan urutan section yang sama
  dengan form create, dengan jenis pengajuan tetap terkunci.
- Pada revisi pengajuan sakit, lampiran lama tetap digunakan bila file baru
  dikosongkan; file lama ditampilkan sebagai informasi dan dapat dipreview.
- Pengajuan cuti ditolak server-side bila belum ada konfigurasi kuota aktif yang
  berlaku untuk tahun tanggal pengajuan.
- Pesan validasi form tidak ditampilkan sebagai panel global duplikat; error
  ditampilkan satu kali pada field atau section yang relevan.
- Pengajuan cuti memerlukan konfigurasi `hris.leave_submission_cutoff` yang
  valid untuk tahun tanggal cuti; konfigurasi kosong atau tidak valid menolak
  pengajuan, sedangkan pengajuan sakit tidak terpengaruh cut-off cuti.
- Pengajuan cuti setelah tanggal cut-off tahun terkait ditolak server-side;
  tanggal cut-off berlaku sampai akhir hari pada timezone aplikasi.
- Pengajuan ulang yang tanggalnya beririsan dengan pengajuan aktif lain
  ditolak kembali ke form revisi dengan pesan validasi, bukan menjadi error
  internal server.
- Pesan konflik tanggal membedakan tanggal yang sudah disetujui dan tanggal
  yang masih menunggu proses persetujuan atasan atau pemeriksaan HR.
- Pengiriman ulang revisi mencatat event approval melalui model domain HRIS
  yang benar dan dijalankan dalam transaksi yang sama dengan perubahan data.
- Waktu pada kartu dan timeline `Pengajuan dibuat` selalu memakai waktu
  pengajuan pertama; pengiriman ulang hanya memperbarui `revision_submitted_at`.
- Notifikasi yang ditujukan kepada approver membuka halaman detail approval
  pada child menu Persetujuan HRIS, sedangkan notifikasi status untuk pemohon
  tetap membuka detail pengajuan biasa.
- Detail approval menggunakan halaman terpisah dari detail pemohon dan
  menyediakan tindakan setujui, tolak, serta minta revisi.
- Tindakan approval pada detail dipisahkan menjadi blok berjudul dengan
  penjelasan singkat agar perbedaan Setujui, Tolak, dan Minta revisi jelas.
- Blok Minta revisi menyediakan panel informasi collapsible untuk menjelaskan
  perbedaan revisi substansi dan revisi administrasi.
- Permission `view_hris_leave_summary`, `view_hris_overtime_summary`,
  `view_hris_reimbursement_summary`, dan `view_hris_cash_advance_summary`
  masing-masing memiliki menu ringkasan dan daftar pengajuan lintas karyawan
  yang dibatasi oleh scope perusahaan user.
- Ringkasan Cuti Karyawan dan Ringkasan Lembur Karyawan adalah halaman dan
  route terpisah dari halaman operasional Cuti & Sakit dan Lembur.
- Aksi Tolak dan Minta revisi pada halaman detail approval menggunakan modal
  konfirmasi/input agar halaman tetap ringkas; setelah keputusan tersimpan,
  pengguna kembali ke daftar Persetujuan HRIS.
- Tabel parameter HRIS menampilkan nama bisnis yang sama dengan label dropdown
  sekaligus tetap mempertahankan kode parameter teknis.
- Field nilai parameter HRIS menyesuaikan tipe parameter: tanggal untuk cut-off,
  angka untuk kuota/maksimum/ambang, dan teks untuk parameter lain.
- Setiap parameter HRIS menampilkan deskripsi panduan yang berubah sesuai pilihan
  pengguna.
- `hris.overtime_maximum_minutes` bersifat opsional; jika belum dikonfigurasi,
  sistem tidak menerapkan batas maksimum durasi lembur.
- `hris.leave_submission_cutoff` hanya boleh memiliki satu konfigurasi aktif
  untuk setiap tahun kalender; aturan ini berlaku lintas scope Global dan
  perusahaan.
- Tanggal berlaku pada tabel parameter ditampilkan dalam format lokal Indonesia
  `dd/mm/yyyy`; nilai cut-off tetap menggunakan format teknis `YYYY-MM-DD`.
- Kegagalan validasi pada halaman Pengaturan HRIS menampilkan warning yang
  menjelaskan bahwa parameter belum disimpan beserta alasan validasinya.
- Aksi arsip parameter HRIS menggunakan modal konfirmasi Workspace, bukan
  dialog konfirmasi native browser.
- User yang memiliki bawahan atau permission approval dapat membuka daftar
  approval khusus untuk cuti, sakit, lembur, reimbursement, dan cash advance.
- Permission `view_hris_approvals` menjadi permission granular untuk menu inbox
  approval; data approval manager dibatasi pada bawahan langsung user.
- Panel filter inbox Persetujuan HRIS memakai `.workspace-list-filter-form` langsung pada elemen form, tanpa pembungkus `.card-body` bersarang. Padding vertikal global row filter adalah 6px di atas dan 10px di bawah.
- Dashboard HRIS menampilkan early warning daftar approval yang menunggu jika
  user memiliki `view_hris_approvals`; setiap item mengikuti scope inbox dan
  mengarah ke detail approval yang terotorisasi. Daftar ini mencakup seluruh
  tahap yang tersedia di inbox: atasan, HR, Finance, dan approver final untuk
  cuti/sakit, lembur, reimbursement, serta cash advance.
- Menu Hari Libur menyediakan CRUD terotorisasi, filter tahun/jenis/status,
  assignment perusahaan, audit trail, serta opsi pengurang kuota cuti untuk
  setiap tanggal cuti bersama.

## Status

Workflow cuti/sakit menggunakan `pending_manager`, `pending_hr`,
`needs_revision`, `approved`, dan `rejected`. `needs_revision` bukan keputusan
akhir; pemohon dapat memperbaiki dan mengirim ulang sesuai klasifikasi revisi.

## Parameter yang disiapkan

Kunci parameter menggunakan namespace `hris.*`, memiliki nilai JSON dan dapat
memiliki tanggal berlaku, antara lain:

- `hris.annual_leave_quota`;
- `hris.leave_backdate_days`;
- `hris.leave_submission_cutoff`;
- `hris.work_calendar`;
- `hris.approval_workflow`;
- `hris.overtime_rounding` dan `hris.overtime_maximum`.

## Dokumen karyawan dan kontrak

Dokumen kontrak kerja, addendum, surat pengangkatan, identitas, dan dokumen
personal disimpan sebagai domain HRIS terpisah dari Document Management. File
tetap menggunakan private MinIO/S3, dengan object key berprefix
`hris/employee-documents/{employee_id}`. Metadata disimpan di koneksi HRIS dan
menyimpan checksum, MIME, ukuran, nama file asli, periode berlaku, serta akun
pengunggah.

### Acceptance criteria

- Pengguna berpermission dokumen HRIS hanya melihat dokumen karyawan dalam
  scope perusahaan yang dimilikinya.
- Upload harus memilih karyawan aktif, jenis dokumen, judul, dan file PDF/JPG/PNG
  maksimal 20 MB; tanggal berakhir tidak boleh sebelum tanggal berlaku.
- File tidak disimpan pada public disk dan tidak menjadi bagian dari daftar
  Document Management umum.
- Download melewati policy scope dan membaca object private melalui stream.
- Arsip hanya melakukan soft delete metadata; object tidak dihapus otomatis agar
  kebutuhan retensi dan audit tetap dapat dipenuhi.
- Daftar menyediakan pencarian, filter karyawan/jenis, sorting server-side,
  pagination, dan pilihan 10/25/50/100 baris.
- Permission dokumen karyawan ditampilkan pada grup HRIS di pengaturan role dan
  pengguna, meskipun nama teknisnya tetap kompatibel dengan assignment lama.
- Form upload dokumen memiliki susunan modern dengan area file/preview, metadata
  dokumen, informasi keamanan, dan nomor dokumen yang dibuat otomatis server-side.
- Nomor dokumen unik berformat `HRIS-YYYYMMDD-XXXXXXXXXX`, tersimpan sebagai
  metadata, menjadi bagian dari object key, tampil pada daftar, dan dapat dicari.

## Di luar fase ini

### Tahun aktif HRIS dan KPI cuti

- Parameter global `hris.active_year` dikelola melalui Pengaturan HRIS dan menjadi default filter tahun pada halaman Cuti & Sakit.
- KPI kuota, cuti bersama, cuti terpakai, sisa cuti, dan sakit tercatat harus mengikuti tahun filter yang dipilih.
- Detail KPI harus menjelaskan sumber perhitungan dan menampilkan tanggal yang membentuk nilai cuti bersama, cuti terpakai, atau sakit tercatat.

Perhitungan hari kerja berdasarkan kalender libur dan upload evidence langsung
ke object storage masih memerlukan konfigurasi operasional tambahan. Core
workflow lembur, reimbursement, cash advance, approval event, notifikasi
in-app, dan Web Push adapter sudah tersedia; delivery memerlukan VAPID key dan
worker queue aktif.
