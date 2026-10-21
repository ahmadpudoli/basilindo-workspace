# Requirement: Development server Windows

## Tujuan

Perintah `composer run dev` berjalan tanpa warning worker PHP yang tidak kompatibel dengan mode reload Laravel.

## Acceptance criteria

- `PHP_CLI_SERVER_WORKERS` tidak ditetapkan pada `.env` atau `.env.example` untuk development reload.
- Laravel tetap berjalan pada port 8080.
- Queue worker dan Vite tetap dijalankan oleh `composer run dev`.
- Tidak ada perubahan pada konfigurasi production.
