# Perbaikan model ticket pada detail project

## Perubahan

- Policy project mengizinkan pemegang `view_any_project` melihat detail project yang belum memiliki anggota. Hak update/delete tetap mengikuti scope anggota project.
- Menambahkan override konfigurasi `core.models` pada aplikasi Workspace.
- Binding dokumen diarahkan ke `App\\Modules\\DocumentManagement\\Domain\\Models\\Document` agar eager loading dokumen pada detail project tidak menggunakan class legacy.
- Relasi `Core\\Models\\Project::tickets()` kini menggunakan `App\\Modules\\ProjectManagement\\Domain\\Models\\Ticket`.
- Binding status, prioritas, epic, catatan project, dan notifikasi juga diarahkan ke model aktif modul Project Management.

## Verifikasi

- Cache konfigurasi dibersihkan dengan `php artisan optimize:clear`.
- File konfigurasi dan model diperiksa dengan lint PHP.
- Tidak ada perubahan migration atau data database.

## Risiko tersisa

- Default konfigurasi package Core masih menyediakan fallback legacy untuk aplikasi lain. Override ini berlaku khusus runtime Workspace.
