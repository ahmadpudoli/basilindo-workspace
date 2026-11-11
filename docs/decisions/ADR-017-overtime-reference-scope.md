# ADR-017: Scope referensi pekerjaan lembur

Tanggal: 2026-11-10

Dropdown Project dan Opportunity pada pengajuan lembur mengikuti perusahaan yang dapat
diakses oleh user melalui pengaturan akses data perusahaan. `employee.company_id` tetap
menjadi perusahaan pemilik pengajuan, tetapi tidak membatasi daftar referensi pekerjaan.

Validasi server menggunakan scope yang sama. Project tetap memerlukan permission dan
membership project; opportunity memerlukan permission melihat opportunity CRM.
