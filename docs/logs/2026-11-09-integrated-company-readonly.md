# Log pekerjaan — Perusahaan hasil integrasi Odoo read-only

## Perubahan

- Company yang memiliki `is_integrated = true` atau `source_system` tidak dapat diarsipkan melalui policy.
- Field sumber Odoo pada form Company dikunci; field konfigurasi lokal seperti role, parent, status, visibilitas, dan logo tetap dapat dikelola.
- Halaman detail dan daftar perusahaan menampilkan penanda `Odoo · field sumber terkunci`.
- CRM Account, Contact, Opportunity, dan Project menerapkan whitelist field lokal pada endpoint update sehingga field sumber tidak dapat diubah melalui request manual.
- Data manual tetap dapat dikelola sesuai permission yang berlaku.

## Verifikasi

- Route edit/update/delete tetap dilindungi oleh `CompanyPolicy`.
- View Blade dikompilasi ulang untuk memastikan kondisi penanda sumber valid.

## Catatan

- Perubahan nama, alamat, kode, dan atribut lain untuk data Odoo harus dilakukan di Odoo lalu diperbarui melalui sinkronisasi.
