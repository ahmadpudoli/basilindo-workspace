# Requirement — Entitlement SSO ke File Organizer

## Tujuan

Callback File Organizer hanya boleh menerima login jika subject SSO memiliki
akses aktif ke aplikasi yang meminta login. Entitlement harus ditentukan dari
OAuth client pada bearer token, bukan hanya dari identitas user.

## Acceptance criteria

- Endpoint OIDC userinfo mengambil client ID dari Passport API guard.
- User dengan pivot `application_user_access.status = active` dan client
  `file-organizer` menerima claim `applications.current.allowed = true`.
- User tanpa entitlement aktif, client tidak dikenal, atau aplikasi nonaktif
  tetap ditolak oleh callback File Organizer dengan HTTP 403.
- Token/transient token yang tidak memiliki client ID tidak boleh memperoleh
  akses secara default.
