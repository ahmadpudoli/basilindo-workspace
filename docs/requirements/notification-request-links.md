# Tautan detail pengajuan pada notifikasi

## Tujuan

Penerima notifikasi dapat langsung membuka pengajuan yang dimaksud. Notifikasi untuk tindakan approval harus membuka detail approval pada tahap yang sesuai.

## Acceptance criteria

- Notifikasi aplikasi, Web Push, dan email untuk pengajuan HRIS menyertakan tautan ke detail pengajuan yang sesuai.
- Notifikasi approval yang masih aktif membuka halaman detail approval dengan jenis pengajuan, ID, dan tahap approval yang benar.
- Notifikasi approval yang tahapnya sudah tidak aktif mengarah ke daftar approval, bukan halaman detail yang tidak lagi dapat ditindaklanjuti.
- Email menampilkan tombol/tautan yang jelas di dalam body email; Web Push memakai URL tujuan yang sama.
- Email notifikasi memakai hierarki informasi yang jelas, layout responsif berbasis tabel dengan warna netral dan aksen brand seperlunya, serta subjek yang merangkum judul notifikasi.
- Tautan tetap melewati route dan authorization aplikasi; isi notifikasi tidak membuka akses baru.
- Jenis notifikasi yang tidak memiliki tujuan pengajuan tidak menampilkan tautan palsu.

## Asumsi

- Detail cuti/sakit, lembur, dan klaim menggunakan route detail HRIS yang sudah ada.
- Link email memakai absolute URL dari konfigurasi `APP_URL` pada environment pengirim.
