# Perbaikan teks dropdown floating label

## Perubahan

- Menetapkan tinggi minimum `48px`, line-height, dan padding vertikal yang
  konsisten untuk select dengan floating label.
- Mencegah teks nilai dropdown terpotong pada bagian bawah field.
- Berlaku global untuk filter dan form yang memakai komponen floating field.
- Menambahkan override final dengan prioritas tinggi karena beberapa rule legacy
  masih mengembalikan padding native select dan menyebabkan teks terpotong.
- Tinggi field dikembalikan menjadi `40px`, setara dengan kontrol filter;
  perbaikan dilakukan melalui line-height dan padding, bukan menambah tinggi.
- Line-height dinaikkan menjadi `1.5` dengan padding vertikal nol agar nilai
  select tetap terpusat dan tidak terpotong.

## Verifikasi

- Rule CSS menggunakan `box-sizing: border-box` agar tinggi field mencakup
  border dan padding.
