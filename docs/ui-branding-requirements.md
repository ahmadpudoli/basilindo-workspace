# Requirement UI dan branding halaman publik

## Tujuan

Menggantikan landing page Laravel bawaan pada `app/file-organizer/` dan `app/project-sso/`
dengan halaman publik yang menjelaskan peran masing-masing aplikasi, memakai
brand Basilindo, dan tetap mengarahkan pengguna ke entry point yang benar.

## Acceptance criteria

- Halaman `/` pada File Organizer menjelaskan Document Hub untuk Finance dalam Bahasa Indonesia.
- Halaman `/` pada SSO menjelaskan akses identitas internal Basilindo, bukan fitur project management.
- CTA utama menuju `/admin` dan memiliki fokus keyboard yang terlihat.
- Layout responsif pada mobile, tablet, dan desktop tanpa overflow horizontal.
- Kontras teks, hierarchy heading, state hover/focus, dan ukuran target interaksi layak digunakan.
- UI tidak menampilkan data operasional nyata; angka pada preview harus jelas bersifat ilustratif.
- Background admin setelah login pada kedua aplikasi harus konsisten dengan atmosfer halaman Basilindo Access: dasar terang dengan glow cyan/blue yang lembut.
- Mode gelap tetap memiliki fallback gradient yang kontras dan tidak mengurangi keterbacaan konten.
- Background admin setelah login pada kedua aplikasi harus konsisten dengan atmosfer halaman Basilindo Access: dasar terang dengan glow cyan/blue yang lembut.
- Mode gelap tetap memiliki fallback gradient yang kontras dan tidak mengurangi keterbacaan konten.
