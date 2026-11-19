# Perbaikan status rekening aktif pada popup pembayaran

Tanggal: 2026-11-19

## Temuan dan perubahan

- Dropdown aksi dipindahkan ke `body` saat dibuka, sehingga JavaScript tidak dapat membaca metadata rekening yang sebelumnya ditempatkan pada baris tabel.
- Metadata rekening dipindahkan ke tombol aksi itu sendiri agar tetap dapat dibaca setelah menu dipindah. Tombol hanya dirender untuk pemegang permission proses pembayaran; nomor lengkap tidak ditanam pada baris umum dan ditampilkan di popup transfer agar Finance dapat melakukan transfer.
- Server tetap mengambil dan memvalidasi rekening aktif secara langsung saat menyimpan pembayaran.

## Verifikasi

- `php artisan view:cache` berhasil.
- `git diff --check` tidak menemukan whitespace error; hanya menampilkan peringatan konversi akhir baris pada file kerja lain.
