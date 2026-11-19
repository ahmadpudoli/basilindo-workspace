# Requirement Reimbursement HRIS

Status: implementasi fase awal

## Tujuan

Menyediakan pengajuan reimbursement berbasis rincian biaya dengan approval
berjenjang sesuai scope perusahaan dan proses pembayaran terpisah oleh Finance.

## Acceptance criteria daftar klaim

- Daftar kerja `/workspace/hris/claims` dan tab Semua/Reimbursement/Cash Advance hanya menampilkan klaim milik pengguna login (requester atau karyawan yang terhubung ke akun); draft yang dibuat pengguna sendiri juga tetap terlihat. Hak akses scope perusahaan tidak memperluas daftar pribadi ini.
- Tab Reimbursement menampilkan panel KPI compact satu panel seperti daftar lembur: total pengajuan, disetujui/dibayar beserta nominalnya, dalam proses, ditolak, dan draft. Agregasi mengikuti filter daftar aktif (periode, status, kategori, dan jenis) serta scope data pemohon; cash advance tidak masuk hitungan. Status disetujui/dibayar mencakup approved, pending payment, paid, dan completed; nominal ditampilkan per mata uang.
- Panel KPI reimbursement mobile dapat diciutkan/dibuka dan tertutup secara default untuk menghemat ruang; desktop menampilkan panel tanpa kontrol tambahan.
- Panel KPI memakai tinggi compact dan filter daftar pada desktop lebar menjaga label floating serta seluruh kontrol tetap terlihat di dalam panel.
- Filter reimbursement memakai class umum yang sudah tersedia `workspace-list-filter-form` dari CSS utama agar container, gradasi lembut, dan padding vertikal konsisten di semua halaman daftar; padding memberi ruang untuk label floating.
- Daftar claim pribadi Semua dan Reimbursement menyediakan pencarian server-side berdasarkan nama karyawan dan nomor pengajuan; keyword bekerja bersama filter lain dan dipertahankan melalui pagination.
- Pemohon dapat membuka detail klaim miliknya sendiri meskipun perusahaan klaim tidak termasuk scope perusahaan yang diberikan lewat role; akses data pengguna lain tetap mengikuti policy dan scope yang berlaku.
- Halaman ringkasan reimbursement/cash advance tetap menjadi laporan berbasis scope perusahaan dan terpisah dari daftar kerja pribadi.
- Ringkasan reimbursement/cash advance tidak menampilkan panel ikhtisar terpisah; gradasi visual diterapkan pada panel KPI agar tata letak lebih compact.
- Panel KPI ringkasan reimbursement/cash advance menggunakan class global bersama dengan halaman cuti dan lembur, termasuk varian empat metrik responsif.
- Nomor pengajuan pada tabel ringkasan menggunakan gaya monospace seperti tabel Ringkasan Cuti Karyawan; isi tabel membungkus teks di dalam kolom agar tidak menimpa kolom tetangga.
- Route detail dari menu Reimbursement & Cash Advance hanya dapat dibuka pemilik/pemohon klaim; akses `view_any_claim` tidak memperluas akses detail pribadi.
- Route detail dari Ringkasan Reimbursement/Cash Advance terpisah dan hanya dapat dibuka pengguna dengan izin ringkasan yang sesuai atau `view_any_claim`, dengan pemeriksaan scope perusahaan dan tanpa akses draft.
- Detail yang dibuka dari Ringkasan Reimbursement memakai route terpisah dengan tampilan Blade yang sama, mempertahankan menu ringkasan aktif dan mengembalikan pengguna ke ringkasan beserta filter sebelumnya; otorisasi tetap memeriksa pemilik atau permission ringkasan dan scope perusahaan.
- Tombol `Buat Pengajuan` menyediakan pilihan terpisah untuk Reimbursement dan Cash Advance. Reimbursement membuka form yang tersedia; Cash Advance membuka halaman terpisah yang menjelaskan form belum tersedia sampai workflow-nya diimplementasikan.
- Dropdown `Buat Pengajuan` menampilkan kedua tipe sebagai pilihan yang mudah dibedakan, dengan ikon berwarna, deskripsi singkat, hover/focus state, dan tata letak yang tetap nyaman pada layar kecil.
- Pemilihan tipe tidak mengubah permission saat ini; permission dan workflow Cash Advance dapat dipisahkan saat fitur pengajuan Cash Advance dibangun.
- Pada viewport desktop lebar (minimal 1400 px), field jenis, status, kategori, rentang tanggal, dan jumlah baris tersusun di baris pertama; aksi Reset/Terapkan berada rata kiri di baris kedua.
- Lebar kolom mengikuti isi: pilihan kategori/status dan rentang tanggal tetap mudah digunakan, sementara jumlah baris dan aksi tidak mengambil ruang berlebih.
- Pada viewport yang lebih kecil, filter tetap membungkus secara responsif dan ringkasan klaim tidak terpengaruh.
- Kolom Kategori pada tabel daftar dibatasi sekitar 19% lebar tabel (maksimum 290 px) dan badge kategori membungkus; tabel tetap dapat digeser horizontal pada viewport sempit.
- Kolom Status dibatasi sekitar 17% (maksimum 275 px) dan ruang tersisa dialokasikan ke kolom Aksi agar beberapa tombol tidak bertumpuk.
- Tombol dalam kolom Aksi selalu satu baris; kolom memakai seperempat lebar tabel dan lebar minimum 360 px agar tombol rata kanan memiliki ruang yang cukup.
- Semua tombol per baris, termasuk Edit/Hapus draft yang dirender dinamis, berada dalam satu flex container sehingga aturan sebaris dan jarak vertikal konsisten.

