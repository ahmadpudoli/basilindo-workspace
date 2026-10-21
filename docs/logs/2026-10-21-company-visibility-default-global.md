# Company visibility default global

Tanggal: 2026-10-21

## Perubahan

- Menetapkan bahwa visibilitas kosong pada perusahaan berarti global.
- Memperluas `Company::visibleTo()` agar mengikutsertakan record tanpa relasi visibilitas, selain record yang cocok dengan perusahaan operasional user.
- Menambahkan `Company::operationalFor()` untuk membatasi selector perusahaan operasional di modul Company, CRM, Project Management, dan Document Management.
- Memastikan dashboard Company memakai query yang sama dengan policy visibilitas.
- Memperbarui hint form agar arti global dan batas hak akses user jelas bagi pengguna.
- Mendokumentasikan requirement, acceptance criteria, dan keputusan arsitektur pada dokumen proyek.

## Batasan yang dipertahankan

Dokumen, project, ticket, account CRM, dan data operasional lain tetap memakai company scope record. Master perusahaan yang global tidak menjadikan data operasional tersebut global.

## Verifikasi

- Audit pemakaian `Company::query()` pada selector lintas modul.
- PHP lint untuk controller/model yang berubah.
- View cache dan frontend build dijalankan setelah perubahan.

## Risiko tersisa

- Migration tabel visibilitas harus sudah dijalankan pada setiap environment sebelum halaman edit perusahaan digunakan.
- Modul baru wajib memakai `visibleTo()` untuk daftar master perusahaan dan `operationalFor()` untuk pilihan perusahaan operasional.
