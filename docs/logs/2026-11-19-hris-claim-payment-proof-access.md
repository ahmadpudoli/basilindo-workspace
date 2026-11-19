# Akses bukti pembayaran reimbursement

- Menambahkan endpoint privat untuk preview inline dan unduh bukti pembayaran tersimpan.
- Endpoint mewajibkan permission `process_hris_payment` atau role `super_admin`, pembayaran reimbursement berstatus dibayar, scope perusahaan yang sesuai, dan object bukti yang tersedia. MIME dibatasi ke JPEG, PNG, WebP, dan PDF; response memakai `nosniff` serta cache privat tanpa penyimpanan.
- Detail reimbursement pada ringkasan menampilkan aksi popup preview dan unduh hanya di dalam panel yang sudah dibatasi permission.
- Popup khusus bukti pembayaran memakai layout satu kolom dan ukuran viewer responsif agar gambar/PDF tidak tampil kecil di sisi kiri dialog yang terlalu luas.
- Verifikasi: lint PHP, kompilasi Blade view, dan pemeriksaan diff whitespace.
