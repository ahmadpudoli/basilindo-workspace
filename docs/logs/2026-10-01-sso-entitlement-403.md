# 2026-10-01 — Perbaikan 403 entitlement SSO

## Temuan

File Organizer menolak callback ketika claim `applications.current.allowed`
bernilai `false`. Data development sudah memiliki akses aktif untuk akun SSO,
dan OAuth client ID cocok dengan konfigurasi File Organizer. Endpoint userinfo
project-sso mengambil client ID dari token yang terpasang pada user, padahal
token tersebut tidak selalu membawa client pada alur Passport aktual.

## Perubahan

- Lookup client OIDC menggunakan `auth('api')->client()` dari Passport guard.
- Pemeriksaan entitlement tetap fail-closed jika client tidak dikenal atau
  tidak ada akses aktif.
- Requirement, checklist, dan acceptance criteria diperbarui.

## Verifikasi

- Verifikasi database: aplikasi `file-organizer` aktif dan akses kedua akun
  development berstatus `active`.
- Test suite project-sso dijalankan setelah perubahan controller.

## Catatan operasi

Restart project-sso agar kode controller baru aktif, hapus callback URL lama,
lalu login ulang dari `http://localhost:8000/admin/login`.
