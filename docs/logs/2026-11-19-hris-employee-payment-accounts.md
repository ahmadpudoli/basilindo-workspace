# Data pembayaran karyawan HRIS

## Perubahan

- Menambahkan halaman Data pembayaran dari menu aksi karyawan, dengan panel
  rekening gaji dan reimbursement.
- Menambahkan empat permission view/update yang dikelompokkan dalam HRIS → Pegawai
  pada pengelolaan role dan pengguna.
- Menambahkan tabel versi rekening di koneksi HRIS; nama pemilik dan nomor rekening
  memakai encrypted cast, tampilan riwayat menyamarkan nomor rekening.
- Perubahan mengganti rekening aktif dan menyimpan versi terdahulu. Audit mencatat
  jenis serta ID versi tanpa nilai rekening.
- Karyawan yang memiliki riwayat rekening diarsipkan saat penghapusan diminta.

## Verifikasi

- Ditinjau route, policy, scope perusahaan, permission grouping, dan audit payload.
- Setelah migrasi aktif, cache permission Spatie dibersihkan. Migrasi permission
  juga menginvalidasi cache agar permission yang baru ditambahkan langsung dikenali.
- Test otomatis tidak dijalankan.

## Risiko / asumsi

- Perubahan langsung aktif; persetujuan perubahan rekening belum termasuk requirement.
- Pengguna dengan permission update dapat menulis rekening, tetapi hanya permission
  view yang menampilkan identitas dan nomor tersamarkan.
