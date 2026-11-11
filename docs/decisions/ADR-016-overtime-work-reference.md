# ADR-016: Referensi pekerjaan pada lembur

Tanggal: 2026-11-10

Pengajuan lembur bertipe `external_project` tidak dipaksa memiliki project. Referensi
pekerjaan dapat berupa project, CRM opportunity, atau tidak ada referensi.

`project_id` tetap dipertahankan. `opportunity_id` disimpan sebagai UUID lintas koneksi
tanpa foreign key lintas schema, dan service memeriksa scope perusahaan serta permission
CRM. Nama dan status referensi disimpan sebagai snapshot untuk menjaga histori ketika
opportunity berubah stage, closing status, atau dihapus.

Stage `won`/`delivered` atau closing status `closed` diberi label `Sales`. Status lain
ditampilkan sebagai `Prospek / tidak menjadi sales`.
