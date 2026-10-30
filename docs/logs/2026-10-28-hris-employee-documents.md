# Dokumen Karyawan HRIS

## Perubahan

- Menambahkan tabel metadata `hris.employee_documents` dengan ULID, checksum,
  metadata file, periode berlaku, uploader, dan soft delete.
- Menambahkan permission granular dokumen HRIS secara incremental.
- Menambahkan akses self-service `view_own_employee_documents` agar karyawan
  hanya dapat melihat dokumennya sendiri.
- Menambahkan menu dan halaman khusus HRIS untuk daftar dan upload dokumen
  karyawan; fitur tidak dicampur ke Document Management umum.
- Memindahkan tampilan permission dokumen karyawan ke grup HRIS pada form Role
  dan Pengguna tanpa mengubah nama atau assignment permission yang sudah ada.
- Menambahkan reconciliation migration untuk memulihkan katalog permission yang
  hilang pada database aktif; grant penuh hanya untuk `super_admin`/`hris_admin`,
  sedangkan `hris_member` hanya menerima akses dokumen miliknya sendiri.
- Cache permission Spatie dan cache Laravel di-reset setelah provisioning agar
  permission baru langsung terbaca oleh proses aplikasi yang berjalan.
- Menyimpan file pada private disk yang dikonfigurasi aplikasi dengan prefix
  `hris/employee-documents/{employee_id}` dan download melalui stream policy.
- Memperbarui form upload menjadi layout modern dengan hero, area preview file,
  panel metadata, dan informasi keamanan.
- Menambahkan `document_number` unik otomatis berformat `HRIS-YYYYMMDD-...`,
  menampilkannya di daftar, memasukkannya ke pencarian, dan menggunakannya pada
  object key file.
- Menyesuaikan header halaman upload ke pola page heading standar HRIS dan
  menghapus istilah teknis storage dari antarmuka pengguna.
- Mengganti placeholder `XXXXXXXXXX` pada form dengan nomor dokumen aktual yang
  dibuat saat halaman dibuka dan dipakai kembali saat submit.
- Menyederhanakan struktur form agar judul berada paling atas, lalu karyawan,
  jenis dokumen, periode berlaku, dan lampiran; dropdown karyawan kini memakai
  pencarian nama/NIP.
- Mempertahankan kembali layout lampiran dan panel nomor dokumen yang sudah
  benar; perubahan hanya diterapkan pada urutan blok informasi dokumen.

## Verifikasi

- Syntax PHP dan route list perlu dijalankan setelah migration diterapkan.
- Test upload dan penolakan akses lintas perusahaan ditambahkan pada
  `tests/Feature/HrisEmployeeDocumentTest.php`.
- Acceptance criteria ditambahkan ke `docs/requirements/hris-module.md`.
- Migration `2026_10_28_000013_add_document_number_to_hris_employee_documents`
  berhasil dijalankan.

## Risiko tersisa

- Pengujian integrasi MinIO dan retention job perlu dijalankan pada stack Docker.
- Penghapusan object fisik belum dilakukan saat arsip; ini disengaja untuk
  menjaga retensi dan audit.
