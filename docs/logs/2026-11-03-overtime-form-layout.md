# Penyelarasan layout form pengajuan lembur

- Form lembur disusun ulang mengikuti pola form cuti/sakit: header Kehadiran,
  satu card utama, dua kolom desktop yang responsif, section informasi pengajuan,
  jenis/lokasi lembur, informasi karyawan/atasan, rincian lembur, dan footer aksi.
- Floating label dipertahankan untuk input/select/textarea; pilihan External / Project,
  project opsional, tambah/hapus rincian, error validation, dan pengiriman tetap bekerja.
- Tidak ada perubahan aturan domain, workflow approval, migration, atau authorization.

Verifikasi: Blade view berhasil dikompilasi dan dibersihkan; PHP syntax controller
lulus; `git diff --check` tidak menemukan whitespace error pada view.
