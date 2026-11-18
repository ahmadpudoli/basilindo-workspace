# Reimbursement draft tetap pada form edit

- Tanggal: 2026-11-18
- Perubahan: Simpan draft reimbursement baru mengarahkan pemohon ke halaman edit draft yang dibuat. Simpan draft dari form edit tetap pada halaman edit dan menampilkan pesan sukses di form.
- Requirement: `docs/requirements/hris-reimbursement.md`.
- Diagnosis 404: `updateDraft()` mengganti record draft lama, sedangkan redirect semula masih memakai ID lama yang sudah dihapus. Redirect kini menggunakan model hasil pembaruan.
- Verifikasi: PHP lint pada controller dan `git diff --check`.
- Risiko tersisa: Test browser dan test fitur belum dijalankan.
