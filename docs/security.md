# Keamanan dan Privasi

## Threat model ringkas

Risiko utama: kebocoran dokumen melalui URL, user melihat company lain, upload file berbahaya, duplicate/replace file tanpa jejak, bundle dapat ditebak, credential MinIO terekspos, serta perubahan status tanpa audit.

## Kontrol wajib

- Private bucket; tidak ada public-read object.
- Temporary signed URL atau streamed download setelah policy check.
- Authorization berlapis: authentication, role/permission, company scope, project scope, dan object-level policy.
- MIME allowlist, ukuran maksimum, extension validation, checksum, nama object acak, dan antivirus hook sebelum `ready` bila tersedia.
- CSRF, secure cookie, session rotation, rate limit login/upload/download, dan security headers.
- Secret hanya melalui environment/secret manager; `.env` tidak di-commit.
- Audit untuk login, upload, replace/version, view/download, relation, verification, bundle, permission, dan delete/restore.
- Jangan menulis nama lengkap dokumen sensitif, token, isi OCR, atau credential ke log.
- PostgreSQL backup terenkripsi dan diuji restore. MinIO lifecycle, replication/backup, dan retention harus terdokumentasi.
- Queue job idempotent; retry tidak boleh membuat duplicate bundle atau mengubah status secara tidak konsisten.

## Data retention

Retention bukan hard-code tanpa keputusan bisnis. Sediakan konfigurasi per company/document type, legal hold, expiry notification, dan approval untuk purge. Detail kebijakan harus disetujui pemilik data/finance/legal.

Upload baru masuk status `quarantine` dan tidak dianggap siap digunakan sebelum direlease oleh workflow terotorisasi. `legal_hold` mencegah proses purge retention; executor purge belum diaktifkan sampai kebijakan Finance/Legal disetujui.

## Security acceptance criteria

Dokumen tidak dapat diakses dengan mengganti ID/URL, user lintas company tidak melihat hasil search, bundle hanya berisi dokumen yang diizinkan saat request, expired link gagal, dan seluruh tindakan sensitif dapat ditelusuri ke actor/request ID.
