# Tabel approval HRIS terpadu

## Perubahan

- Menggabungkan approval manager dan HR ke satu tabel antrean.
- Menampilkan jenis pengajuan, pemohon, tanggal pengajuan, tahap approval, waktu submit, dan aksi.
- Aksi `Setujui` tetap tersedia langsung pada baris tabel.
- Aksi `Tolak` membuka modal dengan alasan wajib.
- Aksi `Minta revisi` membuka modal dengan jenis revisi dan catatan wajib.
- Menambahkan tombol `Detail` yang membuka popup detail tanpa navigasi ke halaman lain.
- Menampilkan setiap tanggal cuti/sakit sebagai badge terpisah pada tabel dan popup detail.

## Verifikasi

- Data approval manager dan HR digabung serta diurutkan berdasarkan waktu pengajuan terbaru.
- Controller tetap menggunakan endpoint approval yang sama sehingga authorization dan workflow service tetap berlaku.
- Kompilasi Blade dilakukan melalui compiler Laravel tanpa mengubah database.
