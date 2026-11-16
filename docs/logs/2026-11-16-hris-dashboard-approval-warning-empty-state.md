# Panel Early Warning kosong pada dashboard HRIS

- Menyembunyikan panel Early Warning pada ringkasan pribadi HRIS ketika daftar approval kosong.
- Panel tetap hanya ditampilkan kepada pengguna yang memiliki akses melihat approval dan memiliki pengajuan yang menunggu.
- Menghapus empty state di dalam panel karena panel tidak lagi dirender pada kondisi kosong.
- Memperbarui requirement dan checklist fitur.
- Verifikasi: `git diff --check` dan pemeriksaan sintaks Blade; test belum dijalankan.
