# Informasi pembayaran pada detail ringkasan reimbursement

Tanggal: 2026-11-19

## Perubahan

- Detail klaim yang dibuka dari ringkasan menampilkan snapshot pembayaran setelah status pembayaran `paid`.
- Panel mencakup metode, penerima, detail rekening transfer atau sumber kas kecil, nominal, tanggal, referensi, dan status bukti.
- Controller dan Blade membatasi panel kepada `process_hris_payment` atau `super_admin`; detail tidak ditampilkan pada halaman detail biasa.

## Verifikasi

- Sintaks controller dan kompilasi Blade diperiksa.
- Test aplikasi tidak dijalankan.
