# Log pekerjaan — Pemetaan port lokal

## Perubahan

- Menetapkan `project-sso` ke port `8000`, `file-organizer` ke port `8001`, dan `project-management` ke port `8002`.
- Menyesuaikan script Composer/Octane, Docker Compose, `.env`, `.env.example`, issuer OIDC, callback, logout redirect, dokumentasi, dan test.
- Menambahkan migration koreksi untuk URL aplikasi dan redirect URI File Organizer pada data SSO yang sudah ada.
- Menambahkan registration/upsert OAuth client File Organizer karena client ID yang dipakai redirect sebelumnya belum dijamin ada di tabel `oauth_clients`.

## Verifikasi

- Pencarian konfigurasi aktif menunjukkan ketiga port sesuai pemetaan baru; referensi pada log historis dibiarkan sebagai catatan kejadian masa lalu.
- `project-sso`: `tests/Feature/OidcDiscoveryTest.php` lulus, 3 test dan 21 assertion.
- Investigasi error `invalid_client`: metadata aplikasi SSO menunjuk ke client ID File Organizer, tetapi tidak ada registration migration yang memastikan record OAuth client tersebut tersedia.
- Migration Project SSO berhasil dijalankan; request authorization live dengan client ID dan callback baru tidak lagi menghasilkan `invalid_client` (request uji berhenti pada validasi PKCE seperti yang diharapkan).
- Menghapus `prompt=login` dari request normal File Organizer. Parameter tersebut sebelumnya membuat middleware SSO me-logout session yang baru berhasil dibuat ketika URL authorization dipanggil ulang, sehingga login berputar kembali ke `/admin/login`.
- Menghapus efek logout otomatis dari middleware capture authorization agar URL lama yang masih membawa `prompt=login` juga tidak dapat menghapus session login yang baru dibuat.
- `file-organizer`: `tests/Feature/SsoControllerTest.php` terblokir sebelum assertion karena permission denied saat menulis cache Laravel/Pest pada `storage` dan `vendor`.
- Project Management memakai client ID/secret lokal yang sesuai; secret OAuth di database diubah menjadi hash Bcrypt agar Passport dapat memvalidasi token exchange.
- Migration hash secret PM berhasil dijalankan dan cache Project SSO/Project Management berhasil dibersihkan.
- Memperbaiki mismatch tabel notifikasi: migration lama membuat `pm_notifications`, sedangkan shared user mencari `core_notifications`; model notifikasi dan migration baru sekarang memakai koneksi `core`.
- Memperbaiki deklarasi ganda `$connection` pada model Notification Project Management yang menyebabkan fatal error saat class dimuat.
- Menetapkan model Ticket Project Management ke koneksi `pgsql` agar relasi dari shared User/Project membaca `pm_tickets` dan `pm_ticket_users`, bukan `core_tickets`.
- Menghindari relasi lintas koneksi bermasalah pada halaman User dan widget statistik PM dengan query eksplisit ke tabel `pm_ticket_users`/`pm_tickets` melalui koneksi `pgsql`.
- Merotasi nama cookie session dari suffix `v2` ke `v3` pada ketiga aplikasi agar browser otomatis mengabaikan cookie session lama tanpa tindakan manual dari pengguna.

## Risiko tersisa

- Service yang sedang berjalan perlu dihentikan dan dijalankan ulang agar memakai port baru.
- Browser mungkin masih menyimpan cookie/session dari host-port lama.
