# Permission akses modul

## Requirement

Setiap modul kerja memiliki satu permission akses tingkat modul: `access_company`,
`access_crm`, `access_project_management`, atau `access_documents`. Permission ini
menentukan visibilitas kartu pada dashboard, launcher, dan sidebar modul serta menjadi
otorisasi halaman ringkasan modul. Permission CRUD resource tetap terpisah dan
menentukan menu serta operasi di dalam modul.

## Acceptance criteria

- Kartu dan menu modul tidak ditampilkan jika permission akses modul tidak dimiliki.
- Akses URL halaman ringkasan modul ditolak jika permission akses modul tidak dimiliki.
- Permission `view/create/update/delete_*` tetap mengontrol resource individual.
- Migration melakukan backfill permission modul dari permission `view_any_*` lama tanpa
  menghapus data atau mereset database.
- Super admin tetap memiliki seluruh permission.
