# Preferensi email notifikasi per kategori — 2026-11-17

- **Status:** Accepted
- **Keputusan:** Infrastruktur email tetap terpusat di Core Notifications. User mengatur email secara terpisah untuk kategori Cuti & Sakit, Lembur, Reimbursement, Cash Advance, dan Notifikasi lainnya.
- **Klasifikasi:** Kategori HRIS mengikuti `request_type` dan subtype klaim. Notifikasi di luar kategori HRIS masuk ke Notifikasi lainnya. Klasifikasi hanya mengendalikan email; notifikasi aplikasi dan Web Push tidak berubah.
- **Default:** Kategori yang belum disimpan aktif secara default untuk menjaga kompatibilitas. Tiap kategori menjadi satu-satunya kontrol preference delivery; kolom sakelar global lama dipertahankan sementara tetapi tidak lagi memblokir email.
- **Penyimpanan:** Preferensi kategori disimpan sebagai JSON nullable pada user Core dan ditambahkan lewat migration incremental.
