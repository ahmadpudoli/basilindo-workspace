# Requirement Reimbursement HRIS

Status: implementasi fase awal

## Tujuan

Menyediakan pengajuan reimbursement berbasis rincian biaya dengan approval
berjenjang sesuai scope perusahaan dan proses pembayaran terpisah oleh Finance.

## Acceptance criteria daftar klaim

- Pada viewport desktop lebar (minimal 1400 px), filter jenis, status, kategori, rentang tanggal, jumlah baris, dan aksi Reset/Terapkan tersusun dalam satu baris tanpa kolom kosong di antaranya.
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
- Atasan pada tahap `pending_manager` dan approver Finance pada tahap `pending_finance` dapat meminta revisi dengan catatan wajib dan tipe `substantive` atau `administrative`.
- Revisi substansi kembali ke approval Atasan; revisi administrasi kembali ke tahap yang meminta revisi (Atasan atau Finance).
- Pemohon dapat mengubah header, rincian biaya, dan bukti lalu mengirim kembali claim yang sama dengan nomor pengajuan yang sama.
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
- Reimbursement yang sudah disetujui Finance menyediakan tombol Cetak pada daftar dan detail; dokumen cetak memakai nama perusahaan sebagai kop, nomor pengajuan, status terkini, ringkasan, rincian biaya, total, dan waktu persetujuan Finance.
- Dokumen cetak menampilkan tanggal pengajuan pertama (tetap setelah revisi), nama approver Atasan dan Finance beserta waktu persetujuan, serta tanda centang hijau pada blok persetujuan yang sudah diberikan.
- Nama dan waktu approver hanya ditampilkan pada blok persetujuan footer agar tidak berulang pada ringkasan; footer juga menyediakan ruang tanda tangan pemohon beserta nama dan waktu pengajuan.
- Nama perusahaan hanya tampil pada kop dokumen dan tidak diulang dalam ringkasan.
- Tabel `hris_claims` menyimpan `submitted_at`; migration mengisi pengajuan lama non-draft dari `created_at` dan tidak mengubah waktu tersebut saat revisi.
- Route cetak memakai policy `view` klaim yang sama dan hanya menyajikan reimbursement yang telah melewati persetujuan Finance; halaman menyediakan dialog cetak browser dan format ramah kertas.
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
- Draft dapat dibuka pada halaman detail, diedit kembali, atau dihapus oleh pemohon.
- Simpan draft dan Kirim pengajuan menampilkan indikator loading selama unggah bukti dan pengiriman form berlangsung.
- Alur dasar adalah Atasan -> Finance -> Disetujui -> Menunggu pembayaran -> Dibayar -> Selesai.
- Approval tambahan dapat diarahkan ke approver final berdasarkan threshold nominal yang aktif pada perusahaan pengajuan.
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
- Field lokasi kegiatan tidak ditampilkan pada form.
- Server menolak nominal rincian nol/negatif dan menghitung total secara konsisten.
- User tidak dapat memilih karyawan atau project di luar company scope.
- Pengajuan baru masuk `pending_manager`, bukan `submitted`.
- Approval atasan mengubah status menjadi `pending_finance`; bila meminta revisi, pemohon dapat memperbaiki pengajuan dan mengirim ulang sesuai tipe revisi.
- Approval Finance mengubah status menjadi `approved` jika tidak ada threshold final.
- Approval Finance mengubah status menjadi `pending_final` jika threshold aktif terpenuhi.
- Pengajuan dengan threshold terpenuhi tanpa approver final aktif tidak dapat disetujui.
- Approver final yang bukan konfigurasi perusahaan tidak dapat memproses pengajuan.
- Finance dapat mencatat pembayaran hanya pada pengajuan berstatus `approved` atau `pending_payment`.
- Setelah pembayaran valid dicatat, status menjadi `paid`, lalu dapat ditandai `completed`.
- Aksi approval dan pembayaran mencatat approval/audit event tanpa menyimpan isi dokumen sensitif di log.
- Form memiliki validasi, empty state, dan pesan error yang dapat dibaca.
- Aksi Simpan draft dan Kirim pengajuan menjalankan validasi client-side sebelum form dikirim; server tetap memvalidasi ulang semua input pada kedua aksi.
- Simpan draft dengan lampiran tetap berstatus `draft`; hanya tombol Kirim pengajuan yang memulai approval `pending_manager`.
- Daftar klaim menyediakan detail, edit draft, dan hapus draft; halaman detail draft hanya dapat diakses pemohon atau super admin.
- Aksi detail, edit, dan hapus draft hanya ditampilkan pada baris draft di daftar; tidak ada panel ringkasan draft terpisah di atas daftar.
- Saat mengirim draft, validasi client-side harus lolos sebelum dialog konfirmasi tampil; pengiriman baru dilakukan setelah pengguna menyetujui dialog.
- Simpan draft dan Kirim pengajuan menampilkan popup loading selama proses penyimpanan berlangsung.
- Textarea Keterangan umum dan Deskripsi pada rincian memiliki tinggi awal minimum dua baris dan dapat diperbesar pengguna.
- Validasi rincian mencakup minimal satu item, kategori master, nominal positif maksimal dua desimal, deskripsi wajib, mata uang yang diizinkan dan seragam, serta pilihan project/opportunity yang diwajibkan sesuai referensi.
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

