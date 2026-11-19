# Validasi dan isi otomatis rekening HRIS

## Perubahan

- Menambahkan validasi client-side interaktif untuk bank, pemilik, dan nomor
  rekening, termasuk pesan pada field, batas karakter, format, dan jumlah digit.
- Menambahkan validasi server-side untuk seluruh field dan normalisasi nomor
  rekening menjadi digit saja sebelum dienkripsi dan disimpan.
- Menambahkan tombol isi otomatis pada panel reimbursement untuk menyalin nama
  bank, pemilik, dan nomor dari rekening gaji aktif. Tombol hanya tersedia bagi
  pengguna dengan izin lihat rekening gaji serta ubah rekening reimbursement.
- Menambahkan instruksi global bahwa validasi setiap field harus ada pada client
  dan server; atribut HTML saja tidak cukup.

## Verifikasi

- Pemeriksaan sintaks PHP dan diff dijalankan; test otomatis tidak dijalankan.
