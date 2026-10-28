# Perbaikan reaktivasi workflow approval HRIS

Tanggal: 2026-10-28

## Perubahan

- Memperbaiki constraint unik lama pada tabel `hris_workflow_approvers` yang masih menganggap record soft-deleted sebagai duplikasi.
- Menambahkan migration incremental untuk menghapus constraint lama dan mempertahankan unique index hanya untuk konfigurasi aktif.
- Saat konfigurasi workflow yang sama sudah diarsipkan, penyimpanan baru akan memulihkan record tersebut dan memperbarui approver, bukan membuat record duplikat.
- Menambahkan feature test untuk skenario arsip lalu aktifkan kembali.

## Verifikasi

- Migration berhasil dijalankan dengan `php artisan migrate --force --no-interaction`.
- Feature test reaktivasi workflow berhasil: 1 test, 3 assertions.
- PHP lint untuk controller, migration, dan test berhasil.

## Catatan

- Warning penulisan cache hasil Pest pada `vendor/pestphp/pest/.temp` tetap berasal dari permission environment lokal dan tidak memengaruhi hasil test.
