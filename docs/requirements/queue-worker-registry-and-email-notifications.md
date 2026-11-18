# Requirement Registry Worker dan Email Notifikasi

## Tujuan

Semua queue worker Workspace harus dapat ditemukan dari satu inventaris pusat. Job tetap dimiliki modul domainnya, sedangkan pengaturan queue dan kelompok worker dicatat di `workspace/config/workers.php`. Jadwal tetap didaftarkan di `workspace/routes/console.php`.

## Acceptance criteria

- Inventaris mencatat setiap worker group, queue, dan kelas job yang menggunakan queue tersebut.
- Worker Docker menjalankan group dari `config/workers.php`; `composer dev` hanya menjalankan web server dan Vite, sedangkan `composer queue-all` menjalankan group worker yang disebutkan eksplisit.
- `composer queue-all` tidak menggunakan group agregat `all`; setiap group worker yang dijalankan terlihat eksplisit sebagai daftar dipisahkan koma. Command menggabungkan queue group tersebut ke satu proses worker lokal.
- Setiap job baru atau perubahan queue wajib memperbarui inventaris pusat dan dokumentasi/checklist.
- Email notifikasi diproses worker group `email` pada queue `email-notifications`, terpisah dari Web Push.
- Email notifikasi hanya dibuat jika preferensi email user aktif dan alamat email tersedia.
- Body email dirender dari Blade dan disimpan di log antrean dalam format AES-256-GCM terenkripsi yang di-Base64.
- Key enkripsi bersama berasal dari `APP_DATA_ENCRYPTION_KEY` melalui `config/encryption.php`. IV dibuat acak per payload, lalu disimpan bersama ciphertext dalam Base64; IV statis tidak digunakan.
- Rotasi key memakai `APP_DATA_ENCRYPTION_PREVIOUS_KEYS` untuk membaca ciphertext lama; key lama hanya boleh dikeluarkan setelah payload lama selesai diproses atau dienkripsi ulang.
- Konfigurasi koneksi SMTP dan alamat pengirim berada di `config/mail.php`; pemetaan Blade template email berada di `config/notifications.php`. `config/workers.php` hanya berisi inventaris worker.
- Worker email hanya mengirim rekaman antrean yang sudah disiapkan; status `queued`, `processing`, `sent`, dan `failed` dapat dipantau tanpa menampilkan body.
- Navigasi Queue & Job memiliki submenu log job umum (`view_queue_jobs`) dan antrean email (`view_email_queue`); endpoint dan tautan masing-masing memeriksa permission yang sesuai.
- Role `admin` hanya memperoleh akses log job umum secara default. Akses antrean email diberikan secara eksplisit melalui role; `super_admin` tetap memiliki seluruh permission.
- Halaman log job menyediakan pencarian nama/ID job, filter queue, status, rentang tanggal, sorting server-side, serta pagination 10/25/50/100; seluruh state dipertahankan di query string.
- Tabel log job menyediakan aksi detail dalam modal dan retry terkonfirmasi hanya untuk job terminal yang gagal, mencapai batas percobaan, serta masih tersedia di failed-job provider. Menu retry terlihat pada kondisi tersebut; pengguna tanpa permission `retry_queue_jobs` melihat aksi terkunci dengan keterangan akses yang diperlukan.
- Log email mencatat penerima, template, subject, status, attempts, waktu, dan metadata error tanpa menyimpan isi email.
- Halaman antrean email memakai header, KPI, panel filter, tabel ringkas, sorting, dan pagination yang konsisten dengan halaman Queue & Job; pencarian mencakup penerima, subjek, dan template, sedangkan filter status serta rentang tanggal dipertahankan di query string.
- Email berstatus gagal setelah tiga percobaan dapat diantrikan ulang melalui menu aksi dengan konfirmasi dan permission `retry_queue_jobs`; status email selain gagal atau belum mencapai batas percobaan tidak dapat diantrikan ulang.
- SMTP dikonfigurasi melalui environment. Host default `mail.legospay.id`, sender `legospay.development@legospay.id`; port dan password tetap dapat dikonfigurasi.

## Asumsi

- Email notifikasi pertama memakai template Blade generik yang dapat dipetakan per tipe notifikasi melalui `notifications.email.templates`.
- Pengguna baru dan yang sudah ada menerima email notifikasi secara default; tiap pengguna dapat mematikannya sendiri.
- Port SMTP contoh menggunakan 587 dan perlu disesuaikan jika server SMTP menetapkan port/TLS berbeda.
