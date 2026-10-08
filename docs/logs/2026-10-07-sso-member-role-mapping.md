# Log pekerjaan — Mapping role member SSO

## Perubahan

- Menambahkan mapping `member` dari claim `applications.current.roles` SSO ke role `member` File Organizer.
- Memastikan user yang baru diprovision dari SSO memiliki role lokal sehingga `canAccessPanel()` tidak mengarahkannya kembali ke login.

## Verifikasi

- Config mapping diperiksa terhadap role `member` yang dibuat oleh `RoleSeeder` File Organizer.

## Catatan penggunaan

- User `test2@example.com` perlu login ulang dari awal agar callback SSO menjalankan provisioning atau sinkronisasi role.