## Acceptance criteria persetujuan klaim

- Panel detail klaim menonjolkan keperluan, jenis pengajuan, cakupan, referensi project/nomor PO bila relevan, kategori, total, serta daftar rincian biaya.
- Informasi konteks klaim disajikan compact tanpa kartu terpisah per field; keperluan tetap mudah ditemukan.
- Bukti nota per rincian dibuka dalam modal berjudul “Preview Bukti Dokumen”, dengan informasi rincian di panel kiri dan viewer file di panel kanan.
- Preview tetap memakai endpoint streaming privat yang memeriksa keterkaitan rincian dengan klaim dan kewenangan pemohon/approver; modal menyesuaikan layar kecil.
- Preview foto menyediakan kontrol zoom in, zoom out, dan sesuaikan pada form pengajuan/edit serta halaman detail; kontrol hanya tampil untuk gambar, bukan PDF.
- Halaman detail approval reimbursement menampilkan accordion riwayat/timeline tepat di bawah Rincian biaya dengan kondisi awal tertutup. Timeline tampil bagi setiap pengguna yang lolos otorisasi halaman detail persetujuan; tidak ada permission audit tambahan untuk menampilkannya.
- Header timeline memakai ikon dan tata letak compact dengan latar gradasi yang halus.
- Preview bukti gambar pada detail persetujuan (termasuk tahap Finance) menyediakan zoom in, zoom out, dan sesuaikan; kontrol tidak muncul untuk PDF.
- Timestamp approval event disimpan konsisten dalam UTC dan dikonversi ke zona waktu aplikasi saat dibaca, agar urutan kronologis timeline akurat.
- Halaman detail approval reimbursement tidak menampilkan bagian Dokumen pendukung umum; bukti hanya ditampilkan per rincian biaya.
- Atasan pada tahap `pending_manager`, approver Finance pada tahap `pending_finance`, dan Approver final pada tahap `pending_final` dapat meminta revisi dengan catatan wajib dan tipe `substantive` atau `administrative`.
- Revisi substansi dari tahap mana pun kembali ke approval Atasan, lalu Finance dan Approver final sesuai ambang; revisi administrasi kembali ke tahap yang meminta revisi (Atasan, Finance, atau Approver final).
- Accordion pilihan revisi di tahap Approver final menjelaskan bahwa revisi substansi memulai ulang pemeriksaan Atasan/Finance, sementara revisi administrasi kembali langsung ke Approver final dengan approval Atasan/Finance tetap tercatat.
- Pemohon dapat mengubah header, rincian biaya, dan bukti lalu mengirim kembali claim yang sama dengan nomor pengajuan yang sama.
- Setelah pengiriman ulang revisi administrasi Approver final, status kembali ke `pending_final` dan Approver final menerima notifikasi; pengiriman ulang revisi substansi kembali ke `pending_manager`.
- Setiap pengiriman ulang menyimpan snapshot claim/rincian sebelumnya, mempertahankan approval event, dan tidak menghapus file bukti lama; rincian yang dikeluarkan dari pengajuan ditandai tidak aktif.
- Detail pengajuan menampilkan riwayat permintaan revisi, keputusan approval, pengiriman ulang, serta snapshot sebelum revisi.

Keputusan teknis lampiran: [Unggah bukti reimbursement](hris-reimbursement-attachment-upload-decision.md).

