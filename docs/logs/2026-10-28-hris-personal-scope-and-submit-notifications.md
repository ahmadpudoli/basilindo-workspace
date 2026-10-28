# Scope pengajuan personal dan notifikasi submit HRIS

## Perubahan

- Daftar Cuti & Sakit kini hanya menampilkan pengajuan milik user login pada mode personal.
- Tampilan seluruh perusahaan hanya tersedia untuk `super_admin` atau mode ringkasan dengan permission `view_hris_leave_summary`.
- Submit cuti/sakit, lembur, dan claim kini membuat notifikasi konfirmasi untuk user pengaju serta notifikasi tugas untuk atasan jika atasan tersedia.
- Notifikasi workflow memvalidasi bahwa recipient user masih ada sebelum dibuat/dikirim.

## Verifikasi

- Query notifikasi header dan halaman daftar dibatasi dengan `user_id` user login.
- Detail notifikasi memeriksa ownership sebelum menandai dibaca.
