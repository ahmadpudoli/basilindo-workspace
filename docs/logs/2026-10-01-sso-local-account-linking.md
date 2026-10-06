# 2026-10-01 — SSO File Organizer dan akun recovery lokal

## Temuan

- `project-sso` dan `file-organizer` memakai nama cookie session default yang sama
  serta APP_KEY yang sama; pada host `127.0.0.1` hal ini membuat session OAuth
  tercampur dan memicu 403 Passport.
- File Organizer memiliki akun recovery `admin@example.com`, sehingga callback
  tidak dapat membuat user baru ketika email tersebut sudah ada.
- Role `admin` dari application access belum dipetakan ke role `admin` lokal.

## Perubahan

- Memisahkan cookie menjadi `basilindo_sso_session` dan
  `basilindo_file_organizer_session`.
- Memisahkan APP_KEY development project-sso dari File Organizer.
- Menambahkan linking terbatas untuk protected system account dengan email
  terverifikasi dan flag `SSO_ALLOW_SYSTEM_ACCOUNT_LINKING`.
- Menambahkan mapping role SSO `admin` ke role aplikasi `admin`.
- Menambahkan feature test callback linking.

## Verifikasi

- SSO callback test mencakup provisioning baru dan linking recovery account.
- Session cookie configuration terdokumentasi di `.env.example`.

## Catatan operasi lokal

Hapus cookie lama untuk host `127.0.0.1` dan `localhost`, lalu restart kedua
server agar konfigurasi `.env` dan config cache terbaca.
