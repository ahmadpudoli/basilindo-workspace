# Schema cleanup dan system admin — 2026-10-01

## Perubahan

- Audit migration, model, service, policy, resource, dan foreign key database `db_file_organize`.
- Mempertahankan tabel project/ticket karena masih direferensikan UI, model, import/export, dan dashboard.
- Menghapus `saved_searches` melalui migration karena belum memiliki consumer aplikasi.
- Menambahkan `users.is_system_account` dan akun `admin@example.com` sebagai `super_admin`.
- Menambahkan proteksi delete di policy, model event, dan trigger PostgreSQL.

## Verifikasi

- Database terhubung dan migration sebelumnya berstatus `Ran`.
- Sebelum seed, belum ada user bisnis; role dasar tersedia tanpa assignment.
- Verifikasi lanjutan dilakukan dengan migrate, seed, test, dan pengecekan trigger.

## Risiko tersisa

- Password development harus diganti melalui `LOCAL_ADMIN_PASSWORD` sebelum deployment.
- Tabel legacy project/ticket belum dipensiunkan karena masih menjadi dependency aplikasi.
