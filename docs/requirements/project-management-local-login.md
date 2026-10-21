# Requirement: Login lokal Project Management legacy

## Tujuan

Project Management tidak boleh mengarahkan pengguna ke Identity Provider SSO
yang sudah tidak menjadi runtime aktif. Halaman login harus mengikuti pola
File Organizer: form login lokal Laravel menggunakan guard aplikasi.

## Acceptance criteria

- `GET /admin/login` menampilkan form login lokal.
- Halaman login tidak melakukan redirect ke `/auth/sso` atau
  `http://localhost:8000/oauth/authorize`.
- Login menggunakan kredensial user lokal dan session aplikasi.
- Cache rate limiter tersedia tanpa bergantung pada tabel database legacy yang
  belum dimigrasikan.
- Relasi assignee Ticket membaca `core_users` dan `pm_ticket_users` tanpa
  menghasilkan nama tabel `core_ticket_users`.
- Perubahan tidak mengaktifkan kembali `project-sso` sebagai dependency runtime.
