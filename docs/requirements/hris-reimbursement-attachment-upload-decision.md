# Keputusan desain unggah bukti reimbursement

Status: diterapkan — 2026-11-12.

- Browser memvalidasi form lebih dulu, kemudian mengunggah setiap bukti secara terpisah ke prefix temporary private MinIO. Pengajuan final hanya mengirim metadata dan token ULID yang terikat ke user.
- Setiap file dibatasi 10 MB dan keseluruhan bukti satu pengajuan dibatasi 50 MB. Pilih file membuka direktori perangkat; Ambil foto meminta izin browser dan memakai `getUserMedia` untuk pratinjau kamera langsung (kamera belakang diutamakan pada ponsel), dengan fallback pemilih kamera/file native jika browser tidak berada di secure context atau API kamera tidak tersedia.
- Lampiran wajib untuk setiap rincian. Gambar yang dipilih/diambil menampilkan thumbnail, file lain menampilkan ikon dan nama; pengguna dapat menghapusnya. Menghapus bukti tersimpan menandai penggantian, sehingga validasi client dan server meminta lampiran baru sebelum penyimpanan.
- Server memeriksa kepemilikan token dan keberadaan file, menyalin file ke prefix permanen setelah validasi, lalu menghapus temporary object. Akses unduh diperiksa berdasarkan pemohon, reviewer aktif, permission claim, dan scope perusahaan.
- Temporary claim uploads kedaluwarsa setelah 30 menit; scheduler membersihkan object yang tertinggal setiap 10 menit.
- Chunk/resumable upload tidak dipakai pada fase ini karena ukuran satu file dibatasi 10 MB dan upload serial per file menghindari satu request multipart besar. Jika kebutuhan file besar atau koneksi terputus terbukti, protokol resumable dapat ditambahkan kemudian.
