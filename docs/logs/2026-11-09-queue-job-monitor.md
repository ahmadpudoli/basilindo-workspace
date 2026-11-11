# Queue & Job Monitor — 2026-11-09

## Perubahan

- Menambahkan `core_queue_job_logs` untuk mencatat job queued, processing, succeeded, dan failed.
- Menambahkan listener event queue Laravel tanpa menyimpan payload job yang berpotensi sensitif.
- Menambahkan halaman Core → Pengaturan → Queue & Job dengan jumlah antrean Redis, filter status, pagination 10/25/50/100, waktu eksekusi, attempts, dan error.
- Menambahkan permission `view_queue_jobs` dan akses additive untuk akun Ahmad.

## Verifikasi

- Migration berhasil dijalankan.
- Route queue monitor, PHP lint, dan Blade view cache berhasil diverifikasi.
- Queue worker tetap dijalankan melalui `php artisan queue:work`; histori mulai tercatat untuk job yang diproses setelah fitur ini aktif.
