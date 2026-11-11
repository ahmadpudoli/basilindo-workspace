# CRM Account index: filter, pagination, dan relasi

## Perubahan

- Menambahkan filter server-side untuk pencarian nama, tipe account, status, perusahaan pemilik, serta client/partner.
- Menambahkan pilihan jumlah baris `10`, `25`, `50`, dan `100` dengan default `10` serta mempertahankan state melalui query string.
- Menambahkan sorting server-side pada nama, tipe, dan status dengan indikator arah sorting.
- Menampilkan kontak account, perusahaan client/partner (`party_company`), dan perusahaan pemilik/scope (`company`).

## Verifikasi

- Controller memakai whitelist kolom sorting dan ukuran halaman.
- Query account menggunakan eager loading untuk menghindari N+1 pada kontak dan perusahaan.
- Data integrasi tetap dibaca dari relasi yang sudah ada; tidak ada migration atau perubahan data master pada pekerjaan ini.

## Risiko tersisa

- Daftar perusahaan pada filter mengikuti data `core_companies` yang tersedia dan permission scope yang sudah diterapkan pada model/policy.
