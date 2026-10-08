# Requirement: Projection application access lintas SSO dan Workspace

## Tujuan

Admin aplikasi client hanya melihat user yang memiliki akses ke aplikasi tersebut,
tanpa membaca database Project SSO secara langsung dan tanpa menyalin data identitas
yang tidak diperlukan.

## Acceptance criteria

- Project SSO menyediakan endpoint internal untuk access projection.
- Endpoint hanya menerima Passport client-credentials dengan scope `application:access:read`.
- Client harus terdaftar pada allowlist `SSO_INTERNAL_ACCESS_CLIENT_ID`.
- Response hanya memuat `application`, waktu generasi, `subject`, dan `status` access.
- Endpoint memiliki rate limit dan audit log metadata tanpa nama/email/token.
- File Organizer menyimpan projection lokal pada `core_application_access`.
- Sinkronisasi menandai access yang sudah tidak ada di SSO sebagai `revoked`.
- Daftar user File Organizer hanya mengambil user dengan access aktif untuk code `file-organizer`.
- Jika migration projection belum tersedia, halaman user harus menampilkan state kosong yang aman, bukan fallback ke seluruh user.
- Mapping ke user lokal menggunakan `(issuer, subject)`, bukan email.

## Operasional

1. Pada Project SSO buat client credentials:

   ```bash
   php artisan passport:client --client --name="Workspace Access Projection"
   ```

2. Simpan Client ID pada `SSO_INTERNAL_ACCESS_CLIENT_ID` di Project SSO.
3. Simpan Client ID dan secret yang sama pada File Organizer melalui
   `SSO_ACCESS_SYNC_CLIENT_ID` dan `SSO_ACCESS_SYNC_CLIENT_SECRET`.
4. Aktifkan `SSO_ACCESS_SYNC_ENABLED=true` pada File Organizer.
5. Jalankan sinkronisasi:

   ```bash
   php artisan sso:sync-application-access file-organizer
   ```

6. Jalankan scheduler Laravel pada worker/cron deployment:

   ```bash
   php artisan schedule:work
   ```

   Projection akan disinkronkan setiap jam, hanya ketika
   `SSO_ACCESS_SYNC_ENABLED=true`, dengan lock `withoutOverlapping` dan
   `onOneServer`.

Secret hanya boleh berada di environment/secret manager dan tidak boleh dicatat di log.
