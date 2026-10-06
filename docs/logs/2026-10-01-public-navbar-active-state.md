# 2026-10-01 — Kontras state aktif navbar publik

## Perubahan

- Menetapkan state aktif CTA navbar menuju `/admin` dengan background slate
  gelap, teks putih, border cyan yang halus, dan shadow ringan.
- Menambahkan hover dan focus-visible state agar kontras tetap jelas tanpa
  menabrak glow cyan/blue pada background halaman publik.

## Verifikasi

- Selector dibatasi pada link navbar `/admin`, sehingga elemen konten lain
  tidak ikut berubah.
- Fokus keyboard terlihat melalui outline cyan dengan offset yang cukup.
