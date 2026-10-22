# Perbaikan umpan balik validasi upload dokumen

## Perubahan

- Menampilkan seluruh pesan validasi server pada form upload dokumen.
- Menampilkan pesan khusus di bawah input file dan field metadata.
- Memastikan kondisi `upload` kosong terlihat jelas setelah redirect validasi; browser tidak dapat mengisi ulang file yang sudah dipilih.
- Menormalkan metadata boolean dari checkbox unchecked (`0`) menjadi `false` sebelum validasi dan penyimpanan.

## Verifikasi

- Form berhasil dikompilasi melalui `php artisan view:cache`.
- Validasi metadata `angka_1`, `angka_2`, tanggal, dan datetime dengan contoh nilai pada form berhasil diuji tanpa error.
- Test boolean unchecked berhasil: `9` test lulus, `18` assertion.

## Data

- Tidak ada perubahan migration, reset database, atau penghapusan data.
