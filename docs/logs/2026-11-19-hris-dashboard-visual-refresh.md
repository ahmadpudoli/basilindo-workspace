# Penyegaran visual dashboard HRIS

Tanggal: 2026-11-19

## Perubahan

- Menambahkan hero dashboard dengan identitas HRIS, aksen warna modern, dan
  pintasan aksi yang tetap mengikuti permission pengguna.
- Mengubah kartu statistik menjadi metrik berwarna lembut dengan ikon dan
  keterangan konteks; memperbarui hierarki panel karyawan serta approval.
- Menambahkan styling responsif, avatar inisial, gaya nomor karyawan, dan
  state kosong yang lebih jelas.
- Menindaklanjuti tampilan pada ringkasan pribadi: hero, kartu statistik
  beraksen, dan ajakan membuat pengajuan kini memakai tata visual yang lebih
  hangat serta responsif.
- Memperbaiki kontras judul hero (putih di atas gradasi biru-teal), memberi
  gradasi pastel berbeda pada kartu metrik, dan memperhalus gradasi panel aksi.
- Menambahkan pintasan personal untuk Cuti/Sakit, Lembur, dan Reimbursement
  berdasarkan permission `create_leave_request`, `create_overtime`, dan
  `create_claim`; panel early warning tetap bersyarat pada daftar approval
  yang benar-benar tersedia bagi pengguna.
- Memperjelas label pintasan pertama menjadi Cuti/Sakit secara visual dan
  merapatkan seluruh tombol dalam satu kelompok agar tidak tampak sebagai
  tombol yang terpisah-pisah.
- Memadatkan hero, kartu metrik, dan panel tindakan; tombol kini memiliki ikon
  kalender, jam, dan dokumen serta tersusun sebagai grid responsif untuk layar
  mobile.
- Mengganti panel tindakan terpisah dengan kartu ringkas per jenis pengajuan.
  Jumlah/judul membuka daftar jika permission melihat daftar tersedia, sementara
  tombol Ajukan di dalam kartu mengikuti permission create. Ikon SVG digunakan
  untuk kalender, lembur, reimbursement, dan aksi tambah.
- Memperjelas angka KPI sebagai “Semua periode”; query ringkasan saat ini
  memang menghitung semua pengajuan pemilik tanpa filter bulan atau tahun.
- Memperbarui requirement dan checklist dashboard HRIS.

## Definisi KPI personal

- Mengganti KPI semua-periode dengan metrik sesuai kebutuhan per kategori:
  cuti/sakit pada tahun aktif (jumlah pengajuan, hari disetujui, sisa kuota),
  lembur bulan berjalan (pengajuan, approval, durasi rincian disetujui), dan
  reimbursement bulan berjalan (jumlah serta nominal disetujui).
- Link daftar meneruskan filter tahun/bulan yang didukung dan rincian tetap
  dibatasi pada pengajuan milik pengguna.
- Memperbaiki urutan inisialisasi query KPI lembur dan reimbursement agar
  keduanya didefinisikan sebelum dipakai pada hitungan bulanan.

## Penyegaran halaman Biodata

- Mengganti detail biodata menjadi grid field compact dengan label/nilai yang
  jelas, status berbahasa Indonesia, dan aksen gradasi lembut.
- Memadatkan padding, avatar, node, dan indentasi tree organisasi; penanda
  “Anda”, buka/tutup native, dan fokus keyboard tetap tersedia pada layout mobile.
- Menyederhanakan grid biodata dengan menghapus kotak dan latar individual per
  field; pemisah tipis dan hover lembut menjaga pemindaian tetap jelas.
- Meratakan node tree organisasi: bingkai hanya tersisa pada panel terluar,
  sedangkan indentasi dan garis cabang halus menjaga relasi hierarki.

## Verifikasi

- Pemeriksaan perubahan dilakukan secara statis pada Blade dan dokumentasi.
- Test suite/browser tidak dijalankan.

## Risiko tersisa

- Verifikasi visual langsung di browser belum dilakukan.
# Penyempurnaan KPI daftar lembur

