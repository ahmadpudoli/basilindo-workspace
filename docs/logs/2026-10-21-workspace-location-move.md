# Pemindahan lokasi runtime Workspace

## Perubahan

- Memindahkan source aplikasi Workspace dari `app/workspace/` ke `workspace/`.
- Menyesuaikan agents, arsitektur, requirement aktif, checklist, dan workflow CI ke lokasi baru.
- Menyesuaikan autoload namespace Core dari `../../core/src/` menjadi `../core/src/`.
- Mempertahankan `app/` sebagai lokasi kode legacy atau referensi yang belum dipensiunkan.

## Verifikasi

- `workspace/composer.json`, `workspace/artisan`, dan `workspace/resources/` tersedia.
- Workflow CI menggunakan `workspace/` sebagai working directory.
- Referensi `app/workspace` pada log historis dipertahankan sebagai catatan keadaan sebelumnya.

## Risiko tersisa

- Git saat ini melihat pemindahan sebagai penghapusan `app/workspace/` dan file baru di `workspace/` sampai perubahan di-stage.
