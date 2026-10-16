# Scope perusahaan Document Management — 2026-10-12

## Perubahan

- Assignment perusahaan pada user menjadi sumber batas akses dokumen.
- `super_admin` dengan assignment perusahaan eksplisit hanya dapat melihat dan memilih perusahaan yang di-assign.
- `super_admin` tanpa assignment tetap memiliki fallback akses seluruh perusahaan agar akun sistem baru tidak terkunci sebelum provisioning.
- Dropdown `Perusahaan internal`, query daftar dokumen, dan policy view/download/update/review/delete memakai aturan scope yang sama.

## Verifikasi

- `admin@example.com` pada data lokal memiliki assignment Quinsis dan Legospay; kedua perusahaan tersebut menjadi pilihan perusahaan internalnya.
- Akses ke dokumen perusahaan lain ditolak oleh query/policy.

## Risiko tersisa

- Assignment perusahaan tetap dikelola melalui halaman Users. Perubahan assignment akan langsung memengaruhi pilihan dan daftar dokumen pada login berikutnya.
