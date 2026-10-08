# Basilindo Workspace

Platform internal modular monolith untuk Core, Company, CRM, Project Management,
dan Document Management.

## Lingkup aplikasi

- User, role, permission, company scope, dan audit login lokal.
- CRM account/customer, contact, lead, opportunity, dan activity.
- Project, member, task, ticket, milestone, timeline, status, dan project team.
- Katalog perusahaan, proyek, vendor, dan tipe dokumen.
- Upload dokumen ke private MinIO/S3 dengan metadata, checksum, dan versioning.
- Pencarian metadata dengan company/project/vendor/document type scope.
- Relasi antar dokumen seperti PO, invoice, receipt, dan payment proof.
- Review dan verifikasi dengan evidence, discrepancy, dan audit trail.
- Pembuatan bundel ZIP secara asynchronous dengan expiry dan download terotorisasi.
- Satu login lokal Workspace untuk seluruh modul.

Semua modul berjalan dalam satu lifecycle deployment dan satu session authentication.

## Teknologi

- PHP 8.2+
- Laravel 12
- Filament 4
- PostgreSQL
- Redis
- MinIO/S3-compatible private storage

## Setup lokal

1. Salin .env.example menjadi .env.
2. Isi konfigurasi PostgreSQL, Redis, dan MinIO sesuai environment lokal.
3. Jalankan composer install dan npm install.
4. Jalankan php artisan key:generate.
5. Jalankan php artisan migrate --seed.
6. Jalankan npm run build.

Untuk verifikasi:

    php artisan test
    php artisan migrate:fresh --seed
    php artisan optimize:clear
    npm run build

Jangan menyimpan dokumen bisnis pada public/local disk. Disk bisnis harus tetap
mengarah ke object storage private.

URL development utama: `http://localhost:8080`.
