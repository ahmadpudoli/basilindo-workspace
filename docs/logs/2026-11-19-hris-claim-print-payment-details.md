# Rincian pembayaran pada cetakan reimbursement

- Menambahkan status pembayaran ke bagian informasi pengajuan.
- Cetakan menampilkan detail transaksi dan melampirkan bukti pembayaran hanya untuk pemegang `process_hris_payment` atau `super_admin`; bukti ditempatkan setelah lampiran rincian biaya.
- Gambar bukti pembayaran mengikuti layout lampiran gambar, sedangkan PDF menggunakan renderer PDF.js halaman cetak yang sudah ada.
- Verifikasi: lint PHP, kompilasi Blade, dan pemeriksaan diff whitespace.
