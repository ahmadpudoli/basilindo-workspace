# Registry Worker dan Email Notifikasi — 2026-11-17

## Perubahan

- Menambahkan `workspace/config/workers.php` sebagai inventaris pusat group notifications, documents, integration, dan default beserta nama queue dan kelas job.
- Mengarahkan nama queue job yang ada ke konfigurasi pusat dan menambahkan command `workspace:queue-work` untuk worker group atau semua group.
- Menambahkan service Docker worker-notifications, worker-email-notifications, worker-documents, worker-integration, dan scheduler. Scheduler tetap memakai registrasi pusat di `routes/console.php`.
- Menambahkan antrean email notifikasi Core Notifications: pilihan email per user, Blade template, body AES-256-GCM dengan IV acak dan Base64 encoding, queue terpisah, log status khusus, dan monitor khusus.
- Memindahkan cipher menjadi service bersama Core `PayloadCipher`; key global berada pada `config/encryption.php`, SMTP pada `config/mail.php`, dan mapping Blade pada `config/notifications.php`. `workers.php` sekarang hanya mencatat worker.
- Mengonfigurasi contoh SMTP dari environment menggunakan host `mail.legospay.id` dan sender `legospay.development@legospay.id`.

## Verifikasi

- Belum menjalankan test suite atau menghubungkan SMTP eksternal pada perubahan ini.

## Risiko/asumsi tersisa

- Kredensial SMTP harus diisi melalui secret environment; port 587 adalah nilai contoh dan perlu dikonfirmasi terhadap konfigurasi SMTP server.
- `APP_DATA_ENCRYPTION_KEY` perlu diset sebelum mengantrekan payload sensitif pada environment non-local. `NOTIFICATION_EMAIL_SHARED_SECRET` dan `APP_DATA_ENCRYPTION_PREVIOUS_KEYS` dibaca sebagai key lama saat dekripsi; fallback ke `APP_KEY` hanya berlaku untuk local/testing.
- Template generik saat ini digunakan untuk seluruh notifikasi; mapping tipe dapat ditambah melalui `notifications.email.templates`.

## Penyesuaian perintah development dan worker

