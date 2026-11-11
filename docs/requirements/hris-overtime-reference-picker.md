# Requirement: pemilih referensi pekerjaan lembur

## Acceptance criteria

- Pada lembur `External / Project`, Project dan Opportunity ditampilkan sebagai tabel.
- Tabel menampilkan nama, status, dan client; Opportunity juga menampilkan kontak.
- Tabel memiliki pencarian client-side berdasarkan nama, status, client, atau kontak.
- User dapat memilih satu baris dan melihat ringkasan nama, status, client, dan kontak.
- Nilai yang dikirim tetap menggunakan `project_id` atau `opportunity_id` yang divalidasi server.
- Daftar tetap mengikuti scope perusahaan dan permission user yang berlaku.
