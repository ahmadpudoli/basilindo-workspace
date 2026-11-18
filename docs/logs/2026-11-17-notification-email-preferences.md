# Preferensi email notifikasi per kategori

## Perubahan

- Preferensi akun menyediakan switch mandiri per kategori Cuti & Sakit, Lembur, Reimbursement, Cash Advance, dan Notifikasi lainnya.
- Kategori pengajuan HRIS ditambahkan ke metadata notifikasi oleh workflow; subtype claim membedakan reimbursement dan cash advance.
- Dispatcher email hanya menerapkan preferensi kategori; sakelar global lama tidak lagi memblokir kategori yang aktif. Notifikasi dalam aplikasi dan Web Push tetap independen.
- Pengaturan kategori disimpan di kolom JSON nullable baru pada user Core. Kategori yang belum memiliki nilai tetap aktif untuk kompatibilitas.
- Resubmit cuti/sakit mengirim email konfirmasi ke pemohon dan notifikasi email ke approver tahap berikutnya.
- Requirement, keputusan, dan checklist diperbarui.

## Verifikasi

- Pemeriksaan sintaks PHP, kompilasi Blade, dan `git diff --check` dilakukan setelah perubahan.
- Migration `2026_11_17_000006_add_core_user_email_notification_preferences` sudah dijalankan pada database development lokal dan tercatat berstatus `Ran`.
- Perubahan delivery diverifikasi lewat penelusuran alur notification-created dan pemeriksaan sintaks/diff; test suite tidak dijalankan.

## Risiko tersisa

- Migration yang sama tetap perlu dijalankan pada setiap environment selain development lokal.
- Test suite tidak dijalankan.
