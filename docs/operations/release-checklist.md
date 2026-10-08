# Release checklist dan rollback

## Sebelum release

- [ ] Review migration dan rollback path.
- [ ] Jalankan `php artisan test` dan `npm.cmd run build`.
- [ ] Jalankan `php artisan migrate --pretend` pada seluruh migration baru.
- [ ] Verifikasi secret manager, konfigurasi login lokal, MinIO private bucket,
  queue worker, dan scheduler. Jika OIDC diaktifkan, verifikasi redirect URI dan credential.
- [ ] Ambil backup PostgreSQL dan catat backup object storage terbaru.
- [ ] Verifikasi health endpoint, authorization scope, dan smoke test login.

## Rollout

1. Deploy image/code immutable.
2. Jalankan migration yang kompatibel secara backward.
3. Restart queue worker dengan `queue:restart`.
4. Jalankan scheduler dan job queue yang diperlukan.
5. Smoke test `/up`, login, daftar user, CRM, project, upload, dan download.

## Rollback

- Hentikan rollout dan pertahankan worker pada versi yang kompatibel.
- Kembalikan image/code ke release sebelumnya.
- Jangan menjalankan `migrate:rollback` otomatis pada production; evaluasi migration
  dan gunakan forward-fix atau restore snapshot yang telah disetujui.
- Revoke/rotate credential yang terindikasi bocor.
- Catat incident, waktu, actor, backup point, dan hasil validasi.
