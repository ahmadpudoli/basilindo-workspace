# Keterangan umum reimbursement tidak disalin dari rincian

- Tanggal: 2026-11-18
- Perubahan: Penyimpanan klaim tidak lagi mengisi `purpose` dari deskripsi rincian pertama. Pada form edit, nilai lama yang sama persis dengan deskripsi rincian pertama diperlakukan sebagai hasil fallback otomatis dan ditampilkan kosong; pemohon dapat memasukkan kembali keterangan jika memang menghendaki teks yang sama.
- Requirement: `docs/requirements/hris-reimbursement.md`.
- Verifikasi: PHP lint pada service/controller dan `git diff --check`.
- Asumsi: Keterangan lama yang sama persis dengan deskripsi rincian pertama berasal dari fallback otomatis sebelumnya. Jika sebelumnya sengaja ditulis sama, pemohon dapat mengetik ulang pada form revisi/edit.
