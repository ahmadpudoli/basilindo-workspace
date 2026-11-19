# Master bank untuk rekening pembayaran HRIS

Tanggal: 2026-11-19

## Perubahan

- Menambahkan master `core_banks` dengan primary key varchar, kode transfer tiga karakter sebagai teks, BIC, nama, status aktif, serta 123 entri dari tabel kode transfer bertanggal 31 Maret 2026.
- Menambahkan relasi bank dan kode snapshot pada rekening pembayaran HRIS. Data rekening lama tetap utuh dan dibackfill hanya saat nama cocok tepat setelah normalisasi.
- Mengganti input nama bank menjadi dropdown master pada panel gaji dan reimbursement, menampilkan kode bank, dan memvalidasi bank aktif di browser serta server.
- Dropdown bank menyediakan pencarian nama/singkatan; label utama menampilkan nama (singkatan). Kode transfer dihilangkan dari UI sesuai arahan terbaru, tetapi tetap disimpan pada master dan snapshot.
- Ringkasan rekening aktif dan status setiap versi diberi badge yang kontras; tabel riwayat memakai layout sesuai lebar panel agar status rekening tidak terpotong.
- Nomor rekening aktif maupun versi riwayat dapat diungkap pemegang permission lihat setelah verifikasi password pada popup; endpoint memeriksa employee scope, membatasi percobaan, mengirim no-cache, dan UI membersihkan nomor setelah 20 detik/penutupan.
- Alur isi otomatis dari payroll juga meminta password dan mengambil data melalui endpoint terotorisasi/no-cache; nomor rekening tidak lagi disisipkan pada atribut HTML tombol.
- Simpan pertama meminta konfirmasi; penggantian rekening aktif meminta konfirmasi lalu password akun. Server menegakkan password hanya bila sudah ada versi aktif; validasi client tetap dijalankan sebelum dialog bersama.

## Verifikasi lanjutan

- Sintaks controller dan routes lolos `php -l`; route reveal dan enrichment terdaftar; Blade berhasil dikompilasi dengan `php artisan view:cache`.
- `git diff --check` tidak menemukan whitespace error. Build Vite belum dapat dijalankan karena proses tidak diizinkan menulis file sementara di `workspace/node_modules/.vite-temp` (EPERM); tidak ada test aplikasi yang dijalankan.
- Menghubungkan tombol isi otomatis rekening reimbursement ke pilihan bank payroll yang sama.
- Mencatat cakupan dan sumber daftar pada `docs/decisions/hris-bank-master-source.md`.

## Verifikasi

- Kedua migration berhasil diterapkan secara incremental pada database lokal.
- `php -l` berhasil untuk dua migration, model bank, model rekening, dan controller.
- Tinker tidak dapat dijalankan karena PsySH mencoba menulis history di direktori profil di luar workspace; tidak ada pengujian aplikasi yang dijalankan.

## Catatan

- Daftar merupakan snapshot kode transfer bank Indonesia dari sumber yang dicatat pada keputusan. Unit usaha syariah yang tercantum sebagai entri tersendiri disertakan; Bank Indonesia dikecualikan sebagai bukan rekening tujuan biasa.
- `down()` master dan referensi tidak menghapus data agar rollback tidak membuang data master atau riwayat rekening.