## Aturan bisnis

- Satu pengajuan memiliki satu perusahaan, satu karyawan, dan satu nomor pengajuan.
- Pada form self-service, karyawan selalu diambil dari employee yang terhubung ke
  akun login; user tidak memilih karyawan dari dropdown.
- Form menampilkan atasan langsung dan approver Finance perusahaan sebagai informasi
  jalur approval.
- Halaman pengajuan ini khusus untuk reimbursement; jenis pengajuan ditampilkan
  sebagai informasi tetap dan tidak dapat dipilih pengguna. Cash advance memiliki
  form dan alur pertanggungjawaban tersendiri pada fase berikutnya.
- Pengajuan reimbursement dapat memuat beberapa rincian biaya.
- Setiap rincian biaya wajib mencantumkan tanggal nota.
- Setiap rincian biaya wajib menyertakan file bukti nota. Pengguna dapat memilih file dari perangkat atau mengambil foto melalui kamera pada perangkat yang mendukung.
- Semua gambar bukti dari kamera maupun galeri dioptimalkan di browser sebelum upload dengan batas dimensi bertahap hingga 1.600 px dan target ukuran sekitar 1,5 MB, menggunakan kompresi JPEG yang menjaga keterbacaan; PDF tidak diubah. Jika browser tidak mendukung pemrosesan atau hasilnya lebih besar, file asli tetap digunakan.
- Zoom 100% pada preview berarti Sesuaikan ke area viewer; kontrol zoom memperbesar gambar relatif terhadap ukuran pas layar dan area preview tetap dapat digeser.
- Bukti diunggah satu per satu setelah validasi client-side berhasil ke prefix private MinIO; setiap file maksimal 10 MB dan total seluruh bukti maksimal 50 MB per pengajuan.
- Preview bukti nota pada form edit draft menampilkan rincian biaya pada panel kiri dan gambar/PDF pada panel kanan dalam dialog yang sama; pada layar kecil panel ditumpuk agar tetap dapat digunakan.
- Halaman detail reimbursement menampilkan tanggal kegiatan per rincian dan aksi Preview Bukti Dokumen yang membuka dialog verifikasi dua panel dengan data rincian di kiri dan dokumen di kanan.
- Referensi opportunity menggunakan UUID CRM dengan panjang maksimum 36 karakter; validasi request dan kolom `claims.opportunity_id` harus kompatibel dengan UUID tersebut.
- Konteks pada detail reimbursement menampilkan client dari account opportunity atau client project, serta kontak opportunity jika tersedia.
- Route detail reimbursement tetap menandai menu sidebar “Reimbursement & Cash Advance” aktif dan membuka grup “Manajemen Claim”.
- Form edit dan pengajuan menampilkan pengingat yang menonjol di bawah accordion informasi karyawan/alur persetujuan agar bukti fisik disimpan dan diserahkan ke Finance pada akhir bulan pengajuan.
- Label status klaim `pending_manager` ditampilkan sebagai “Menunggu persetujuan atasan” dan `pending_finance` sebagai “Menunggu persetujuan Finance”; nilai status internal tetap.
- Daftar klaim menyediakan tombol Detail untuk setiap pengajuan yang dapat diakses pengguna, berdampingan dengan aksi status seperti pembayaran atau edit draft.
- Daftar pribadi Reimbursement & Cash Advance menampilkan kolom Nomor dengan elemen `<code>` seperti tabel Pengajuan Lembur; nomor kosong pada draft ditampilkan sebagai `—`, dan filter, sorting, aksi, serta pagination tetap berfungsi dengan kolom tambahan.
- Reimbursement menyediakan tombol Cetak pada daftar pribadi, ringkasan perusahaan, dan halaman detail hanya jika pengguna memiliki hak cetak (otomatis untuk pemilik/pemohon) dan pengajuan sudah melewati seluruh tahap approval. Jika approval final diwajibkan, status `pending_final` belum boleh dicetak; endpoint cetak dan endpoint pratinjau lampiran juga menolak akses pada status tersebut. Dokumen cetak memakai nama perusahaan sebagai kop, nomor pengajuan, status terkini, ringkasan, rincian biaya, total, dan waktu persetujuan Finance.
- Grup permission `Approval & ringkasan` menyediakan hak `print_hris_reimbursement`, `print_hris_cash_advance`, `print_hris_overtime`, dan `print_hris_leave`, yang ditampilkan sebagai identifier teknis konsisten dengan permission lain seperti `view_hris_approvals`. Hak print mengatur visibilitas tombol dan akses endpoint bagi non-pemilik dalam scope perusahaan; pemilik/pemohon tetap otomatis boleh mencetak tanpa permission print. Permission Cash Advance, Lembur, dan Cuti disediakan untuk alur cetak masing-masing.
- Permission cetak tidak diberikan otomatis ke role `admin` atau `hris_admin`; akses non-pemilik, termasuk `super_admin`, hanya tersedia bila permission jenis pengajuan diberikan eksplisit dan pengguna berada dalam scope perusahaan. Pemilik/pemohon tetap otomatis dapat mencetak pengajuan miliknya. Draft tidak dapat dicetak.
- Dokumen cetak menampilkan tanggal pengajuan pertama (tetap setelah revisi), nama approver Atasan dan Finance beserta waktu persetujuan, serta tanda centang hijau pada blok persetujuan yang sudah diberikan.
- Nama dan waktu approver hanya ditampilkan pada blok persetujuan footer agar tidak berulang pada ringkasan; footer juga menyediakan ruang tanda tangan pemohon beserta nama dan waktu pengajuan.
- Nama perusahaan hanya tampil pada kop dokumen dan tidak diulang dalam ringkasan.
- Tabel `hris_claims` menyimpan `submitted_at`; migration mengisi pengajuan lama non-draft dari `created_at` dan tidak mengubah waktu tersebut saat revisi.
- Route cetak memakai policy `view` klaim yang sama dan hanya menyajikan reimbursement yang telah melewati seluruh approval (status `approved`, `pending_payment`, `paid`, atau `completed`); status `pending_final` ditolak sampai approver final memutuskan. Halaman menyediakan dialog cetak browser dan format ramah kertas.
- Pratinjau dan hasil cetak klaim memakai ukuran A4; lampiran gambar mengalir secara proporsional dengan batas tinggi 90 mm dan dapat berbagi halaman agar hemat kertas.
- PDF reimbursement dirender sebagai halaman individual melalui PDF.js lokal: setiap halaman dokumen menjadi satu lembar A4 baru, tanpa menyematkan PDF viewer. Sumber file tetap endpoint privat terotorisasi dan tombol cetak menunggu seluruh halaman selesai dirender.
- Tinggi setiap lembar PDF cetak harus sama dengan area konten A4 setelah margin halaman, agar page break tidak menghasilkan halaman kosong tambahan.
- Lembar PDF menjadi elemen halaman mandiri dan tidak bersarang di dalam wadah halaman lampiran lain, sehingga pemisah halaman tidak menggandakan halaman kosong.
- Lampiran gambar mengalir berurutan dan berbagi ruang pada halaman A4 yang sama bila masih cukup; tiap gambar tidak dipaksa memulai halaman baru.
- Lampiran gambar berurutan ditampilkan di dalam satu lembar preview bersama yang sama seperti hasil cetak; rangkaian PDF tetap menjadi lembar terpisah.
- Token unggahan sementara terikat pada pengguna dan hanya dapat dipakai untuk pengajuan miliknya; setelah disimpan, object disalin ke prefix permanen klaim dan file temporary dihapus.
- Temporary proof uploads memakai TTL 30 menit dan dibersihkan oleh scheduler setiap 10 menit.
- Total pengajuan dihitung dari rincian biaya di server, bukan dari input total manual.
- Nominal pada tiap rincian menerima angka maksimal dua desimal dan ditampilkan dalam format lokal Indonesia, misalnya `1.234,56`; server menormalkan nilai sebelum validasi.
- Referensi nota bersifat opsional, menampilkan label Opsional, dan memberi contoh nomor nota pada placeholder.
- Aksi Simpan draft menyimpan status `draft`, sedangkan hanya aksi Kirim pengajuan yang berpindah ke `pending_manager`; pilihan aksi harus tetap terkirim saat unggah bukti dilakukan lebih dahulu.
- Setelah Simpan draft pada form baru, pemohon diarahkan ke halaman edit draft yang baru dibuat; menyimpan ulang draft pada halaman edit tetap di halaman tersebut dan menampilkan pesan keberhasilan pada form.
- Pesan status pada form reimbursement hanya tampil sekali di dalam form, tanpa duplikasi pada banner global halaman.
- Tabel daftar klaim menggunakan ruang kolom aksi yang ringkas agar badge status panjang tetap tampil satu baris.
- Rincian biaya menggunakan panel accordion yang dapat dibuka dan ditutup secara mandiri: rincian pertama terbuka saat form dimuat, panel lain menampilkan ringkasan tanggal nota, kategori, nominal, dan status bukti ketika ditutup, serta validasi otomatis membuka rincian yang perlu diperbaiki tanpa menutup panel lain.
- Header rincian dan kontrol bukti memakai jarak yang cukup, tombol bergaya lembut dengan ikon tindakan, serta indikator fokus yang tetap terlihat.
- Draft dapat dibuka pada halaman detail, diedit kembali, atau dihapus oleh pemohon.
- Simpan draft dan Kirim pengajuan menampilkan indikator loading selama unggah bukti dan pengiriman form berlangsung.
- Alur dasar adalah Atasan -> Finance -> Disetujui -> Menunggu pembayaran -> Dibayar -> Selesai.
- Approval tambahan dapat diarahkan ke approver final berdasarkan threshold nominal yang aktif pada perusahaan pengajuan.
- Form konfigurasi workflow claim menyediakan accordion informasi ringkas yang menjelaskan perbedaan tugas Finance dan Approver final serta hubungan tahap final dengan ambang nominal perusahaan.
- Jika threshold tambahan tidak dikonfigurasi, pengajuan tidak memiliki tahap approval tambahan.
- Jika threshold terpenuhi tetapi approver final tidak dikonfigurasi, pengajuan ditolak pada boundary dan tidak boleh melewati approval.
- Metode pembayaran hanya dapat dicatat oleh Finance setelah seluruh approval selesai.
- Semua query, approval, dan pembayaran wajib menggunakan company scope pengajuan.
- Pengajuan cash advance tetap dapat masuk tahap pertanggungjawaban setelah pencairan.

