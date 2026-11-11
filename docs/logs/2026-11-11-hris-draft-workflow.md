# Log 2026-11-11 — Draft Pengajuan HRIS

## Perubahan

- Menambahkan status `draft` untuk pengajuan lembur, reimbursement/cash advance, dan cuti/sakit.
- Draft dapat disimpan, dilanjutkan, diperbarui, dan dihapus melalui soft delete.
- Draft tidak masuk inbox approval, tidak mengirim notifikasi approval, dan tidak memanggil counter master penomoran.
- Konfirmasi ditampilkan ketika pengguna mengirim draft atau pengajuan baru untuk diproses.
- Nomor resmi dialokasikan melalui `NumberingService` pada saat pengajuan dikirim, sehingga counter bersama tetap aman ketika beberapa pengguna membuat draft secara bersamaan.
- Menambahkan aksi edit/hapus draft pada halaman daftar dan memperbarui relasi child saat draft diperbarui agar tidak meninggalkan rincian biaya atau tanggal cuti yatim.
- Form cuti/sakit kini menggunakan modal konfirmasi template setelah validasi client-side lulus; tombol Simpan draft tidak memicu modal.
- Menambahkan filter target submitter pada mekanisme modal global agar konfirmasi hanya berlaku untuk tombol pengiriman yang ditentukan.
- Merapikan baris tanggal cuti menggunakan grid agar tombol Hapus tetap sejajar di bagian atas saat pesan validasi tampil.
- Memperbaiki layer klik ikon kalender agar tombol ikon menjadi pemicu picker langsung dan picker tidak lagi diposisikan dari input transparan selebar ikon.
- Menambahkan aksi Edit draft dan Hapus draft langsung pada baris tabel di daftar lembur, reimbursement/cash advance, dan cuti/sakit.
- Menghapus eksperimen injeksi aksi tabel berbasis `@json()` yang menyebabkan ParseError Blade; aksi draft tetap tersedia melalui panel draft yang aman di tiga halaman daftar.
- Menambahkan kembali aksi langsung pada baris Draft dengan route JSON sederhana per item, tanpa nested expression Blade, untuk menghindari ParseError.
- Ringkasan cuti/sakit mengecualikan status `draft` dari daftar dan metrik, dengan filter perusahaan tetap diterapkan untuk pengguna non-super-admin.
- Memindahkan data draft reimbursement dari ekspresi kompleks Blade ke controller untuk mencegah ParseError saat membuka form create/edit.
- Memperbaiki identitas aktif sidebar Manajemen Claim dengan key khusus agar Ringkasan Reimbursement, Ringkasan Cash Advance, dan daftar claim tidak aktif bersamaan.
- Memperbaiki layout halaman edit/revisi cuti-sakit: panel alasan revisi kini membentang penuh sebelum dua kolom form, sehingga tidak tersisip sebagai kolom field dan membuat konten berantakan.
- Memperbaiki form edit draft cuti/sakit dengan memindahkan `@method('PUT')` ke dalam body form; sebelumnya directive berada di atribut pembuka dan membuat atribut HTML tampil sebagai teks.
- Edit draft cuti/sakit kini memuat status dokumen dari private storage, menampilkan tautan preview/unduh, dan memperlakukan upload ulang sebagai opsional bila dokumen sebelumnya masih tersedia.
- Preview dokumen pendukung pada edit draft cuti/sakit diubah menjadi popup dialog Bootstrap dengan iframe untuk PDF dan gambar untuk JPG/PNG.
- Panel dokumen tersimpan diringkas menjadi layout horizontal compact dengan metadata file di kiri dan aksi di kanan, serta fallback stacked pada layar kecil.
- Update draft cuti/sakit tanpa file baru kini membawa kembali object key dokumen lama, sehingga proses rekonstruksi draft tidak menghapus referensi lampiran sebelumnya.
- Penyimpanan draft cuti/sakit kini memvalidasi benturan dengan pengajuan aktif dan hari libur, serta mengembalikan pesan konflik ke form sebelum draft disimpan.
- Menyamakan layout form revisi cuti/sakit dengan form create/edit: alert cuti bersama/revisi membentang penuh, kolom form kembali sejajar, dan teks pilihan Cuti/Sakit tidak lagi menempel.
- Mengarahkan halaman edit status `needs_revision` ke template form cuti/sakit yang sama dengan create dan draft edit, dengan tetap mempertahankan action resubmit serta informasi alasan revisi.
- Memperbaiki resubmit revisi cuti/sakit dengan menghapus field kontrol `action` dan input file dari atribut model sebelum `forceFill`, sehingga tidak lagi mencoba menulis kolom database yang tidak tersedia.
- Daftar lembur tidak lagi menampilkan tombol Revisi untuk Draft; status Draft kini menggunakan badge kuning agar berbeda dari status proses lainnya.
- Menambahkan jarak horizontal konsisten pada tombol dan form aksi di tabel HRIS agar kontrol tidak saling menempel.
- Filter status daftar lembur diubah menjadi dropdown multiple select; controller memvalidasi whitelist status dan memakai `whereIn` untuk mempertahankan filter server-side.
- Filter jenis dan status daftar cuti/sakit diubah menjadi dropdown multiple select; controller memvalidasi whitelist nilai dan memakai `whereIn` untuk query server-side.
- Daftar lembur kini memakai default tanggal awal bulan sampai hari ini dan menampilkan ringkasan jumlah pengajuan serta total durasi berdasarkan query filter sebelum pagination.
- Panel ringkasan lembur dipindahkan tepat di atas filter dan dirinci berdasarkan status: total, disetujui, dalam proses, ditolak, dan draft, masing-masing dengan jumlah serta durasi.
- Memisahkan kondisi draft dan revisi pada form lembur agar draft memakai judul edit draft tanpa panel catatan revisi, sedangkan panel tersebut hanya tampil pada status `needs_revision`.
- Memperbaiki redirect resubmit lembur: penyimpanan draft kembali ke daftar dengan pesan pembaruan draft, pengiriman draft memakai pesan pengajuan terkirim, dan hanya resubmit revisi yang kembali ke detail.
- Mengganti native browser confirm pada form lembur dengan dialog konfirmasi template global yang hanya aktif untuk tombol Kirim pengajuan.
- Memperbaiki konflik nomor saat submit draft lembur: draft tidak memakai kembali nomor lama dan allocator memeriksa kandidat terhadap nomor lembur yang sudah tersimpan.
- Menghilangkan panel ringkasan draft di bagian atas daftar lembur agar halaman lebih ringkas; aksi Edit draft dan Hapus draft tetap tersedia pada baris tabel.

## Verifikasi

- Pemeriksaan alur route/controller/service untuk ketiga form.
- PHP lint, kompilasi Blade, route list, dan `git diff --check` dijalankan setelah perubahan.

## Risiko tersisa

- Draft saat ini tetap harus melewati validasi field wajib pada form. Dukungan menyimpan draft yang benar-benar belum lengkap memerlukan aturan validasi kondisional dan penyesuaian kolom database yang masih wajib.
- Nomor draft ditampilkan sebagai “Nomor dibuat saat dikirim”; nomor final dapat berbeda dari draft pengguna lain karena dialokasikan secara atomik ketika submit.
