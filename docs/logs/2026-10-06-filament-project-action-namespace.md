# Perbaikan action Project Resource

## Perubahan

- Mengganti `Tables\\Actions\\ViewAction` dan `Tables\\Actions\\EditAction` pada `ProjectResource` dengan action Filament 4 dari namespace `Filament\\Actions`.
- Menambahkan import eksplisit `ViewAction` dan `EditAction`.

## Verifikasi

- Memeriksa resource lain di File Organizer yang sudah memakai pola Filament 4.
- Menjalankan pemeriksaan sintaks PHP dan test yang relevan setelah perubahan.

## Risiko tersisa

- Tidak ada perubahan pada authorization, query scope, atau data proyek.
