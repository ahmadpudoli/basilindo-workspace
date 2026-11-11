# Log pekerjaan — Filter multiple select Account CRM

## Perubahan

- Filter Tipe, Status, Perusahaan Pemilik, dan Client/Partner pada daftar Account CRM diubah menjadi dropdown compact berbasis checkbox yang mendukung multiple select.
- Filter menggunakan array pada query string sehingga beberapa pilihan tetap tersimpan ketika sorting atau pagination digunakan.
- Query Account CRM memakai `whereIn`; pilihan `Belum ditentukan` dapat digabungkan dengan status lain melalui `whereNull` dan `whereIn`.
- Pencarian nama account dan pilihan jumlah baris per halaman tetap single-value.

## Verifikasi

- Validasi sintaks controller PHP.
- Kompilasi cache view Blade.
- Pemeriksaan whitespace pada file yang diubah.

## Catatan

- Tidak ada library baru atau perubahan skema database.
- Nilai filter kosong berarti semua data.
- Dropdown menampilkan ringkasan pilihan dan menyediakan pencarian untuk daftar status/perusahaan yang panjang.
