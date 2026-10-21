# Requirement: Pembersihan root `resources/`

## Tujuan

Menghapus folder `resources/` di root repository apabila tidak menjadi source code runtime atau asset build Basilindo Workspace.

## Acceptance criteria

+ Runtime Laravel tetap menggunakan resource di `workspace/resources/`.
- Tidak ada konfigurasi, route, view, atau build script yang mengonsumsi root `resources/`.
+ File legacy di root `resources/` dihapus tanpa mengubah resource aktif di `workspace/`.
- Test atau pemeriksaan build yang relevan tetap dapat dijalankan setelah penghapusan.
- Checklist fitur dan log pekerjaan diperbarui.
