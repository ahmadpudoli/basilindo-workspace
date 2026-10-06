# Penyelarasan master perusahaan dan multi-company

## Perubahan

- Menetapkan `companies` sebagai master tunggal untuk Group Basilindo, perusahaan internal, client, vendor, dan partner.
- Memperjelas label dan kolom `Klasifikasi` serta `Peran bisnis` pada menu Master Perusahaan.
- Menambahkan filter klasifikasi dan peran bisnis pada master perusahaan.
- Menyembunyikan menu Vendor legacy dari navigasi tanpa menghapus tabel/data lama.
- Memperjelas relation manager assignment banyak perusahaan pada user dan hak akses per company.
- Memperbaiki modal assignment agar field perusahaan benar-benar tampil; sebelumnya custom form hanya menampilkan pilihan role tanpa pilihan perusahaan.
- Mengganti label aksi lanjutan menjadi `Tambahkan & Tambahkan Lainnya` dan menambahkan panduan tiga scope role di modal.
- Memperbaiki format panduan role dari teks multiline menjadi HTML bullet list agar tampil terstruktur di Filament Placeholder.
- Membatasi pilihan assignment user hanya ke perusahaan internal (`classification=internal`, `entity_type=company`); Group Basilindo dan pihak eksternal tidak dapat dipilih.
- Memaksa tabel perusahaan user melakukan reset/reload setelah AttachAction berhasil, termasuk aksi tambah berikutnya.
- Menghapus relasi `companies` yang mungkin tercache pada owner user sebelum reset agar hasil terbaru langsung diambil dari database.
- Mengubah form tambah perusahaan dari modal penuh menjadi slide-over agar tabel dan hasil penambahan tetap terlihat saat menambahkan beberapa perusahaan.
- Memperkuat refresh setelah aksi `attachAnother` dengan me-refresh owner user dari database sebelum reset tabel.
- Mengubah panduan role menjadi kartu informasi responsif dengan ringkasan dan tooltip per role agar lebih mudah dipindai di modal.
- Memperbarui domain model dan checklist.

## Verifikasi

- Memeriksa bahwa `company_user` dan relasi `User::companies()` tetap menjadi sumber scope akses.
- Memeriksa bahwa document query/policy sudah membatasi data berdasarkan company yang dimiliki user.
- Pemeriksaan test penuh masih memiliki kegagalan setup database duplicate constraint/table pada koneksi `core`, yang tidak disebabkan perubahan UI ini.

## Risiko tersisa

- `vendor_id` dan tabel/model Vendor lama masih dipertahankan untuk kompatibilitas. Migrasi data dokumen ke `document_parties` dengan role `vendor` perlu dijadwalkan sebagai pekerjaan terpisah sebelum legacy schema dihapus.
- `scope_role` sudah dicatat per user-perusahaan, tetapi policy granular untuk membedakan aksi ketiga role masih perlu ditambahkan.
