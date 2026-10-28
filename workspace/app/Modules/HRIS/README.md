# HRIS

Modul HRIS memakai Core/Identity dan Company sebagai shared platform; tidak
membuat login, koneksi database, atau aplikasi baru.

## Implementasi saat ini

Implementasi inti mencakup:

- master karyawan yang terhubung ke akun user dan perusahaan operasional;
- parameter HRIS per perusahaan dan tanggal berlaku;
- daftar dan pengajuan cuti/sakit;
- kuota cuti tahunan yang dapat dikonfigurasi, dengan default 12 hari;
- pencatatan sakit tanpa kuota;
- validasi overlap tanggal untuk pengajuan aktif;
- status pengajuan dan audit-ready approval fields.
- approval manager/HR/Finance/final berbasis status workflow;
- lembur dengan pembulatan dan batas maksimum;
- reimbursement dan cash advance dengan pertanggungjawaban;
- approval event dan notifikasi in-app berbasis ID referensi;
- Web Push PWA per user/device melalui service worker, queue job, dan VAPID.

## Web Push

Isi `WEBPUSH_VAPID_SUBJECT`, `WEBPUSH_VAPID_PUBLIC_KEY`, dan
`WEBPUSH_VAPID_PRIVATE_KEY` pada environment deployment. Jalankan queue worker
agar approval dan perubahan status dapat dikirim ke device. Browser harus
berjalan melalui HTTPS (localhost diperbolehkan untuk development), lalu
pengguna mengaktifkan notifikasi melalui ikon lonceng pada topbar.

Key dapat dibuat sekali di luar repository dengan:

```bash
php -r "require 'vendor/autoload.php'; echo json_encode(\Minishlink\WebPush\VAPID::createVapidKeys(), JSON_PRETTY_PRINT), PHP_EOL;"
```

Notifikasi in-app tetap disimpan sebagai fallback. Endpoint browser yang
kadaluarsa (HTTP 404/410) dibersihkan otomatis oleh job.

## Boundary

Kode modul berada di `workspace/app/Modules/HRIS`. Migration domain berada di
`core/database/migrations/file-organizer` karena Workspace menjadi owner
migration database terpusat. Model HRIS menggunakan koneksi domain Workspace
dan hanya menyimpan reference ID ke `core.users` serta `core.companies`.
