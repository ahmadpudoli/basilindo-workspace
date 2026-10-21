# Multi-perusahaan pada Tipe Dokumen

## Tujuan

Satu tipe dokumen dapat digunakan oleh beberapa perusahaan internal. Tipe tanpa perusahaan tetap berlaku global.

## Acceptance criteria

- Form tambah dan edit tipe dokumen menyediakan pemilihan beberapa perusahaan.
- Relasi perusahaan disimpan pada pivot `document_type_companies` dan dibaca melalui boundary koneksi dokumen/Core.
- Data `company_id` lama dimigrasikan ke pivot tanpa kehilangan relasi.
- Validasi upload dan edit dokumen menerima tipe jika perusahaan dokumen termasuk dalam relasi tipe atau tipe tersebut global.
- Dropdown perusahaan tetap setinggi input biasa dan teks terpilih menggunakan ellipsis bila terlalu panjang.
