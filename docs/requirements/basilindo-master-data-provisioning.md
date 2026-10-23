# Provisioning master data Basilindo

## Tujuan

Deployment awal Workspace menyediakan master perusahaan Basilindo, role/permission baseline, dan satu akun sistem super admin.

## Aturan

- Provisioning dilakukan melalui migration incremental yang idempotent.
- Perusahaan internal memiliki `classification=internal`, status aktif, dan role `operating_company`; Group Basilindo menjadi parent perusahaan internal.
- Role dan permission diprovision secara additive agar data akses existing tidak dicabut saat upgrade.
- `admin@basilindo.co.id` menjadi satu-satunya akun sistem dengan role `super_admin`, relasi Group Basilindo dan perusahaan internal aktif yang memiliki parent Group Basilindo, serta password awal `password` hanya saat akun baru atau migrasi legacy dilakukan.
- Perusahaan di luar struktur internal Group Basilindo, termasuk data uji seperti `TEST1`, tidak boleh terhubung ke akun sistem.
- Akun sistem tidak dapat dihapus dan permission, role, serta company scope-nya dikunci pada form administrasi.
- Tidak ada bypass company scope baru di policy; akses data tetap berasal dari relasi user–company.
