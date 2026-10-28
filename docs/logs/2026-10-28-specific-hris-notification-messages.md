# Pesan notifikasi workflow HRIS yang spesifik

## Perubahan

- Notifikasi persetujuan/penolakan sekarang mencantumkan jenis pengajuan, nama actor, dan peran actor.
- Notifikasi revisi mencantumkan pihak yang meminta revisi, perannya, serta catatan revisi.
- Notifikasi ke approver berikutnya mencantumkan siapa yang telah menyetujui dan tahap berikutnya.
- Notifikasi submit ke atasan mencantumkan nama karyawan yang mengajukan dan peran penerima.
- Metadata notifikasi menyimpan `action`, `actor_id`, `actor_name`, dan `step` untuk kebutuhan detail/riwayat.

## Verifikasi

- PHP lint dijalankan pada service workflow dan seluruh service submit HRIS.
