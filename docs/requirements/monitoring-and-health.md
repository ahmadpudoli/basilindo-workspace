# Requirement: health dan monitoring Workspace

## Tujuan

Operator harus dapat membedakan aplikasi hidup dari aplikasi yang siap menerima
traffic. Endpoint readiness hanya mengembalikan status komponen teknis, tanpa
credential, connection string, nama perusahaan, atau data dokumen.

## Acceptance criteria

- `GET /health/ready` memeriksa database, queue/cache Redis bila digunakan, dan
  disk object storage.
- Response `200` hanya ketika seluruh check kritis sehat; response `503` bila
  ada check kritis gagal.
- Queue melaporkan depth dan jumlah `failed_jobs` tanpa payload job.
- Detail error internal tidak dikirim ke client dan tidak ditulis ke log.
- `/up` tetap menjadi liveness check ringan Laravel.
- Test mencakup healthy path dan failure path database.

## Keputusan

Readiness memeriksa koneksi S3 dengan `HeadBucket`, bukan upload object probe,
agar health check tidak membuat object sampah atau mengubah data bisnis.
