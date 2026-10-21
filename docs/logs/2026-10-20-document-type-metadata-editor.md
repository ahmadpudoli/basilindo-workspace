# Document type metadata editor

## Perubahan

- Mengembalikan editor visual metadata pada form tipe dokumen.
- Mendukung tambah metadata, nama field, tipe input, status wajib, dan hapus baris.
- Menambahkan drag-and-drop native untuk mengatur urutan field.
- Menyimpan hasil editor ke format `required_fields` JSON yang kompatibel dengan backend dan format metadata lama.
- Menambahkan empty state, focus state, hover state, dan dark theme.

## Verifikasi

- `npm.cmd run build` berhasil.
- `php artisan view:cache` berhasil.
- `git diff --check` berhasil.
