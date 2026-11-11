# 2026-11-11 — Implementasi reimbursement HRIS

## Perubahan

- Mendokumentasikan requirement reimbursement multi-rincian.
- Menyelaraskan status awal reimbursement ke `pending_manager`.
- Menambahkan rencana approval Finance, threshold final berbasis company scope,
  serta proses pembayaran terpisah.
- Menambahkan migration incremental untuk rincian biaya, konteks pengajuan,
  metadata pembayaran, dan backfill claim lama.
- Menambahkan form multi-rincian, approval Finance/final berbasis scope perusahaan,
  konfigurasi approver final, serta form pembayaran Finance.
- Menyesuaikan form self-service agar karyawan diambil dari sesi login dan
  menampilkan atasan serta Finance approver perusahaan.

## Verifikasi

- Requirement dan acceptance criteria ditambahkan sebelum perubahan runtime.
- Test workflow claim yang sudah ada dijadikan baseline untuk perubahan bertahap.
- PHP lint dan Blade view cache berhasil.
- Route list memastikan route payment start, payment, dan complete terdaftar.

## Risiko tersisa

- Integrasi upload file nota ke private object storage perlu mengikuti boundary
  dokumen HRIS yang sudah ada; implementasi fase ini menyimpan reference object key.
- Konversi threshold multi-mata-uang belum diaktifkan; threshold fase awal memakai IDR.
- Suite test belum dapat dieksekusi karena baseline migration test berhenti pada
  duplicate table `ws_saved_searches`; database tidak di-reset.
