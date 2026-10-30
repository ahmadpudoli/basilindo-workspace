# Preview dokumen pada revisi pengajuan sakit

## Perubahan

- Form revisi menampilkan informasi dokumen pendukung yang sudah tersimpan.
- PDF/gambar dapat dipreview melalui dialog tanpa upload ulang.
- Upload file baru tetap opsional untuk mengganti dokumen lama.
- Validasi hanya menolak revisi sakit jika tidak ada file baru dan dokumen lama juga tidak tersedia.
- Field `supporting_document` tidak lagi diteruskan ke update model; hanya object key `supporting_document_id` yang disimpan.

## Verifikasi

- PHP lint dan pemeriksaan perubahan dijalankan setelah implementasi.
