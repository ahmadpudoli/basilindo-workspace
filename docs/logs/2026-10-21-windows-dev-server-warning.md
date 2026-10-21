# Penghapusan warning development server

## Perubahan

- Menghapus `PHP_CLI_SERVER_WORKERS=4` dari `workspace/.env` dan `workspace/.env.example`.
- Menambahkan requirement dan acceptance criteria untuk development server Windows.

## Verifikasi

- Script `composer run dev` tetap menggunakan Laravel server, queue worker, dan Vite.
- Laravel dapat memakai mode reload tanpa warning worker yang tidak kompatibel.

## Risiko tersisa

- Development server berjalan dengan satu worker, sesuai perilaku reload Laravel.
