# Perbaikan bulk action Project Resource

## Perubahan

- Mengganti `Tables\\Actions\\BulkActionGroup` dan `Tables\\Actions\\DeleteBulkAction` dengan action Filament 4 dari namespace `Filament\\Actions`.
- Menambahkan import eksplisit `BulkActionGroup` dan `DeleteBulkAction`.

## Verifikasi

- Memeriksa resource dan relation manager lain yang telah menggunakan namespace action Filament 4.
- Menjalankan pemeriksaan sintaks PHP setelah perubahan.

## Risiko tersisa

- Tidak ada perubahan pada authorization, query scope, atau data proyek.
