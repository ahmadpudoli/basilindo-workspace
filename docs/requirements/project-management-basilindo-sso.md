# Requirement — Basilindo Project Management dan SSO

## Tujuan

Project Management menggunakan identitas dan pola pengalaman yang konsisten dengan File Organizer, tanpa branding DewaKoding.

## Acceptance criteria

- Halaman `/` adalah landing page publik dengan branding Basilindo Project Management.
- Panel `/admin` memakai layout dan atmosfer visual Basilindo yang konsisten dengan File Organizer.
- Login panel diarahkan ke Project SSO menggunakan authorization code + PKCE.
- Callback hanya menerima user yang memiliki akses aktif ke aplikasi Project Management.
- `admin@example.com` tersedia sebagai system account dengan role `super_admin`.
- Logout hanya menghapus session Project Management dan kembali ke `/`, tanpa logout global SSO.
- Cookie session Project Management terpisah dari File Organizer.
- Tidak ada referensi branding DewaKoding pada source dan dokumentasi aplikasi.
