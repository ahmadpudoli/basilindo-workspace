# HRIS leave: pesan konflik tanggal berdasarkan status

## Perubahan

- Pesan konflik tanggal kini mencantumkan tanggal yang beririsan.
- Tanggal pada pengajuan `approved` diberi alasan bahwa tanggal sudah digunakan
  oleh pengajuan yang disetujui.
- Tanggal pada pengajuan `pending_manager` atau `pending_hr` diberi alasan
  bahwa tanggal masih dalam proses persetujuan.

## Verifikasi

- `php -l app/Modules/HRIS/Application/Services/LeaveRequestService.php`
- `git diff --check`
