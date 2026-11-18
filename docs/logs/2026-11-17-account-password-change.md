# Fitur ubah password akun — 2026-11-17

## Perubahan

- Menambahkan tautan Ubah password pada menu akun Workspace.
- Membuat halaman perubahan password dengan verifikasi password saat ini, minimum 8 karakter, konfirmasi, dan aturan password baru berbeda dari password saat ini.
- Memperbarui hash password melalui cast `hashed` pada model User, memutar session aktif, serta mencabut session lain saat driver session menggunakan database.
- Mencatat event audit `auth.password_changed` tanpa password atau metadata rahasia.
- Endpoint perubahan password dibatasi throttle 6 request per menit.

## Verifikasi

- Template Blade dikompilasi dan file PHP diperiksa sintaksnya.
- Test suite tidak dijalankan.

## Asumsi

- Minimum password 8 karakter mengikuti aturan provisioning user Workspace yang sudah berlaku.
- Pencabutan session lain diterapkan untuk driver database; driver session lain mengikuti kemampuan invalidasi drivernya.
