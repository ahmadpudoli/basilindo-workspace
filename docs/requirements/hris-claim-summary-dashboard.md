# Requirement ringkasan reimbursement dan cash advance

## Tujuan

Menyajikan ikhtisar pengajuan biaya karyawan yang mudah dibaca pada desktop dan mobile, dengan pemisahan tipe pengajuan dan batas akses sesuai perusahaan pengguna.

## Acceptance criteria

- Route ringkasan Reimbursement dan Cash Advance hanya menampilkan tipe yang dipilih.
- Ringkasan hanya mengambil klaim dari perusahaan yang dapat diakses pengguna; `super_admin` dapat melihat seluruh perusahaan sesuai hak aksesnya.
- Klaim berstatus `draft` tidak muncul di daftar, metrik, opsi status, atau hasil filter ringkasan.
- Filter Status pengajuan dan Status pembayaran terpisah. Status pengajuan mengikuti tipe workflow; status pembayaran hanya untuk Reimbursement, bernilai `null` sebelum approval lengkap, otomatis `pending_payment` setelah disetujui, dan menjadi `paid` setelah Finance mencatat pembayaran.
- Status `pending_payment` dan `paid` tidak menjadi nilai `status` pengajuan; status pengajuan Reimbursement tetap `approved` setelah dibayar. Cash Advance tidak menggunakan status pembayaran Reimbursement.
- Ikhtisar menampilkan jumlah pengajuan, jumlah nominal yang dikelompokkan per mata uang (tanpa menjumlahkan mata uang berbeda), jumlah dalam proses, serta jumlah disetujui (atau selesai untuk Cash Advance).
- Tabel pengajuan menampilkan identitas karyawan, nomor pengajuan, perusahaan, tanggal, tujuan, kategori, referensi project jika ada, nominal, Status pengajuan, Status pembayaran, dan tautan detail yang diotorisasi.
- Daftar dan metrik mengikuti periode, perusahaan, kedua status, kategori, kata kunci, rentang nominal, serta pagination yang dipilih; filter dipertahankan di query string.
- Tampilan ringkasan menggunakan tabel yang dapat digeser horizontal pada layar kecil, dengan pagination dan jumlah baris standar 10/25/50/100.
- Tabel memakai lebar kolom eksplisit; Status dibatasi sekitar 10% dan Aksi mendapat sekitar 10% agar tombol tetap muat. Label status panjang membungkus di dalam kolom.
- Kolom Aksi hanya menampilkan tombol dropdown ikon tiga titik vertikal yang dirender sebagai SVG inline; menu bergaya modern menyediakan ikon SVG untuk detail dan cetak.
- Menu Cetak hanya muncul untuk reimbursement yang sudah memenuhi seluruh syarat izin cetak dan dibuka pada tab baru.
- Menu aksi ditempatkan pada document body dengan posisi viewport agar tidak terpotong container tabel/scroll, dan menyesuaikan posisi saat viewport bergulir atau berubah ukuran.
- Empty state menjelaskan bahwa tidak ada pengajuan pada filter yang aktif dan draft memang tidak ditampilkan.
- Header ringkasan compact dengan aksen warna, empat metrik disatukan dalam satu panel tipis, dan form filter memakai grid responsif dengan tombol Reset/Terapkan sejajar.
- Header ikhtisar tidak menampilkan badge nama perusahaan; filter Perusahaan menyediakan multi-select dropdown yang hanya berisi perusahaan dalam scope pengguna dan mempertahankan pilihan melalui query string.
- KPI ringkasan memakai class global `workspace-overtime-summary-panel` dan `workspace-overtime-summary-card` yang sama dengan halaman cuti/lembur; varian empat metrik menampilkan divider yang jelas pada layar kecil.
- Panel KPI ringkasan reimbursement/cash advance memakai komponen global `workspace-mobile-kpi-panel`, dapat dibuka dan ditutup pada mobile dengan kondisi awal collapsed; pada desktop KPI selalu tampil tanpa toggle.
- Saat terbuka di mobile, panel KPI memiliki ruang 8px di atas, 10px di sisi, dan 10px di bawah antara header toggle/container dan kartu metrik.
- Tombol Reset dan Terapkan berdempetan sebagai satu grup aksi.
- Pencarian kata kunci menyaring nama karyawan, nomor pengajuan, label/kode status, dan kategori rincian.
- Filter rentang nominal minimum/maksimum menyaring total pengajuan, memvalidasi nilai numerik non-negatif, dan mengikuti seluruh agregat serta pagination.
- Filter disusun dalam dua baris di desktop lebar dan membungkus pada viewport yang lebih sempit.
- Periode tanggal pada filter terisi otomatis dari awal bulan berjalan hingga hari ini, selaras dengan rentang pada ikhtisar.
- Tombol Reset/Terapkan berada tepat setelah kolom nominal maksimum pada desktop lebar.
- Pada layar kecil, tabel tetap dapat digulir horizontal tanpa kolom kategori, nominal, status, dan aksi saling menimpa; tombol aksi dan dropdown menu memakai ukuran compact.
- Ringkasan Reimbursement menyediakan aksi Catat pembayaran hanya bagi `finance_admin` dan `super_admin`, serta hanya pada pengajuan berstatus disetujui dan menunggu pembayaran.
- Pencatatan pembayaran dilakukan melalui modal pada halaman ringkasan dan dikirim dengan AJAX; validasi dan otorisasi server tetap berlaku, hasil sukses memperbarui status pembayaran di baris tanpa navigasi, dan pengguna dapat lanjut ke pembayaran berikutnya.
- Setelah validasi client lolos, tampilkan indikator loading selama pemrosesan/kompresi bukti dan pengiriman AJAX, lalu sembunyikan setelah response server diterima, baik berhasil maupun gagal.
- Nominal pembayaran memakai format lokal Indonesia (`id-ID`) dengan dua angka desimal. Saat fokus untuk diedit, pemisah ribuan dihilangkan dan input hanya menerima angka serta satu koma desimal; saat fokus keluar, format lokal diterapkan kembali. Jika berbeda dari total pengajuan, tampilkan informasi tepat di bawah input dan minta konfirmasi melalui modal konfirmasi Workspace sebelum mengirim; server tetap memvalidasi nominal sebagai angka positif dan menolak selisih jika penanda konfirmasi tidak dikirim.
- Pencatatan pembayaran mengunci pengajuan di dalam transaksi agar dua permintaan bersamaan tidak menghasilkan pencatatan pembayaran ganda.
- Pada mode ringkasan hanya ada satu tabel ringkasan; aksi Catat pembayaran dari menu baris membuka popup. Tidak ada route/halaman pencatatan pembayaran terpisah.
- Popup mewajibkan Tanggal pembayaran dan menandainya sebagai field wajib; default awal memakai tanggal hari ini.
- Popup mewajibkan unggah bukti pembayaran (JPEG, PNG, WebP, atau PDF) dengan batas file tersimpan/terkirim maksimum 5 MB. Pemilih file bergaya thumbnail menampilkan gambar, nama, ukuran, serta aksi ganti/hapus, sedangkan PDF menampilkan penanda file. Gambar dikompresi di browser sebelum upload; bukti disimpan di disk privat dengan object key acak, sedangkan ID bukti manual tetap opsional dan terpisah.
- Validasi client memeriksa field wajib termasuk tanggal pembayaran, metode, kelengkapan bank untuk transfer, nominal positif, format tanggal, panjang teks, tipe file, dan ukuran sebelum mengirim. Server mengulang validasi otorisasi/scope, field, nominal positif, tipe/ukuran file dan status klaim.

## Keputusan

Nominal total dikelompokkan per mata uang karena kurs konversi tidak tersedia pada data pengajuan. Scope memakai relasi perusahaan user yang telah dipakai route ringkasan saat ini.
