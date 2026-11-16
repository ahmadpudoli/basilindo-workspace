# Permintaan revisi oleh Approver final

- Mengizinkan Approver final meminta revisi claim dengan alasan dan klasifikasi wajib.
- Revisi administratif kembali ke `pending_final` dan mempertahankan persetujuan Atasan/Finance; revisi substansi kembali ke `pending_manager` sehingga alur Atasan dan Finance terulang.
- Menambahkan notifikasi final approver setelah revisi administratif dikirim ulang.
- Menambahkan accordion panduan jenis revisi pada modal Approver final dan menampilkan role final pada detail/timeline pemohon.
- Memperbarui requirement dan checklist. Tidak memerlukan migration schema baru.
- Verifikasi: lint PHP/Blade dan `git diff --check`; suite test belum dijalankan.
- Perbaikan lanjutan: memastikan markup modal revisi ikut dirender pada tahap `final`; sebelumnya tombol tampil tetapi target modal tidak ada di DOM sehingga Bootstrap melempar TypeError saat dibuka.
