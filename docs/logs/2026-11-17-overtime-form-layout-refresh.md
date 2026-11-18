# Penyegaran layout form lembur

- Menata ulang form menjadi kolom kiri untuk pilihan jenis, nomor pengajuan, dan accordion informasi karyawan/alur approval; rincian lembur berada di kolom kanan.
- Mengubah pilihan jenis lembur menjadi kartu ringkas dengan ikon rumah dan project memakai style kartu reimbursement.
- Menggunakan accordion informasi karyawan dan approver dengan pola visual yang sama dengan form reimbursement.
- Memperbaiki wrapper kartu agar grid dua kolom tidak dibatasi ulang oleh kolom Bootstrap; teks pilihan kini mendapat lebar kartu penuh.
- Memindahkan panel nomor pengajuan ke urutan pertama pada kolom kiri, sebelum pilihan jenis lembur.
- Memberi jarak aman pada label Jenis lembur dan mengecilkan padding, ikon, serta tipografi kartu pilihan.
- Merapikan accordion informasi karyawan: ringkasan identitas dibuat lebih padat, sedangkan atasan dan HR approver dibedakan dengan aksen biru dan ungu yang lembut.
- Mengubah tombol Batal, Simpan draft, dan Kirim pada footer form menjadi ukuran small.
- Memperbarui tombol Ajukan Lembur di halaman daftar dengan ikon tambah dalam lingkaran, gradasi biru, dan state hover/focus yang jelas.
- Membatasi dropdown Opportunity lembur ke scope perusahaan pemilik; perusahaan Account hanya menjadi fallback jika Opportunity tidak memiliki perusahaan pemilik, sesuai policy akses.
- Blade dikompilasi; test suite tidak dijalankan.
