# Shared Core Architecture

## Perubahan

- Membuat `core/` sebagai source shared dengan namespace `Core`.
- Memindahkan implementasi model general `User`, `Company`, `Vendor`, `Project`, `Roles`, `Setting`, dan `ExternalIdentity` ke `core/src/Models`.
- Memindahkan factory user dan migration general ke `core`.
- Mendaftarkan PSR-4 `Core` dan `CoreServiceProvider` pada File Organizer serta Project Management.
- Menyediakan alias `App\\Models` sementara agar resource, policy, dan service existing tetap kompatibel.
- Menghapus implementasi/migration shared yang duplikat dari dua aplikasi.
- Membiarkan `project-sso/` tidak berubah.

## Verifikasi

- `composer dump-autoload --no-scripts` berhasil pada File Organizer dan Project Management.
- `composer validate --no-check-publish` berhasil pada kedua aplikasi.
- Pemeriksaan class autoload berhasil untuk `Core\\Models\\User` dan `Core\\CoreServiceProvider` pada kedua aplikasi.
- `php artisan about` belum dapat dipakai pada environment ini karena `file-organizer/bootstrap/cache` dilaporkan tidak writable; Project Management tidak menghasilkan output dalam batas waktu verifikasi.
- Migration existing belum dijalankan karena database aktif belum tersedia pada sesi ini.

## Risiko tersisa

- Migration legacy File Organizer masih memiliki pembersihan schema lama dan perlu diverifikasi pada database existing.
- Alias `App\\Models` adalah compatibility bridge; import baru sebaiknya menggunakan `Core\\Models` secara eksplisit.
- Staging wajib menjalankan `migrate:fresh --seed` dan migration upgrade terhadap database existing sebelum release.

## Perubahan lanjutan

- Kedua aplikasi menggunakan PostgreSQL terpusat `db_file_organize` pada port `5435`.
- Menambahkan koneksi `core` dengan prefix `core_`, prefix default `fo_` untuk File Organizer, dan `pm_` untuk Project Management.
- File Organizer ditetapkan sebagai owner migration core dan Project Management sebagai consumer tabel core.
- Reference lintas prefix menggunakan reference ID tanpa FK lintas koneksi; FK internal tetap dipertahankan.
## Verifikasi prefix

- PHP lint berhasil untuk seluruh migration File Organizer dan Project Management.
- Pemeriksaan tidak menemukan reference constrained('core_*') atau sintaks `$table$table yang dapat menyebabkan prefix ganda.
- Migration database terpusat belum dijalankan; urutan deployment wajib menjalankan migration File Organizer sebagai owner core terlebih dahulu, lalu migration Project Management.

- Migration legacy Project Management yang tersalin di File Organizer dipindahkan secara reversible ke docs/archive/file-organizer-legacy-project-management-migrations; hanya migration domain File Organizer, infrastructure, permission, dan cleanup yang tetap aktif di project tersebut.

## Sentralisasi migration

- Seluruh migration aktif dipindahkan ke core/database/migrations/shared, core/database/migrations/file-organizer, dan core/database/migrations/project-management.
- Directory migration pada kedua aplikasi tidak lagi menjadi source migration aktif.
- CORE_MIGRATIONS_OWNER dan APP_MIGRATION_SET mengatur set migration yang dimuat agar schema shared dan domain tidak dijalankan dua kali.

## Verifikasi database terpusat

- `php artisan migrate:fresh --seed` berhasil dari File Organizer setelah migration proteksi `is_system_account` dibuat aman terhadap kolom yang sudah ada.
- `php artisan migrate --seed` dan `php artisan db:seed` berhasil dari Project Management.
- Shared user dan data admin hanya diseed oleh File Organizer/core; Project Management hanya menjalankan seeder permission/role agar tidak membuat user shared duplikat.
- Index permission dan migration lintas aplikasi menggunakan nama yang diprefix untuk menghindari benturan nama index PostgreSQL.
- `php artisan migrate:status` pada Project Management menunjukkan seluruh migration domain Project Management berstatus `Ran`.

## Koreksi model perusahaan dan pihak transaksi

- Master `companies` sekarang mendukung Group Basilindo, anak perusahaan internal, serta perusahaan eksternal dengan klasifikasi dan role bisnis.
- Relasi parent-child ditambahkan untuk Group Basilindo dan anak perusahaan.
- Relasi `project_parties` dan `document_parties` ditambahkan agar client/vendor menjadi role dalam konteks project atau dokumen, bukan entity vendor duplikat.
- Seeder File Organizer menambahkan Group Basilindo dan enam perusahaan internal: Quinsis, Legospay, LTN, Carano, Magis, dan Mitratama Mahadirga Makmur.
- Tabel `vendors` lama dipertahankan sementara untuk kompatibilitas migration/data existing dan ditandai sebagai legacy dalam dokumentasi.

## Perbaikan relasi lintas koneksi

- Menghapus eager count `documents_count` dari resource Company dan Project karena model shared pada koneksi `core` tidak boleh mengasumsikan tabel domain `fo_documents` sebagai `core_documents`.
- Jumlah dokumen lintas koneksi akan dihitung melalui query/service File Organizer pada tahap berikutnya.
- `php artisan optimize:clear` dan PHP lint untuk resource terkait berhasil.
