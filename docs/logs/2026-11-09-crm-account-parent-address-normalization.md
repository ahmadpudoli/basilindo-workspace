# CRM account parent company dan alamat Odoo

## Perubahan

- Datasource Odoo kini mengambil alamat partner serta nama state dan country.
- Account perorangan yang memiliki `parent_id` Odoo dihubungkan ke perusahaan asal melalui `party_company_id`.
- Account lama yang sebelumnya terbentuk tanpa tipe atau relasi perusahaan direkonsiliasi menggunakan external ID Odoo pada metadata.
- Alamat account disimpan pada CRM account; alamat perusahaan client/partner disimpan pada `core_companies`.
- Detail account menampilkan alamat account dan alamat client/partner.
- Constraint party company diperbaiki: satu perusahaan hanya memiliki satu CRM account bertipe perusahaan, tetapi dapat memiliki banyak account bertipe perorangan.

## Verifikasi

- Migration alamat dan constraint berjalan berhasil.
- Initial sync Odoo dijalankan ulang: 172 record diproses dan 0 gagal.
- `Ibu Khaira` terdeteksi sebagai `person`, memiliki `party_company_id`, dan alamat `Jakarta` tersimpan.
- Syntax PHP dan kompilasi Blade berhasil.

## Catatan data sumber

Pada database Odoo yang sedang terhubung, `Ibu Khaira` memiliki `parent_id` ke `PT. JFX Technologi`. Jika yang diharapkan adalah `PT CYTEC SOLUSI GLOBAL`, relasi parent pada record Odoo perlu diperiksa karena sinkronisasi mengikuti `parent_id` Odoo sebagai sumber kebenaran.
