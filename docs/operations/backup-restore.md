# Runbook backup dan restore

## Scope

Backup mencakup PostgreSQL metadata (`core_*` dan `fo_*`), Redis hanya sebagai
cache/queue yang dapat dibangun ulang, serta bucket MinIO private untuk binary
dokumen dan bundle sementara.

## Backup PostgreSQL

Jalankan dari host backup dengan credential secret manager:

```bash
pg_dump --format=custom --no-owner --file=basilindo-workspace-<timestamp>.dump "$DATABASE_URL"
```

Simpan file terenkripsi, checksum, tanggal expiry, dan metadata environment.
Jangan menaruh dump di repository atau bucket public.

## Backup MinIO

Gunakan `mc mirror` ke target backup private dengan versioning/lifecycle yang
sesuai kebijakan retensi:

```bash
mc mirror --overwrite basilindo-workspace/documents backup/basilindo-workspace/documents
```

## Restore drill

1. Siapkan database dan bucket kosong pada environment staging terisolasi.
2. Restore dump dengan `pg_restore --clean --if-exists`.
3. Restore object dengan `mc mirror` dari backup.
4. Jalankan `php artisan migrate:status` dan migration yang belum diterapkan.
5. Jalankan test suite, cek `/up`, login SSO, download dokumen, dan generate bundle.
6. Catat waktu recovery, hasil checksum, dan discrepancy.

Restore production membutuhkan approval pemilik data dan tidak boleh menimpa
database aktif tanpa snapshot/rollback point.
