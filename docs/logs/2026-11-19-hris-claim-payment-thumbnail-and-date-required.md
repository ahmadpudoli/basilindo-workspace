# Thumbnail bukti pembayaran dan tanggal wajib

## Perubahan

- Tanggal pembayaran ditampilkan dengan indikator wajib dan tetap memiliki nilai awal tanggal hari ini; validasi client dan server menolak tanggal kosong.
- Native file input diganti area pilih file yang menampilkan thumbnail gambar, nama, ukuran, dan aksi ganti/hapus. PDF menampilkan preview tile khusus.
- Pemilihan gambar tetap dikompresi di browser sebelum pengiriman; file privat dan batas validasi server tidak berubah.

## Verifikasi

- Meninjau markup popup, validasi tanggal, preview object URL, reset preview saat mengganti pengajuan, dan input multipart.
- `git diff --check` bersih. Test suite tidak dijalankan.
