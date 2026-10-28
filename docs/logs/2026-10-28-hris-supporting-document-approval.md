# Dokumen pendukung pada approval HRIS

## Perubahan

- Menampilkan tombol `Dokumen` pada tabel approval hanya jika object dokumen pendukung benar-benar tersedia.
- Menampilkan tombol `Buka dokumen pendukung` pada popup detail jika file tersedia.
- Menampilkan pesan `Dokumen pendukung tidak tersedia atau belum diupload` jika tidak ada file.
- Menambahkan endpoint streaming private HRIS dengan pengecekan permission approval, company scope, dan tahap approver sebelum file dibuka.

## Verifikasi

- Blade view berhasil dikompilasi.
- Controller dan route menggunakan storage private serta response inline tanpa membuka object key secara langsung.
