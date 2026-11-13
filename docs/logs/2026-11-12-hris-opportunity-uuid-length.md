# 2026-11-12 — Panjang UUID opportunity reimbursement

## Perubahan

- Mengganti validasi server opportunity dari panjang ULID 26 menjadi UUID maksimal 36 karakter.
- Menambahkan validasi client-side maksimum 36 karakter.
- Menambahkan migration incremental untuk memperbesar `claims.opportunity_id` menjadi `varchar(36)`, dengan rollback yang tidak akan mempersempit kolom jika sudah ada UUID panjang.
- Memperbarui requirement dan checklist reimbursement.

## Verifikasi

- Membandingkan skema CRM `opportunity_id` bertipe UUID dengan validasi reimbursement yang sebelumnya mengharuskan 26 karakter.
- Menjalankan migration incremental pada database lokal; migration selesai.
- Pemeriksaan sintaks PHP, `npm.cmd run build`, dan `git diff --check` berhasil.

## Risiko tersisa

- Tidak ada risiko khusus yang diketahui; migration dapat di-rollback tanpa mempersempit kolom jika terdapat UUID 36 karakter.