## Field fase pertama

Header: nomor pengajuan, jenis reimbursement (informasi tetap), karyawan, perusahaan,
project, client, PO number (khusus keperluan project/client), tujuan/keterangan.

Rincian: tanggal nota wajib, tanggal kegiatan opsional, kategori pilihan dari master,
deskripsi, mata uang, nominal, referensi nota opsional, dan satu file bukti wajib.

Pembayaran: metode pembayaran, penerima, bank, nomor rekening, tanggal pembayaran,
nominal dibayar, referensi pembayaran, dan bukti pembayaran.

## Acceptance criteria

- User dapat membuat reimbursement dengan minimal satu rincian biaya.
- Tanggal nota wajib diisi pada setiap rincian dan divalidasi di browser serta server.
- Setiap rincian meminta satu bukti nota; mobile menyediakan aksi pilih file dan ambil foto.
- Bukti nota yang sudah dipilih/tersimpan menyediakan tombol Preview di samping Hapus dan dibuka dalam modal popup; akses bukti tersimpan tetap melewati endpoint privat terotorisasi.
- Berkas bukti hanya tersimpan di MinIO private dan dapat diunduh oleh pemohon, reviewer yang berwenang, atau pengguna dengan permission klaim dalam scope perusahaan.
- Upload per-file dibatasi ukuran; kegagalan unggah tidak mengirim file lain dalam batch yang sama, dan file temporary tertinggal dibersihkan setelah TTL.
- User tidak perlu memilih karyawan dan sistem menggunakan employee dari sesi login.
- Form menampilkan nama atasan dan Finance approver yang aktif dalam scope perusahaan.
- Form hanya menampilkan informasi tetap “Reimbursement” tanpa pilihan jenis pengajuan;
  informasi ini berada di panel kiri.
