# HRIS leave revision: approval event model import

## Perubahan

- Menambahkan import `App\Modules\HRIS\Domain\Models\ApprovalEvent` pada
  `LeaveRequestService`.
- Submit revisi kini dapat mencatat event `resubmitted` setelah perubahan
  tanggal dan status.
- Perubahan tetap berada di dalam transaksi HRIS agar kegagalan pencatatan
  event tidak meninggalkan data revisi setengah tersimpan.

## Verifikasi

- `php -l app/Modules/HRIS/Application/Services/LeaveRequestService.php`
- `git diff --check`
