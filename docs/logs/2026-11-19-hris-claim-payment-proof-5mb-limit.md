# Batas 5 MB untuk bukti pembayaran reimbursement

## Perubahan

- Bukti PDF dibatasi maksimum 5 MB di browser dan server.
- Ukuran file gambar dan PDF yang dipilih dibatasi maksimal 5 MB; gambar tetap dikompresi di browser secara bertahap dan server menegakkan batas akhir 5 MB.
- Label popup menjelaskan batas ukuran file yang dikirim.

## Verifikasi

- Meninjau seluruh batas ukuran pada validasi client/server dan keterangan UI.
- `git diff --check` bersih. Test suite tidak dijalankan.
