# Company role filter

Tanggal: 2026-10-21

## Perubahan

- Menambahkan filter multi-select peran bisnis pada daftar Company.
- Peran yang tersedia: operasional, client, vendor, dan partner.
- Filter menggunakan query yang sama dengan search, klasifikasi, status, sorting, dan pagination.
- Interaksi tetap tanpa refresh halaman melalui endpoint partial server-side.

## Verifikasi

- PHP lint `CompanyController` berhasil.
- Blade cache berhasil.
- Frontend production build berhasil.
