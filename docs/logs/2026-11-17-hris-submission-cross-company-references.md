# Referensi Project dan Opportunity lintas perusahaan pada pengajuan

## Perubahan

- Form create/edit lembur dan reimbursement memuat Project serta Opportunity lintas perusahaan, mengabaikan company scope dan membership hanya pada alur pengajuan tersebut.
- Permission `view_project` dan `view_crm_opportunity` tetap menjadi prasyarat pilihan dan penyimpanan referensi.
- Validasi server pada pengajuan/revisi memeriksa record dan permission tanpa menggunakan filter company scope; policy CRM global dan pemakaian project di luar kedua form tetap mengikuti scope masing-masing.
- Validasi client tetap memeriksa jenis/ID pilihan, dan seluruh ID tetap divalidasi ulang di server.
- Requirement dan checklist HRIS diperbarui.

## Verifikasi

- Pemeriksaan sintaks PHP, kompilasi Blade, dan `git diff --check`.
- Test suite tidak dijalankan.

## Risiko tersisa

- Tidak ada perubahan skema database. Pemakai form yang tidak memiliki permission lihat Project atau Opportunity tetap tidak dapat memilih referensi tersebut.
