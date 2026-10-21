# Validasi peran perusahaan operasional

Tanggal: 2026-10-21

## Perubahan

- Menonaktifkan pilihan `Perusahaan operasional` saat klasifikasi Company diubah menjadi External.
- Menghapus pilihan tersebut dari state form ketika klasifikasi berubah.
- Menambahkan validasi server-side agar kombinasi External dan `operating_company` ditolak.
- Menambahkan pesan validasi pada form dan dokumentasi aturan bisnis.

## Verifikasi

- PHP lint berhasil.
- Blade cache berhasil.
- Frontend production build berhasil.
