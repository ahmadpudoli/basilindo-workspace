# Ringkasan Reimbursement dan Cash Advance

- **Perubahan:** Mengganti tampilan tabel pada kedua route ringkasan dengan kartu pengajuan yang responsif, memperkaya informasi karyawan/perusahaan/status/kategori/tujuan/referensi, serta menambahkan metrik jumlah, nominal per mata uang, proses, dan disetujui/dibayar.
- **Scope:** Query ringkasan dibatasi ke perusahaan yang dapat diakses pengguna dan tipe route yang dipilih. Draft dikecualikan dari query dan opsi status.
- **Requirement:** `docs/requirements/hris-claim-summary-dashboard.md`.
- **Verifikasi:** Belum menjalankan test suite sesuai instruksi kerja; pemeriksaan sintaks PHP dan diff whitespace dilakukan setelah implementasi.
- **Risiko tersisa:** Tampilan browser dan data nyata belum dicek langsung.

## Penyesuaian tampilan

- **Perubahan:** Atas arahan pengguna, daftar kartu diganti menjadi tabel responsif dengan kolom informasi pengajuan dan pagination query-string. Metrik ringkasan tetap dipertahankan.
- **Verifikasi:** Belum dilakukan render browser.

- **Perbaikan kolom:** Tabel memakai layout tetap dengan lebar kolom eksplisit. Status mendapat 10% lebar tabel dan Aksi 10%, sehingga badge tidak mendorong kolom tombol keluar; pada mobile tabel tetap dapat digeser horizontal.
- **Menu aksi:** Kolom Aksi diringkas menjadi tombol dropdown ikon tiga titik vertikal, dengan tautan detail di menu dan authorization yang sama.
- **Penyempurnaan menu:** Ikon memakai SVG inline agar tidak bergantung pada Bootstrap Icons. Dropdown diberi heading, ikon, deskripsi singkat, dan gaya modern; Cetak tersedia di menu untuk reimbursement yang sudah lolos seluruh syarat cetak.
- **Perbaikan clipping:** Menu aksi dipindahkan sementara ke `document.body`, diposisikan terhadap viewport, dan menghitung ulang posisi saat scroll/resize agar tampil di depan tabel serta berpindah ke atas jika ruang bawah tidak cukup.
- **Penyegaran ringkasan:** Header dibuat lebih padat dengan warna aksen, empat metrik digabung menjadi satu panel bersekat, dan filter memakai grid desktop/mobile dengan tombol aksi tetap sejajar.
- **Filter perusahaan:** Badge perusahaan pada banner disembunyikan; filter multi-select Perusahaan ditambahkan dan pilihan divalidasi terhadap daftar perusahaan operasional yang dapat diakses pengguna. Tombol Reset/Terapkan dirapatkan tanpa celah.
- **Filter lanjutan:** Ditambahkan pencarian kata kunci nama pemohon/nomor/status/kategori, batas nominal total minimum dan maksimum, serta tata letak filter dua baris yang responsif.
- **Penataan filter:** Pada desktop lebar, kata kunci/perusahaan/status/kategori/jumlah baris berada di baris pertama; periode dan rentang nominal berada di baris kedua bersama tombol aksi.
- **Periode dan aksi filter:** Nilai tanggal mulai/akhir kini mengikuti rentang ikhtisar (awal bulan sampai hari ini), dan tombol Reset/Terapkan ditempatkan langsung setelah input nominal maksimum.
- **Tabel mobile dan aksi:** Kolom kategori, nominal, status, dan aksi diberi lebar minimum pada tabel yang digulir horizontal agar konten tidak bertumpuk; tombol tiga titik dan menu aksi dipadatkan.
- **Menu aksi daftar klaim:** Tabel Semua, Reimbursement, dan Cash Advance kini mengubah aksi Detail/Revisi/pembayaran/Cetak/draft menjadi menu dropdown compact dengan pola tombol tiga titik, ikon, posisi viewport, dan authorization tiap aksi tetap dijaga.
- **Standar tabel berikutnya:** `docs/requirements/workspace-ui-pagination.md` kini mewajibkan satu dropdown aksi compact per baris serta menu yang tidak terpotong container tabel.
- **Proporsi kolom daftar klaim:** Kolom Karyawan/Jenis/Kategori/Nominal/Status/Aksi diberi lebar 19%/15%/24%/17%/15%/10%, dengan batas lebar minimum agar angka tetap terbaca dan tabel mobile dapat digulir horizontal.
- **Koreksi lebar kolom:** Selector dibatasi pada tabel daftar setelah panel filter dan lebar dikunci pada header tabel agar Status tidak melebar dan Aksi mendapat ruang sesuai proporsinya.
- **Scope daftar pribadi:** Daftar kerja Semua/Reimbursement/Cash Advance dibatasi ke klaim dengan pemohon/karyawan yang terhubung ke pengguna login (serta draft yang dibuatnya); company scope tidak memperluas hasil. Policy detail mengizinkan pemohon melihat klaimnya sendiri lintas company scope, sedangkan halaman ringkasan tetap mengikuti scope perusahaan.
