# Project opsional pada pengajuan lembur

## Perubahan

- Requirement/acceptance criteria HRIS, domain model, keputusan scope, dan checklist
  diperbarui sebelum penyelesaian implementasi.
- Form tambah/revisi menampilkan dropdown Project (opsional) dengan floating label
  saat memilih External / Project. Internal menyembunyikan, menonaktifkan, dan
  mengosongkan pilihan. Empty state tetap mengizinkan pengajuan tanpa project.
- Controller mengambil project berdasarkan perusahaan pemohon, permission
  view_project, serta membership (super_admin dikecualikan dari permission/membership).
- Service memvalidasi ulang scope project sebelum menyimpan pengajuan maupun revisi.
  Form Request menolak ID bukan integer dan mengecualikan project pada jenis Internal.
- Kolom project_id nullable berindex dan relasi Core Project ditambahkan secara
  incremental. Pengajuan lama tetap dipertahankan tanpa backfill yang diperlukan.
  Rollback kode mempertahankan kolom agar referensi tidak hilang.
- Tidak menambah package, mengubah role assignment, atau mengubah alur approval.

## Verifikasi

- Migration targeted pada database development: berhasil, tanpa reset/penghapusan data.
- PHP syntax check service, Form Request, migration: lulus.
- Test lembur mencakup penyimpanan project valid, external tanpa project, pengosongan
  project saat revisi internal, penolakan lintas perusahaan, tanpa permission,
  tanpa membership, ID tidak ditemukan, render pilihan sesuai scope, dan validasi HTTP.
- Pemeriksaan visual browser interaktif belum dilakukan; script toggle memakai
  event change native dan nilai awal dari old input/model.

## Catatan

Percobaan test awal menemukan fixture super_admin yang melanggar proteksi akun
system dan kode perusahaan dataset duplikat; fixture diperbaiki memakai permission
dan membership normal serta kode unik per kasus. Test dijalankan pada db_workspace_test.
