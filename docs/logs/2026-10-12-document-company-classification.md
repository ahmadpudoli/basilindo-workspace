# Log pekerjaan — Klasifikasi perusahaan pada dokumen

## Perubahan

- `company_id` pada dokumen ditegaskan sebagai perusahaan internal pemilik/
  penanggung jawab dokumen.
- Menambahkan `counterparty_company_id` untuk perusahaan client/vendor.
- Pilihan perusahaan internal hanya menampilkan perusahaan `internal` aktif
  yang berada dalam scope perusahaan user; `super_admin` dapat melihat semua
  perusahaan internal.
- Pilihan perusahaan client/vendor menampilkan perusahaan eksternal aktif yang
  memiliki role `client` atau `vendor`.
- Menghapus input `CRM Account`, `CRM Opportunity`, dan `Vendor` dari form
  dokumen karena tidak diperlukan untuk klasifikasi dokumen yang diminta.
- Relasi dan kolom CRM/vendor lama dipertahankan untuk kompatibilitas data,
  tetapi tidak lagi menjadi pilihan utama pada form dokumen.

## Verifikasi

- Migration development berhasil.
- `php artisan test`: 26 test, 75 assertion lulus.
- PHP lint dan cache clear berhasil.
