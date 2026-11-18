# Preferensi email notifikasi

## Tujuan

Pengguna dapat mengurangi email yang tidak relevan tanpa mematikan notifikasi email yang dibutuhkan. Infrastruktur delivery tetap terpusat di Core Notifications.

## Acceptance criteria

- Setiap kategori email memiliki kontrol aktif/nonaktif tersendiri; tidak ada sakelar global tambahan yang dapat memblokir kategori aktif.
- Preferensi kategori terpisah untuk Cuti & Sakit, Lembur, Reimbursement, Cash Advance, dan Notifikasi lainnya.
- Preferensi kategori hanya mengendalikan pengiriman email, tidak menghapus atau menonaktifkan notifikasi dalam aplikasi/Web Push.
- Kategori HRIS ditentukan dari jenis pengajuan; reimbursement dan cash advance dibedakan berdasarkan subtype klaim.
- Semua tipe notifikasi yang tidak cocok dengan kategori HRIS dipetakan ke Notifikasi lainnya.
- Nilai kategori yang belum pernah disimpan dianggap aktif agar pengaturan yang sudah ada tidak berubah diam-diam setelah deployment.
- Preferensi disimpan per user. Kategori yang belum pernah disimpan dianggap aktif.
- Preferensi kategori divalidasi server-side dengan daftar kategori yang diizinkan.
- Email konfirmasi dikirim kepada pemohon saat pengajuan hasil revisi dikirim ulang, dan email tindakan dikirim kepada approver tahap berikutnya.

## Keputusan

- Pengiriman, template, enkripsi, outbox, log, dan worker tetap menjadi layanan global.
- Klasifikasi kategori adalah metadata event notifikasi, bukan konfigurasi SMTP atau worker.
- Semua kategori aktif secara default sampai pengguna mengubahnya. Kolom sakelar global lama hanya dipertahankan untuk kompatibilitas data dan tidak mengendalikan delivery.