- `composer dev` hanya menjalankan Laravel web server dan Vite; worker queue tidak ikut berjalan.
- `composer queue-all` menjalankan group `notifications`, `email`, `documents`, dan `integration` secara eksplisit dalam satu argumen daftar koma; command menggabungkan queue-nya ke satu proses worker dan tidak menggunakan group `all`.
- Verifikasi: struktur JSON `composer.json` diperiksa setelah perubahan. Test aplikasi tidak dijalankan karena perubahan hanya pada script dan dokumentasi.
- Perbaikan deployment lokal: migration `2026_11_17_000001_create_core_email_notification_logs` diterapkan secara incremental ke database aktif setelah halaman monitor melaporkan tabel belum tersedia; `migrate:status` mengonfirmasi migration berstatus `Ran`.
- Navigasi monitor antrean dipecah menjadi submenu log job umum dan antrean email. Permission `view_queue_jobs` dan `view_email_queue` diperiksa terpisah pada menu, tautan, dan controller; migration baru memberi permission email kepada role admin dan super_admin.
- Menambahkan feature test untuk akses independen/penolakan silang pada kedua halaman serta default role admin. `php artisan test --filter=QueuePagePermissionTest` lulus (3 test, 12 assertion); Pest memberi warning saat menulis result cache di `vendor/pestphp/pest/.temp`, tetapi hasil test tetap PASS.
- Default permission disesuaikan setelah terlihat bahwa checkbox langsung pengguna tidak menampilkan permission turunan role: role `admin` tidak lagi otomatis menerima `view_email_queue`; akses antrean email hanya diberikan eksplisit, sedangkan `super_admin` tetap penuh. Migration incremental menghapus grant bawaan admin yang sudah terpasang.
- Monitor Queue & Job diperbarui dengan header kompak, KPI status, filter keyword/queue/status/rentang tanggal, sorting kolom, pagination standar, dan query string yang mempertahankan state. `php artisan test --filter=QueuePagePermissionTest` lulus (4 test, 18 assertion), termasuk kombinasi filter; Pest tetap memberi warning izin menulis cache hasil test di `vendor`.
- Kolom error pada tabel diganti menjadi dropdown Aksi dengan modal detail dan aksi antrikan ulang. Retry dibatasi pada status gagal terminal yang mencapai batas percobaan worker dan memiliki payload failed job yang tervalidasi; permission `retry_queue_jobs` terpisah ditambahkan dan modal konfirmasi Workspace dipakai sebelum dispatch ulang.
- Migration retry tracking/permission berhasil diterapkan. Feature tests lulus (5 test, 25 assertion), termasuk modal detail dan visibilitas/otorisasi retry. `npm.cmd run build` belum dapat menyelesaikan Vite karena sandbox menolak penulisan timestamp sementara ke `workspace/node_modules/.vite-temp`; halaman Blade ter-render melalui test.
- Batas percobaan aktual per job (atau fallback worker jika job tidak menentukan `tries`) ikut disimpan saat terminal failure, sehingga syarat retry mengikuti percobaan job yang bersangkutan. Migration incremental untuk kolom ini diterapkan dan feature tests tetap lulus (5 test, 25 assertion).
- Halaman antrean email diselaraskan dengan desain Queue & Job: header dan KPI ringkas, filter penerima/subjek/template, status, rentang tanggal, tabel compact, indikator sorting, dan footer pagination. Validasi filter tanggal serta whitelist jumlah baris dipertahankan; verifikasi otomatis tidak dijalankan untuk perubahan UI ini.
- Kolom Error pada tabel antrean email diganti menjadi menu aksi tiga titik vertikal. Popup detail memuat metadata pengiriman dan pesan error, tanpa menampilkan body email.
- Perbaikan SMTP development: `MAIL_HOST` pada `.env` diubah dari URL webmail (`https://mail.legospay.id`) menjadi hostname SMTP `mail.legospay.id`. Dengan port `465` dan `MAIL_SCHEME=null`, Laravel memilih skema `smtps` otomatis. Worker yang sedang aktif perlu dihentikan lalu dijalankan ulang agar membaca konfigurasi baru.
- Antrean email menampilkan aksi Antrikan ulang hanya untuk log gagal setelah tiga percobaan dan pengguna dengan permission `retry_queue_jobs`. Endpoint mengunci baris log, mengubah status kembali ke antrean, mempertahankan hitungan percobaan historis, lalu mendispatch job email dengan `afterCommit`.
- Menu aksi pada tabel antrean email dibuka ke atas agar tidak terpotong oleh overflow container tabel responsif.
- Perbaikan lanjutan dropdown email: menu aksi dipindahkan sementara ke `body` ketika dibuka dan ditempatkan relatif ke tombol, kemudian dikembalikan ketika ditutup; posisi menu tidak lagi dibatasi overflow tabel atau header panel.
- Portal dropdown dan penempatan ke atas bila ruang mencukupi diterapkan juga pada tabel Queue & Job; jika ruang atas tidak cukup, menu memakai sisi dengan ruang lebih luas dan membatasi tinggi menu agar tetap terlihat.
- Memperbaiki regresi posisi portal email: ukuran menu sebelumnya dideklarasikan `const` tetapi ditulis ulang saat tinggi dibatasi, sehingga handler berhenti sebelum menerapkan posisi. Ukuran kini mutable; koordinat `top/left` diberi prioritas eksplisit dan `right/bottom` dinetralkan pada kedua halaman.
- Mengganti mekanisme Bootstrap dropdown Queue & Job dan antrean email dengan pola aksi portal HRIS yang bekerja: tombol memakai handler klik tersendiri, menu dipindah ke `body`, lalu ditempatkan di bawah jika muat atau di atas jika tidak. Kedua halaman kini memakai partial JavaScript yang sama.
- Dropdown Queue & Job kini juga menampilkan item Antrikan ulang untuk job gagal terminal yang memenuhi syarat; item diberi tampilan terkunci dan keterangan permission ketika pengguna belum memiliki `retry_queue_jobs`, mengikuti antrean email.
- Perbaikan menu retry Queue & Job: eligibility sebelumnya membandingkan ID Redis job dari log dengan UUID pada tabel `failed_jobs`. Keduanya identifier berbeda, sehingga item Antrikan ulang tidak muncul untuk job email yang sudah gagal tiga kali. Pencocokan kini memakai `failed_job_uuid` terhadap `payload.uuid`, serta memvalidasi nama job, queue, dan koneksi yang sama pada halaman dan endpoint retry. Validasi sintaks PHP lulus; test suite tidak dijalankan.
- Perbaikan lanjutan untuk log lama: record yang dibuat sebelum kolom retry tracking tersedia bisa belum memiliki `failed_job_uuid` dan `max_attempts`. Job gagal dengan jumlah percobaan minimal batas worker default kini dicocokkan ke failed job memakai nama job, queue, koneksi, dan waktu kegagalan terdekat (maksimal selisih 10 menit). UUID dan batas percobaan kemudian di-backfill agar retry dapat dijalankan serta konsisten pada permintaan berikutnya. Validasi sintaks PHP lulus; test suite tidak dijalankan.
- Perbaikan menu retry setelah pemeriksaan screenshot: pengaitan payload failed job tidak lagi menjadi syarat untuk merender submenu. Submenu ditampilkan berdasarkan status gagal dan jumlah percobaan terhadap batas job atau default worker; endpoint tetap memverifikasi payload sebelum retry. Pencocokan log lama memakai nama job, queue, dan waktu kegagalan terdekat, tanpa syarat kesamaan nama koneksi yang dapat menghalangi log historis.
