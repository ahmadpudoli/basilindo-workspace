# Early warning approval HRIS

## Perubahan

- Panel early warning dashboard kini memuat reimbursement dan cash advance yang menunggu Finance atau approver final.
- Scope perusahaan mengikuti konfigurasi approver workflow claim yang aktif, selaras dengan inbox Persetujuan HRIS.
- Requirement dan checklist memperjelas cakupan semua tahap approval: atasan, HR, Finance, dan final.

## Verifikasi

- Meninjau query dashboard dan inbox; inbox sudah memiliki sumber untuk tahap Finance dan final, sementara dashboard sebelumnya belum.
- Test otomatis tidak dijalankan.

## Risiko tersisa

- Cakupan approval mengikuti approver dan bawahan yang sama dengan inbox; user tanpa scope inbox tidak akan melihat pengajuan pada panel.
