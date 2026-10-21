# ADR-011 — Default global untuk visibilitas master perusahaan

Status: accepted — 2026-10-21

## Keputusan

Kolom visibilitas perusahaan operasional bersifat opsional. Tidak adanya baris pada
`core_company_operational_visibility` berarti record perusahaan bersifat global dan dapat
digunakan oleh semua perusahaan operasional, setelah tetap melewati assignment dan policy
user. Jika terdapat satu atau lebih baris, record hanya terlihat pada perusahaan operasional
yang dipilih.

Keputusan ini dipakai agar master data yang memang dapat digunakan lintas perusahaan tidak
perlu dikonfigurasi berulang kali. Default global tidak mengabaikan otorisasi user: user
tanpa assignment perusahaan tetap tidak mendapatkan akses, dan `super_admin` mengikuti
pengecualian policy administrasi.

Scope ini hanya berlaku untuk visibilitas master perusahaan dan selector perusahaan. Data
operasional yang dimiliki oleh perusahaan tertentu tetap menggunakan company scope record
secara langsung untuk mencegah kebocoran data lintas perusahaan.
