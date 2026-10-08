# Monitoring dan observability

## Health checks

- HTTP `/up` untuk proses Laravel.
- HTTP `/health/ready` untuk readiness database, queue/cache, dan object storage.
- `/health/ready` mengembalikan `503` jika komponen kritis gagal; response tidak
  memuat detail exception, credential, connection string, atau data bisnis.
- PostgreSQL dan Redis memakai health check Docker Compose.
- MinIO memakai `mc ready local` pada Compose.
- Pantau queue worker dan `failed_jobs`.

## Sinyal minimum

- HTTP 5xx dan latency `/up`, login SSO, upload, download, dan bundle.
- Queue depth, oldest job, failed jobs, retry count.
- PostgreSQL connection, transaction error, disk usage, dan backup age.
- MinIO error rate, object storage usage, lifecycle cleanup, dan checksum failure.
- Audit event untuk login, permission, upload/download, verification, bundle, dan purge.

Log tidak boleh berisi token, secret, isi dokumen, OCR sensitif, atau nama file
yang bersifat rahasia. Gunakan request ID dan entity ID bila diperlukan.
