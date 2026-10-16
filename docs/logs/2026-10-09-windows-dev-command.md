# Perbaikan command development Windows

## Perubahan

- Menghapus Pail dari `composer run dev` karena Pail membutuhkan ekstensi `pcntl` yang tidak tersedia pada PHP Windows.
- Menambahkan command terpisah `composer run logs` untuk Linux/WSL atau environment yang menyediakan `pcntl`.

## Verifikasi

- `composer validate` dijalankan terhadap `app/workspace/composer.json`.
