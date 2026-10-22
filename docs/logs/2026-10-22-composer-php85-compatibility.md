# Composer dan kompatibilitas PHP 8.5

## Perubahan

- Meninjau hasil `composer update` pada `workspace`.
- Mengubah konfigurasi opsi SSL PDO MySQL/MariaDB agar memakai `Pdo\\Mysql::ATTR_SSL_CA` pada PHP 8.5 dan fallback ke constant lama pada PHP yang lebih lama.
- Tidak melakukan upgrade major `maatwebsite/excel` secara otomatis karena aplikasi memiliki import/export ticket yang perlu regression test.

## Verifikasi

- Composer global diperbarui dari 2.8.11 ke 2.10.3; deprecation notice `$http_response_header` tidak muncul lagi.
- `maatwebsite/excel` dinaikkan ke `4.0.5` dan `phpoffice/phpspreadsheet` ke `5.10.0` agar kompatibel dengan PHP 8.5.
- `php artisan package:discover --ansi` berhasil tanpa warning `PDO::MYSQL_ATTR_SSL_CA`.
- `composer validate --no-check-publish` berhasil.
- `composer update --dry-run --no-scripts --no-install` sekarang gagal pada resolusi dependency karena `maatwebsite/excel` 3.x membutuhkan PhpSpreadsheet 1.x yang belum mendukung PHP 8.5.
- Setelah upgrade, `composer update maatwebsite/excel phpoffice/phpspreadsheet -W --no-cache` berhasil dan `composer audit` menyatakan tidak ada advisory.
- `composer audit` masih melaporkan 10 advisory pada `maatwebsite/excel 3.1.68` dan `phpoffice/phpspreadsheet 1.30.0`.

## Risiko dan tindak lanjut

- PHP 8.5 tidak memenuhi constraint patch `phpoffice/phpspreadsheet 1.30.7`; Composer menahan versi lama.
- API import/export ticket perlu regression test lanjutan terhadap `TicketsImport`, `TicketsExport`, dan `TicketTemplateExport` setelah upgrade major.
