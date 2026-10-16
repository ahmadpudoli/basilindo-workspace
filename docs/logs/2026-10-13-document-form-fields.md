# Document form fields — 2026-10-13

## Perubahan

- Menambahkan kolom `documents.description` nullable melalui migration reversible.
- Menambahkan field `Deskripsi Dokumen` menggunakan `RichEditor` pada panel `File dan informasi utama`.
- Menjadikan `Tanggal dokumen` wajib dan menempatkannya sebagai field pertama pada panel `Detail dokumen`.
- Mengubah `Mata uang` menjadi dropdown opsional dengan pilihan `IDR`, `USD`, dan `SGD`.
- Menjadikan `Perusahaan client/vendor` opsional; dokumen dapat dibuat tanpa pihak eksternal.
- Menambahkan deskripsi ke model dan halaman detail dokumen.

## Verifikasi

- Migration dijalankan pada connection `documents`.
- Form validation memastikan tanggal dokumen tidak boleh kosong.
- Nilai mata uang tidak dipaksakan bila dokumen tidak memiliki informasi nominal.

## Risiko tersisa

- Migration perlu dijalankan pada environment deployment sebelum form digunakan.
