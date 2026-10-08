# Log pekerjaan — Routing logout SSO

## Perubahan

- Mengganti response logout bawaan Filament dengan `SsoLogoutResponse` yang hanya mengakhiri session File Organizer.
- Menghentikan redirect logout ke `/oidc/logout`, sehingga session aplikasi lain dan session IdP tidak ikut dihapus.
- Menambahkan `prompt=login` pada request OIDC dan memprosesnya di `project-sso` agar login baru meminta autentikasi ulang.
- Menyimpan kembali URL authorization setelah session web SSO di-reset untuk proses login ulang.

## Verifikasi

- Syntax check response dan provider dilakukan.
