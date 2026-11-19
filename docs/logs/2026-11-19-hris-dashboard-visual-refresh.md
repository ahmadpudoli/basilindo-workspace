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
