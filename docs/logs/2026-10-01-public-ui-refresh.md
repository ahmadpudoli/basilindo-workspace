# 2026-10-01 — Refresh UI publik Basilindo

## Requirement dan acceptance criteria

- Halaman publik File Organizer harus menjadi landing page Document Hub Finance.
- Halaman publik project SSO harus menjadi portal Basilindo Access.
- Branding DewaKoding dan copy project management tidak boleh tampil di UI publik.
- CTA login, responsif, kontras, fokus keyboard, dan state hover harus layak digunakan.

## Perubahan

- Memperbarui landing page File Organizer dengan hierarchy, CTA, preview dashboard, fitur domain Finance, dan footer Basilindo.
- Mengganti landing page project SSO dengan portal Basilindo Access yang menjelaskan SSO, role-based access, dan audit aktivitas.
- Menetapkan brand panel SSO menjadi `Basilindo Access`.
- Mengganti footer layout pengguna eksternal menjadi Basilindo.
- Menambahkan requirement UI pada `docs/ui-branding-requirements.md`.

## Verifikasi

- `php artisan optimize:clear` berhasil pada kedua aplikasi.
- View Blade dikompilasi ulang tanpa error melalui cache clear.
- Pencarian resource UI tidak menemukan copy `DewaKoding`, `Project Management Simplified`, atau fitur project management lama.

## Risiko tersisa

- Tailwind dan font halaman publik masih memakai CDN; deployment dengan kebijakan jaringan ketat perlu memindahkannya ke asset lokal.
- Browser visual check di breakpoint mobile tetap disarankan setelah server lokal aktif.
