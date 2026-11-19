# Ukuran kertas pada pratinjau cetak reimbursement

- Menambahkan dropdown ukuran A4, Letter, Legal, dan A3 di samping tombol cetak.
- Perubahan pilihan langsung memperbarui proporsi lembar ringkasan dan lampiran, tinggi halaman cetak, serta ukuran `@page` yang dikirim ke dialog printer.
- Tinggi preview setiap lembar sekarang diatur eksplisit dari rasio dan lebar halaman aktual, diperbarui saat pilihan kertas atau ukuran viewport berubah.
- Verifikasi: `node --check resources/js/claim-print.js`, kompilasi Blade, dan pemeriksaan diff whitespace berhasil. `npm.cmd run build` belum dapat berjalan karena Vite ditolak Windows (`EPERM`) saat menulis file sementara di `node_modules/.vite-temp`.
