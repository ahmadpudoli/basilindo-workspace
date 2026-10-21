# 2026-10-21 — Form dokumen dan dependensi metadata

## Perubahan

- Menata ulang pasangan field pada form tambah dan edit dokumen.
- Mengganti CRM account dengan searchable dropdown perusahaan client/vendor.
- Memuat jenis dokumen berdasarkan perusahaan internal melalui komponen Livewire dan menambahkan indikator loading.
- Menambahkan indikator loading panel metadata tambahan.
- Menambahkan format nominal Indonesia dan tag chip input dengan penyimpanan comma separated.
- Menambahkan validasi client untuk form dokumen dan validasi server untuk scope counterparty serta format nominal.
- Menonaktifkan validasi native browser pada form dokumen, menambahkan validasi JavaScript untuk file dan metadata wajib, serta merapatkan padding area tombol simpan.
- Menempatkan pesan error validasi upload pada baris di bawah kontrol file agar tidak menabrak input.
- Menambahkan border dan tinggi kontrol yang seragam pada metadata boolean Ya/Tidak.
- Memperluas aturan floating label global agar wrapper searchable dropdown dan tag input juga menggunakan label floating.

## Verifikasi

- `php -l app/Modules/DocumentManagement/Http/Controllers/DocumentManagementController.php`
- `php -l app/Modules/DocumentManagement/Livewire/DocumentMetadataFields.php`
- `php artisan view:cache`
- `npm.cmd run build`
- `php artisan test tests/Feature/DocumentEditViewTest.php tests/Feature/DocumentUploadServiceTest.php`
- `git diff --check`