- Pilihan keperluan internal atau project/client tampil sebagai kartu pilihan yang jelas,
  responsif, dan menunjukkan pilihan aktif.
- Nomor PO hanya tampil dan dapat diisi ketika keperluan project atau client dipilih.
- Keterangan umum berada di panel kanan, tepat setelah pilihan keperluan.
- Keterangan umum bersifat opsional dan tetap kosong jika pemohon tidak mengisinya; sistem tidak menyalin deskripsi rincian pertama ke field ini.
- Field lokasi kegiatan tidak ditampilkan pada form.
- Server menolak nominal rincian nol/negatif dan menghitung total secara konsisten.
- User tidak dapat memilih karyawan di luar company scope.
- Khusus form pengajuan/revisi reimbursement, dropdown Project dan Opportunity
  mengabaikan company scope dan membership sehingga referensi lintas perusahaan
  tersedia. Permission lihat modul (`view_project` atau `view_crm_opportunity`)
  tetap diwajibkan.
- Validasi client-side memastikan tipe dan ID referensi dipilih dengan format yang
  benar. Server memverifikasi keberadaan record dan permission lihat tanpa menolak
  referensi karena company scope; pengecualian ini tidak mengubah scope modul lain.
- Pengajuan baru masuk `pending_manager`, bukan `submitted`.
- Approval atasan mengubah status menjadi `pending_finance`; bila meminta revisi, pemohon dapat memperbaiki pengajuan dan mengirim ulang sesuai tipe revisi.
- Approval Finance mengubah status menjadi `approved` jika tidak ada threshold final.
- Approval Finance mengubah status menjadi `pending_final` jika threshold aktif terpenuhi.
- Admin dapat memahami bahwa Finance memeriksa claim setelah Atasan, sedangkan Approver final memberi persetujuan tambahan setelah Finance untuk nominal yang memenuhi ambang.
- Pengajuan dengan threshold terpenuhi tanpa approver final aktif tidak dapat disetujui.
- Approver final yang bukan konfigurasi perusahaan tidak dapat memproses pengajuan.
- Finance dapat mencatat pembayaran hanya pada pengajuan berstatus `approved` atau `pending_payment`.
- Setelah pembayaran valid dicatat, status menjadi `paid`, lalu dapat ditandai `completed`.
- Aksi approval dan pembayaran mencatat approval/audit event tanpa menyimpan isi dokumen sensitif di log.
- Form memiliki validasi, empty state, dan pesan error yang dapat dibaca.
- Aksi Simpan draft dan Kirim pengajuan menjalankan validasi client-side sebelum form dikirim; server tetap memvalidasi ulang semua input pada kedua aksi.
- Simpan draft dengan lampiran tetap berstatus `draft`; hanya tombol Kirim pengajuan yang memulai approval `pending_manager`.
- Daftar klaim menyediakan detail, edit draft, dan hapus draft; halaman detail draft hanya dapat diakses pemohon atau super admin.
- Ikon pada menu aksi daftar klaim membedakan makna tindakan: Detail memakai ikon lihat, Edit draft memakai ikon edit, dan Hapus draft memakai ikon hapus dengan aksen merah.
- Aksi detail, edit, dan hapus draft hanya ditampilkan pada baris draft di daftar; tidak ada panel ringkasan draft terpisah di atas daftar.
- Saat mengirim draft, validasi client-side harus lolos sebelum dialog konfirmasi tampil; pengiriman baru dilakukan setelah pengguna menyetujui dialog.
- Simpan draft dan Kirim pengajuan menampilkan popup loading selama proses penyimpanan berlangsung.
- Textarea Keterangan umum dan Deskripsi pada rincian memiliki tinggi awal minimum dua baris dan dapat diperbesar pengguna.
- Validasi rincian mencakup minimal satu item, kategori master, nominal positif maksimal dua desimal, deskripsi wajib, mata uang IDR saja, serta pilihan project/opportunity yang diwajibkan sesuai referensi.
- Kategori biaya dipilih dari master Pengaturan HRIS; kategori tidak diketik bebas.
- Nominal rincian menerima angka dengan maksimal dua desimal, ditampilkan dalam format lokal Indonesia (contoh `1.234,56`), dan dinormalisasi sebelum validasi/penyimpanan.
- Referensi nota bersifat opsional, berlabel Opsional, dan menyediakan contoh nomor nota sebagai placeholder.

## Acceptance criteria daftar klaim

- Daftar klaim menyediakan filter dropdown multi-select untuk status, jenis pengajuan, dan kategori, serta rentang tanggal pengajuan default dari awal bulan berjalan sampai hari ini.
- Status `needs_revision` tersedia pada filter dan daftar; revisi hanya dapat diminta Atasan atau Finance dan pemohon dapat mengirim ulang setelah memperbaiki data.
- Form revisi claim menyediakan aksi Simpan perubahan yang mempertahankan status `needs_revision`, catatan revisi, dan tahap approver agar pemohon dapat melanjutkan nanti.
- Aksi Kirim revisi memperbarui pengajuan dan memulai kembali approval hanya setelah pengguna menyetujui dialog konfirmasi.
- Detail claim menampilkan riwayat workflow kronologis dari pengajuan pertama, termasuk semua permintaan revisi, kirim ulang, dan keputusan tahap approval; identitas pelaku, komentar, dan snapshot revisi mengikuti permission audit trail claim.
- Kolom kategori pada daftar klaim menampilkan kategori rincian yang unik per pengajuan; kategori sama pada beberapa rincian ditampilkan satu kali.

