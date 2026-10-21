# Requirement: Bootstrap cache view Laravel

## Tujuan

Composer dan Artisan harus dapat melakukan package discovery pada instalasi baru tanpa gagal karena path cache Blade tidak tersedia.

## Acceptance criteria

- Konfigurasi view mendefinisikan resource view aktif dan path compiled view.
- Path compiled view tetap valid ketika folder cache belum pernah berisi file hasil kompilasi.
- Folder cache framework dipertahankan melalui placeholder Git tanpa menyimpan cache runtime.
+ composer dump-autoload dan php artisan package:discover dapat berjalan dari workspace/.
