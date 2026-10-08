# Integrasi Basilindo Project Management

## Perubahan

- Mengganti landing page publik dan footer dari branding lama menjadi Basilindo Project Management.
- Menyamakan tema panel Filament dengan atmosfer cyan/blue File Organizer.
- Menghapus login Google dan menggantinya dengan SSO OIDC authorization code + PKCE.
- Menambahkan local system account `admin@example.com` dengan role `super_admin` pada seeder aplikasi.
- Menambahkan session cookie terpisah dan logout lokal yang kembali ke landing page.
- Mendaftarkan OAuth client serta aplikasi Project Management pada Project SSO.

## Verifikasi

- Konfigurasi SSO memakai callback `http://localhost:8002/auth/sso/callback`.
- Client SSO memakai redirect URI yang sama dan akses admin diberi role `super_admin` oleh seeder SSO.
- Sintaks PHP dan konfigurasi Composer perlu diverifikasi setelah dependency/cache dibersihkan.

## Risiko tersisa

- Akun SSO user biasa tetap membutuhkan application access aktif dan provisioning/linking identitas pada shared core.
- Secret client pada `.env` hanya untuk development; production harus memakai secret manager.
