# Persistent queue worker untuk local development

## Perubahan

- Mengganti `php artisan queue:listen --tries=1` pada script `composer run dev` menjadi `php artisan queue:work --tries=1 --timeout=0`.
- Queue worker sekarang tetap menunggu job dan tidak berhenti karena timeout idle 60 detik.

## Verifikasi

- Memeriksa konfigurasi `composer.json` dan environment queue Redis.
- Server Laravel dan Vite tetap dijalankan oleh `composer run dev`.

## Risiko tersisa

- Redis tetap harus aktif pada host dan port yang dikonfigurasi di `.env`.
- `--timeout=0` membuat worker tidak menghentikan job yang macet; job berat perlu timeout/job-level handling yang sesuai.
