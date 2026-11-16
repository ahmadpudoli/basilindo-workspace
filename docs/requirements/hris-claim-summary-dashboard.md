# Requirement ringkasan reimbursement dan cash advance

## Tujuan

Menyajikan ikhtisar pengajuan biaya karyawan yang mudah dibaca pada desktop dan mobile, dengan pemisahan tipe pengajuan dan batas akses sesuai perusahaan pengguna.

## Acceptance criteria

- Route ringkasan Reimbursement dan Cash Advance hanya menampilkan tipe yang dipilih.
- Ringkasan hanya mengambil klaim dari perusahaan yang dapat diakses pengguna; `super_admin` dapat melihat seluruh perusahaan sesuai hak aksesnya.
- Klaim berstatus `draft` tidak muncul di daftar, metrik, opsi status, atau hasil filter ringkasan.
- Ikhtisar menampilkan jumlah pengajuan, jumlah nominal yang dikelompokkan per mata uang (tanpa menjumlahkan mata uang berbeda), jumlah dalam proses, serta jumlah disetujui/dibayar.
- Tabel pengajuan menampilkan identitas karyawan, nomor pengajuan, perusahaan, tanggal, tujuan, kategori, referensi project jika ada, nominal, status, dan tautan detail yang diotorisasi.
- Daftar dan metrik mengikuti periode, perusahaan, status, kategori, kata kunci, rentang nominal, serta pagination yang dipilih; filter dipertahankan di query string.
- Tampilan ringkasan menggunakan tabel yang dapat digeser horizontal pada layar kecil, dengan pagination dan jumlah baris standar 10/25/50/100.
- Tabel memakai lebar kolom eksplisit; Status dibatasi sekitar 10% dan Aksi mendapat sekitar 10% agar tombol tetap muat. Label status panjang membungkus di dalam kolom.
- Kolom Aksi hanya menampilkan tombol dropdown ikon tiga titik vertikal yang dirender sebagai SVG inline; menu bergaya modern menyediakan ikon SVG untuk detail dan cetak.
- Menu Cetak hanya muncul untuk reimbursement yang sudah memenuhi seluruh syarat izin cetak dan dibuka pada tab baru.
- Menu aksi ditempatkan pada document body dengan posisi viewport agar tidak terpotong container tabel/scroll, dan menyesuaikan posisi saat viewport bergulir atau berubah ukuran.
- Empty state menjelaskan bahwa tidak ada pengajuan pada filter yang aktif dan draft memang tidak ditampilkan.
- Header ringkasan compact dengan aksen warna, empat metrik disatukan dalam satu panel tipis, dan form filter memakai grid responsif dengan tombol Reset/Terapkan sejajar.
- Header ikhtisar tidak menampilkan badge nama perusahaan; filter Perusahaan menyediakan multi-select dropdown yang hanya berisi perusahaan dalam scope pengguna dan mempertahankan pilihan melalui query string.
- Tombol Reset dan Terapkan berdempetan sebagai satu grup aksi.
- Pencarian kata kunci menyaring nama karyawan, nomor pengajuan, label/kode status, dan kategori rincian.
- Filter rentang nominal minimum/maksimum menyaring total pengajuan, memvalidasi nilai numerik non-negatif, dan mengikuti seluruh agregat serta pagination.
- Filter disusun dalam dua baris di desktop lebar dan membungkus pada viewport yang lebih sempit.
- Periode tanggal pada filter terisi otomatis dari awal bulan berjalan hingga hari ini, selaras dengan rentang pada ikhtisar.
- Tombol Reset/Terapkan berada tepat setelah kolom nominal maksimum pada desktop lebar.
- Pada layar kecil, tabel tetap dapat digulir horizontal tanpa kolom kategori, nominal, status, dan aksi saling menimpa; tombol aksi dan dropdown menu memakai ukuran compact.

## Keputusan

Nominal total dikelompokkan per mata uang karena kurs konversi tidak tersedia pada data pengajuan. Scope memakai relasi perusahaan user yang telah dipakai route ringkasan saat ini.
