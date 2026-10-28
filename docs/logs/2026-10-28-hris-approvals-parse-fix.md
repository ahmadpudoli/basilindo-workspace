# Perbaikan parse view approval HRIS

## Perubahan

- Memindahkan tampilan approval HRIS ke `modules.hris-approvals` dengan struktur Blade conditional yang valid dan mudah dipelihara.
- Mengarahkan `HrisOperationsController@approvals` ke view baru agar route `/workspace/hris/approvals` tidak lagi menggunakan template dengan conditional inline yang menghasilkan `unexpected token endif`.
- Mempertahankan alur approval manager dan HR, termasuk aksi tolak, minta revisi, dan setujui.

## Verifikasi

- PHP lint berhasil untuk view baru dan controller.
- View cache tidak dapat diselesaikan pada environment lokal karena `storage/framework/views` tidak dapat ditulis (`tempnam(): file created in the system's temporary directory`); bukan error sintaks Blade.