- Menghilangkan tampilan kartu berbingkai di dalam panel ringkasan lembur.
- Lima metrik kini tampil sebagai satu baris ringkasan datar dengan pemisah tipis; warna hanya menjadi aksen pada nilai status.
- Tata letak tablet dan mobile tetap responsif dengan jarak yang lebih ringkas.
- Panel luar memakai gradasi diagonal biru–teal–ungu dan aksen cahaya tipis, mengikuti karakter kartu Dashboard tanpa menambah kartu di dalamnya.
- Pada layar mobile, pemisah horizontal ditambahkan untuk setiap baris metrik agar susunan dua kolom lebih mudah dipindai; ornamen lingkaran dibuat lebih lembut.
- Memulihkan garis vertikal pada kolom kanan baris kedua mobile yang sebelumnya tertimpa aturan breakpoint tablet.
- Menyamakan panel KPI halaman Cuti & Sakit dengan pola satu kartu gradasi biru–putih–pastel pada halaman Lembur; aksi buka rincian per metrik dan perilaku collapse mobile tetap dipertahankan.
- Memperkuat pemisah kolom kanan pada seluruh baris KPI mobile agar garis “Cuti bersama / Sisa cuti” tetap terlihat.
- Memastikan panel KPI benar-benar tersembunyi saat collapse; aturan grid KPI sebelumnya mengalahkan `display:none` pada panel mobile. Pemisah kolom kini memakai inset divider agar tidak tertimpa border lain.
- Panel KPI cuti/sakit disetel terbuka secara default pada mobile dengan `aria-expanded` dan ikon minus yang sesuai; tombol tetap dapat menutup panel.
- Garis kolom kanan KPI mobile ditandai eksplisit pada metrik kanan agar tampil pada kedua baris.
- Metrik terakhir “Sakit tercatat” membentang dua kolom di mobile agar garis pemisah terakhir selebar panel, mengikuti susunan ringkasan lembur.
- Garis vertikal pemisah KPI mobile diperpanjang penuh dari batas atas ke batas bawah setiap baris agar terhubung visual dengan divider horizontal.
- Panel KPI lembur kini memakai kontrol collapse yang sama dengan ringkasan cuti pada mobile, terbuka secara default; skrip pemindah panel kini memindahkan wrapper beserta tombolnya agar kontrol tetap berfungsi.
- Tab Reimbursement daftar pribadi mendapat panel KPI satu kartu bergaya lembur. Agregat jumlah, status, dan nominal disusun dari query daftar sebelum pagination sehingga mengikuti filter aktif dan scope pemohon.
- Mengurangi padding dan tinggi minimum KPI reimbursement, serta menata ulang rasio kolom filter pada desktop lebar dan menambah ruang atas agar label floating tidak terpotong.
- Atas umpan balik tampilan, KPI reimbursement di mobile kini collapsed secara default. Filter desktop lebar mendapat gutter terkontrol, kolom tanpa overflow, serta ruang label yang lebih lega.
- Mengurangi min-height serta padding panel dan metrik KPI lembur dan cuti agar mengambil ruang vertikal lebih sedikit pada desktop dan mobile.
- Mempertegas container filter reimbursement dengan latar putih kebiruan, border inset, radius, dan bayangan yang terlihat; jarak dari KPI serta ruang label ditambah.
- Menambahkan field kata kunci pada filter daftar claim untuk mencari nama karyawan atau nomor pengajuan; pencarian diterapkan pada hasil sesuai filter dan scope pemohon, serta query tetap terbawa oleh pagination. Tab reimbursement dipertahankan saat Reset.
- Mengatur ulang grid filter desktop lebar untuk menyediakan ruang bagi field pencarian tanpa memotong kontrol.
- Menempatkan field Nama / nomor sebagai filter pertama dan memperlebar kolom Periode sampai pada grid desktop; lebar Bootstrap default dinonaktifkan di grid agar aksi filter tidak terdorong ke baris berikutnya.
- Mengaktifkan CSS grid desktop filter klaim agar ukuran kolom khusus benar-benar diterapkan; kolom Periode sampai kini memiliki lebar 260 px.
- Memastikan lebar 260 px untuk field Periode sampai juga diterapkan pada tab Semua di lebar desktop yang belum mencapai breakpoint grid lebar.
- Setelah review visual menunjukkan field Periode sampai masih sempit, memperkuat selector kolom dan menaikkan lebarnya menjadi 260 px termasuk pada layout grid.
- Memperbaiki tombol Terapkan yang meluber dari panel filter dengan meniadakan margin negatif baris Bootstrap pada grid desktop, mengizinkan kolom menyusut, dan membagi lebar tombol aksi secara seimbang.
- Menambah lebar minimum kolom nama, kategori, periode, dan aksi pada filter desktop agar label/nilai tidak terpotong; placeholder pencarian dipersingkat agar muat.
- Menempatkan tombol Reset dan Terapkan pada baris terpisah di bawah seluruh field filter dengan rata kiri, serta mengurangi padding vertikal container filter.
- Memindahkan aturan ringkas panel dan penghilangan gutter vertikal ke style bersama semua tab claim; sebelumnya aturan tersebut berada di blok khusus tab Reimbursement.
- Menyamakan lebar kolom Periode sampai dengan Periode dari dan menambah lebar minimum kolom Jenis pengajuan serta Kategori; pengaturan lama 260 px khusus Periode sampai dihapus.
- Menyatukan gaya panel filter klaim dengan class umum yang sudah ada `workspace-list-filter-form` di `workspace/resources/css/app.css`; padding vertikal kini konsisten dan dapat dipakai oleh semua halaman daftar.
- Mengembalikan lebar tombol Reset/Terapkan ke ukuran konten di baris aksi bawah agar tidak melebar setengah panel.
- Menyamakan KPI Ringkasan Reimbursement dan Cash Advance dengan panel cuti/lembur menggunakan class global `workspace-overtime-summary-panel` dan `workspace-overtime-summary-card`; varian empat metrik memakai grid 2x2 dan divider mobile dari CSS global.
- Verifikasi: `git diff --check` bersih dan `npm.cmd run build` berhasil; Vite tetap memberi peringatan chunk besar yang sudah ada.
- Build aset front-end berhasil melalui `npm.cmd run build`; aset publik memuat class panel filter bersama yang baru.
- Verifikasi: `git diff --check`.
- Risiko tersisa: tampilan visual belum diperiksa lewat browser pada ukuran layar nyata.
