# Penyegaran visual detail lembur

## Perubahan

- Mempertahankan posisi panel informasi pengajuan, ringkasan, rincian realisasi, dan riwayat workflow.
- Menambahkan aksen warna lembut pada panel informasi, total durasi, status, tabel rincian, dan jenis hari agar tidak tampak pucat.
- Mengganti kartu terpisah per informasi menjadi grid datar dengan separator halus dan ikon kecil.
- Memperjelas referensi pekerjaan dengan label jenis dan nama referensi yang menonjol, serta Client dan perusahaan internal pemilik Project (atau perusahaan internal Opportunity).
- Memuat relasi Project, Client, dan Opportunity secara eager pada detail untuk menghindari query tambahan per entitas.
- Menyiapkan label referensi, Client, dan pemilik di controller agar variabel panel tersedia konsisten saat Blade dirender.
- Mengembalikan Riwayat workflow ke sidebar kanan. Accordion Referensi pekerjaan ditempatkan di bawah Informasi pengajuan dan Ringkasan, selebar area utama di kiri.
- Menyamakan awal vertikal sidebar workflow dengan panel Ringkasan dan menyesuaikan posisinya otomatis ketika pemberitahuan revisi tampil.
- Menghapus gutter kolom desktop pada wrapper sidebar agar tepi atas workflow benar-benar rata dengan Ringkasan; gutter tetap diterapkan kembali pada layout mobile.
- Meringkas tinggi header Informasi pengajuan, Ringkasan, dan Rincian realisasi; judul serta jumlah rincian dipertahankan satu baris.
- Menambahkan identitas karyawan dan NIP ke panel Informasi pengajuan, memperjelas label perusahaan terdaftar, serta merapatkan grid informasinya.
- Mengubah riwayat workflow menjadi accordion berikon dengan timeline semantik untuk pengajuan, persetujuan, penolakan, revisi, dan pengiriman ulang.
- Accordion riwayat workflow terbuka secara default, dapat ditutup, dan mendukung interaksi keyboard melalui kontrol Bootstrap.
- Badge jenis referensi dipindahkan ke sisi kanan header accordion Referensi pekerjaan; accordion referensi kini tertutup secara default.
- Gutter vertikal panel referensi dan workflow dirapatkan pada breakpoint tablet/mobile untuk mengurangi ruang kosong sebelum panel berikutnya.

## Verifikasi

- Pembersihan dan kompilasi cache Blade, pemeriksaan sintaks controller, dan `git diff --check`.
- Test suite tidak dijalankan.

## Risiko tersisa

- Tidak ada perubahan backend atau skema database.
