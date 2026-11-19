# Requirement Data Pembayaran Karyawan

Status: implementasi awal.

## Tujuan

Mengelola rekening terpisah untuk pembayaran gaji dan reimbursement pada data
karyawan HRIS, dengan akses granular dan jejak perubahan yang aman.

## Acceptance criteria

- Aksi Data pembayaran tersedia dari menu aksi karyawan dan membuka halaman khusus.
- Halaman memiliki panel rekening gaji dan rekening reimbursement; tiap panel
  dapat dilihat dan diubah dengan permission terpisah.
- Kedua panel memiliki tinggi independen sesuai konten masing-masing; riwayat tabel pada satu panel tidak mengikuti tinggi panel lain.
- Permission berada pada HRIS → Pegawai: `view_employee_payroll_account`,
  `update_employee_payroll_account`, `view_employee_reimbursement_account`,
  `update_employee_reimbursement_account`.
- Semua endpoint memeriksa policy dan scope perusahaan, termasuk saat menyimpan.
- Input rekening memakai komponen floating label global `.workspace-floating-field`
  agar textbox, select, dan kontrol form lain konsisten di seluruh Workspace.
- Semua field rekening memiliki validasi client-side interaktif dan validasi server-side;
  seluruh field wajib, panjang nama/bank dibatasi, dan nomor rekening harus terdiri
  dari 6–34 digit setelah separator dibuang.
- Panel reimbursement menyediakan tombol isi otomatis dari rekening gaji hanya untuk
  pengguna yang memiliki izin lihat rekening gaji dan ubah rekening reimbursement.
- Nama bank dipilih dari dropdown master Core aktif yang dapat dicari berdasarkan nama
  atau singkatan; opsi menampilkan nama dan singkatan dalam tanda kurung tanpa
  menampilkan kode transfer. Server memvalidasi ID dan status
  aktif dari master, lalu menyimpan snapshot nama/kode pada versi rekening.
- Master bank berada pada tabel berprefix `core_`, memiliki primary key string/varchar,
  kode bank disimpan sebagai teks agar nol di depan terjaga, dan data awal memuat bank
  umum Indonesia serta unit usaha syariah yang tercantum pada sumber kode transfer.
- Perubahan master bersifat incremental; rekening lama tetap mempertahankan `bank_name`
  dan hanya dipetakan ke bank master bila dapat dicocokkan tanpa menghapus data lama.
- Data pemilik dan nomor rekening dienkripsi saat tersimpan. Nomor rekening default
  disamarkan; pemegang permission lihat rekening terkait dapat membuka nomor aktif
  maupun versi riwayat setelah verifikasi password akun melalui popup keamanan.
- Endpoint buka nomor memvalidasi password saat ini, permission serta relasi karyawan,
  dibatasi percobaannya, dan mengirim respons tanpa cache. Nomor yang dibuka di popup
  dibersihkan otomatis setelah 20 detik atau ketika popup ditutup.
- Isi otomatis rekening reimbursement dari payroll juga meminta verifikasi password;
  data rekening sumber tidak ditanam sebagai nilai plaintext pada atribut HTML.
- Setiap simpan membuat versi rekening baru dan menonaktifkan versi aktif lama;
  kartu ringkasan dan badge status riwayat menandai rekening aktif dengan jelas,
  sedangkan riwayat menyamarkan nomor rekening dan tabel menyesuaikan lebar panel.
- Simpan rekening pertama meminta konfirmasi; penggantian rekening aktif meminta
  konfirmasi lalu verifikasi password. Server mewajibkan password saat versi aktif
  sudah ada; validasi client dijalankan sebelum dialog konfirmasi.
- Audit mencatat jenis rekening dan ID versi saja, tanpa nilai sensitif.
- Perubahan langsung aktif. Persetujuan perubahan rekening belum termasuk fase ini.
- Penghapusan karyawan mempertahankan record karyawan bila memiliki riwayat rekening.

