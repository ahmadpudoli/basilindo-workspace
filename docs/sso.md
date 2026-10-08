# Arsip: Strategi SSO dan Identity Federation

> Deprecated. Dokumen ini adalah catatan historis. Runtime aktif tidak lagi
> menggunakan SSO; lihat [Identitas Workspace](identity.md) dan ADR-013.

## Arah yang dipilih

Runtime utama Basilindo Workspace menggunakan login lokal Laravel/Filament dalam satu aplikasi modular monolith. `app/project-sso/` dipertahankan sebagai adapter/future identity federation, bukan dependency login normal.

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
- Mapping Workspace juga mencakup `crm_admin`, `crm_member`, `project_admin`, dan `project_member`. Setelah login ulang, user tetap harus memiliki company membership agar menu dan data client dapat diakses sesuai scope.
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

## Application access projection

Database Project SSO dan Workspace tidak dibaca silang. Project SSO menyediakan
endpoint internal projection yang dilindungi Passport client-credentials dengan
scope `application:access:read` dan allowlist client. Response sengaja hanya
memuat subject dan status access tanpa nama, email, atau claims identitas.

File Organizer menyimpan projection lokal pada `core_application_access` dan
menjalankannya melalui command `sso:sync-application-access`. User lokal dipetakan
berdasarkan `(issuer, subject)` dari `external_identities`; email bukan kunci
otorisasi. Access yang hilang dari response ditandai `revoked`, bukan dihapus
agar audit dan rekonsiliasi tetap tersedia.
