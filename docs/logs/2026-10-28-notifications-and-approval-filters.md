# Notifikasi workspace dan filter approval HRIS

## Perubahan

- Menambahkan dropdown notifikasi pada header dengan jumlah unread dan enam notifikasi terbaru.
- Menambahkan halaman daftar notifikasi dengan filter pencarian/status dan pagination 10/25/50/100.
- Menambahkan halaman detail notifikasi yang otomatis menandai notifikasi sebagai sudah dibaca dan menyediakan link ke pengajuan atau dokumen terkait.
- Menambahkan aksi tandai semua notifikasi sebagai sudah dibaca.
- Menambahkan filter approval HRIS berdasarkan pencarian pemohon, jenis, dan tahap approval.
- Menambahkan pagination 10/25/50/100 dan sorting jenis, pemohon, serta waktu pengajuan pada tabel approval.

## Verifikasi

- Filter dan pagination mempertahankan state melalui query string.
- Notifikasi dibatasi berdasarkan `user_id` user yang sedang login.
- Link detail notifikasi menggunakan route internal yang sudah memiliki authorization.
