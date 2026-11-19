# Pencatatan pembayaran reimbursement dari ringkasan

## Perubahan

- Menambahkan aksi Catat pembayaran pada menu tiap baris reimbursement yang sudah disetujui dan berstatus menunggu pembayaran.
- Menambahkan modal di halaman ringkasan. Pengiriman form memakai AJAX, menampilkan validasi server, memperbarui badge status menjadi Dibayar tanpa reload, dan menyediakan aksi untuk lanjut ke pembayaran berikutnya.
- Membatasi permission `process_hris_payment` ke `finance_admin` dan `super_admin`; mencabutnya dari role `admin`. Pemeriksaan permission dan scope perusahaan tetap dijalankan di endpoint.
- Migration permission membersihkan cache Spatie setelah provisioning agar permission baru langsung bisa dipakai oleh form pengelolaan pengguna dan role.
- Mengunci record klaim saat transaksi pencatatan agar pembayaran bersamaan tidak tercatat dua kali.

## Verifikasi

- Meninjau route, otorisasi, validasi field pembayaran, tabel ringkasan, dan interaksi menu aksi.
- Migration permission incremental diterapkan ke database development.
- Test suite tidak dijalankan.

## Catatan

- Metode pembayaran, penerima, bank/rekening untuk transfer, nominal, tanggal, referensi, dan ID bukti dicatat pada modal. Unggah bukti pembayaran belum ditambahkan; field ID bukti tetap opsional sesuai kontrak endpoint yang sudah tersedia.
