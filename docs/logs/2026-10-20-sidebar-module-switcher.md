# Sidebar collapse dan module launcher

## Perubahan

- Menambahkan tombol minimize/expand sidebar desktop pada header Workspace.
- Menyimpan state sidebar di `localStorage` agar preferensi pengguna tetap konsisten.
- Mempertahankan offcanvas sidebar pada viewport mobile.
- Menambahkan module launcher berbentuk grid pada header untuk berpindah ke Company, CRM, Project Management, dan Document Management.
- Menampilkan modul aktif, icon SVG internal, deskripsi singkat, dan link ke seluruh modul.

## Verifikasi

- State collapse dibatasi pada desktop (`min-width: 992px`); mobile tetap menggunakan Bootstrap offcanvas.
- Dropdown menggunakan Bootstrap dan seluruh URL berasal dari route Workspace.
- Tidak ada perubahan pada authorization atau domain logic.
