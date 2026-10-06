# Basilindo SSO

`project-sso` adalah Identity Provider internal perusahaan untuk File Organizer dan aplikasi internal lain.

## Kontrak OIDC

- Issuer lokal: `http://127.0.0.1:8001`
- Discovery: `/.well-known/openid-configuration`
- Authorization endpoint: `/oauth/authorize`
- Token endpoint: `/oauth/token`
- Userinfo: `/oidc/userinfo`
- JWKS: `/oidc/jwks`
- Protokol: authorization code + PKCE S256
- Signing algorithm: RS256

## Database dan service

- PostgreSQL database: `db_sso_basilindo`
- Redis DB: 2, cache DB: 3, prefix `project_sso_`
- MinIO bucket: `app-oss-basilindo`

## Menjalankan

```bash
composer install
php artisan migrate
php artisan passport:keys
composer run dev
```

Register client aplikasi melalui Passport. Redirect URI harus exact-match dan tidak boleh memakai wildcard. Secret client hanya disimpan di secret manager atau `.env` lokal.

## Security boundary

SSO menyimpan identitas, client, authorization code, access/refresh token, dan consent. SSO tidak menyimpan permission domain File Organizer. Setiap aplikasi tetap menerapkan authorization bisnisnya sendiri.

## Validasi

```bash
php artisan test
```

Discovery, JWKS, Passport schema, dan flow client callback File Organizer sudah diuji. Group/role provisioning, revocation dashboard, MFA, dan centralized audit adalah production-hardening lanjutan.
