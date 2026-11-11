# Scope referensi pekerjaan lembur

## Perubahan

- Project pada form lembur kini mengambil semua perusahaan yang diberikan pada akses user,
  bukan hanya perusahaan employee.
- Opportunity memakai daftar perusahaan user yang sama, termasuk opportunity yang company-nya
  diturunkan dari account.
- Validasi penyimpanan menggunakan scope user yang sama dengan dropdown.

## Verifikasi

- Ditambahkan coverage test untuk project lintas perusahaan yang dapat diakses user.
- Ditambahkan coverage test opportunity dari perusahaan yang berbeda dengan perusahaan employee.
