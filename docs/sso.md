# Strategi SSO dan Identity Federation

## Arah yang dipilih

`app/project-sso/` akan menjadi Identity Provider/SSO internal perusahaan. File Organizer di `app/file-organizer/` menjadi OIDC relying party/client. Aplikasi internal lain dapat mendaftarkan client masing-masing ke `app/project-sso/` sehingga karyawan menggunakan identitas yang sama di seluruh aplikasi.

OIDC menjadi protokol utama karena cocok untuk aplikasi web internal. SAML hanya dipertimbangkan sebagai integrasi tambahan jika ada kebutuhan legacy atau federation eksternal.

`app/project-sso/` dapat menggunakan atau membungkus komponen identity provider yang disetujui perusahaan, tetapi kontrak yang terlihat oleh aplikasi adalah OIDC. Pilihan engine, database, dan deployment SSO harus diputuskan di dokumentasi `app/project-sso/`.

## Model identitas

- `users` adalah identitas internal aplikasi.
- `external_identities` menyimpan `issuer`, `subject`, `user_id`, claims terpilih, dan timestamps.
- Kunci mapping adalah `(issuer, subject)`, bukan email.
- Email hanya atribut kontak/initial matching yang perlu verifikasi dan aturan conflict.
- Membership company dan role tetap dikelola oleh aplikasi atau disinkronkan dari group claim yang telah di-whitelist.

## Fase implementasi

1. Definisikan kontrak OIDC dan lifecycle client di `app/project-sso/`.
2. Integrasikan File Organizer dengan discovery URL, client ID/secret, redirect URI allowlist, state/nonce/PKCE, dan account linking yang eksplisit.
3. Tambahkan group/role mapping, logout strategy, session revocation, dan provisioning/deprovisioning.
4. Integrasikan aplikasi internal berikutnya sebagai client baru tanpa membuat akun lokal terpisah.

## Aturan keamanan SSO

- Jangan auto-link akun hanya berdasarkan email tanpa verifikasi domain dan kebijakan eksplisit.
- Simpan refresh token hanya bila benar-benar diperlukan; enkripsi atau hindari penyimpanan.
- Validate issuer, audience, signature, nonce, state, expiry, dan redirect URI.
- SSO tidak menggantikan authorization lokal. User tetap harus memiliki company membership dan permission.
- `app/project-sso/` tidak boleh menjadi tempat menyimpan permission domain File Organizer; aplikasi tetap bertanggung jawab atas authorization bisnisnya.
- Sediakan emergency/admin recovery path yang diaudit dan dibatasi.

## Local development account linking

File Organizer menggunakan cookie session yang berbeda dari `app/project-sso` agar
kedua aplikasi tidak saling membaca session Redis ketika sama-sama dibuka pada
host `127.0.0.1`. Akun recovery system `admin@example.com` hanya boleh di-link
ke subject SSO ketika `SSO_ALLOW_SYSTEM_ACCOUNT_LINKING=true` pada environment
development dan claim email dari SSO sudah terverifikasi. Nilai tersebut harus
tetap `false` pada production; akun biasa memakai account-linking administratif
berbasis issuer + subject.
