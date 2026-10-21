# Requirement: Form dokumen dan dependensi metadata

## Tujuan

Menyederhanakan input dokumen dan memastikan pilihan perusahaan internal, jenis dokumen, counterparty, metadata, nominal, dan tag saling tervalidasi.

## Acceptance criteria

- Form tambah dan edit memiliki pasangan: judul-tanggal, perusahaan internal-jenis dokumen, client/vendor-project, nomor referensi-tag, serta mata uang-nominal.
- CRM account tidak ditampilkan pada form dokumen.
- Counterparty hanya menampilkan perusahaan aktif yang memiliki peran client atau vendor, dapat dicari, dan hanya satu yang dapat dipilih.
- Jenis dokumen dimuat ulang sesuai tipe global, tipe langsung, atau pivot perusahaan internal yang dipilih.
- Jenis dokumen menampilkan indikator loading ketika perusahaan internal berubah.
- Metadata tambahan dimuat setelah tipe dokumen dipilih dan menampilkan indikator loading.
- Nominal menerima format angka Indonesia dan dinormalisasi aman di server.
- Tag dapat ditambahkan dengan Enter, dihapus sebagai chip, dan dikirim sebagai teks dipisahkan koma.
- Validasi client dan server menjaga field wajib, ukuran/jenis file, nominal, scope counterparty, dan kecocokan jenis dokumen.
