# Identitas user requester pengajuan HRIS

## Perubahan

- Menambahkan kolom `requester_user_id` pada pengajuan cuti/sakit.
- Service submit mengisi kolom tersebut dari user login yang melakukan request.
- Data historis di-backfill dari `created_by` tanpa mengubah relasi karyawan.
- Primary key ULID pada model pengajuan HRIS ditetapkan sebagai string/non-incrementing
  agar relasi detail tidak terkoersi menjadi integer `1`.
- Link detail tanggal orphan diperbaiki melalui migration reversible dengan tabel
  repair sebagai jejak pasangan ID lama dan baru.
- Model `LeaveRequest` menyediakan relasi `requester` ke Core User.
- Test pengajuan diperbarui agar menggunakan multiple row tanggal dan memverifikasi
  ID user requester.

## Verifikasi

- Migration incremental disiapkan tanpa reset database.
- PHP lint dilakukan pada model, service, migration, dan test yang berubah.
- Query daftar user Ahmad diverifikasi menampilkan 1 pengajuan setelah perbaikan
  relasi detail tanggal.
