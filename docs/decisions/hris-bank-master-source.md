# Sumber dan identitas master bank HRIS

Status: diterapkan.

## Keputusan

- Master disimpan pada tabel `core_banks`, menggunakan prefix koneksi Core dan
  primary key `varchar` non-numerik.
- Kode bank transfer disimpan sebagai teks tiga karakter agar kode seperti `002`
  dan `014` mempertahankan nol di depan.
- Data awal menggunakan nama dan kode pada Tabel Sandi Bank BCA update
  31 Maret 2026. BIC dari sumber juga disimpan bila tersedia:
  [Tabel Sandi Bank BCA (31 Maret 2026)](https://pustaka.bca.co.id/bisnis/layanan/e-banking-bisnis/klikbca-bisnis/data-bank-update-31-maret-2026.pdf).
- Daftar mencakup bank umum, bank syariah, bank pembangunan daerah, bank asing
  peserta transfer, serta unit usaha syariah yang memiliki entri kode tersendiri.
  Bank Indonesia dikecualikan dari pilihan penerima rekening.
- Cakupan awal mengikuti daftar kode transfer bank umum tersebut. Direktori
  Perbankan Indonesia OJK mencakup Bank Umum dan tidak mencakup BPR, sehingga
  daftar ini bukan registri seluruh lembaga keuangan:
  [Direktori Perbankan Indonesia OJK](https://ojk.go.id/id/kanal/perbankan/data-dan-statistik/direktori-perbankan-indonesia/default.aspx).
- Master di-seed melalui migration sebagai snapshot yang dapat ditinjau dan
  diperbarui melalui migration incremental berikutnya. Master tidak dihapus saat
  bank tidak aktif; statusnya dapat menjadi nonaktif.
- Sumber data snapshot: [Tabel Sandi Bank BCA update 31 Maret 2026](https://pustaka.bca.co.id/bisnis/layanan/e-banking-bisnis/klikbca-bisnis/data-bank-update-31-maret-2026.pdf).
- Cakupan awal mengikuti daftar kode transfer bank umum tersebut; daftar ini
  bukan registri semua lembaga keuangan. Direktori Perbankan Indonesia OJK
  mencakup Bank Umum dan mengecualikan BPR: [Direktori Perbankan Indonesia OJK](https://ojk.go.id/id/kanal/perbankan/data-dan-statistik/direktori-perbankan-indonesia/default.aspx).
- Sumber data snapshot: [Tabel Sandi Bank BCA update 31 Maret 2026](https://pustaka.bca.co.id/bisnis/layanan/e-banking-bisnis/klikbca-bisnis/data-bank-update-31-maret-2026.pdf).
- Cakupan awal mengikuti daftar kode transfer bank umum tersebut; daftar ini
  bukan registri semua lembaga keuangan. Direktori Perbankan Indonesia OJK
  mencakup Bank Umum dan mengecualikan BPR: [Direktori Perbankan Indonesia OJK](https://ojk.go.id/id/kanal/perbankan/data-dan-statistik/direktori-perbankan-indonesia/default.aspx).
- Rekening karyawan menyimpan referensi ID bank bila cocok, ditambah snapshot nama
  dan kode agar riwayat tidak berubah saat master diperbarui. Data legacy tidak
  dihapus dan hanya dibackfill saat pencocokan nama bersifat tepat.

## Alternatif

- Mengambil daftar setiap deployment dari API eksternal ditolak karena menambah
  ketergantungan runtime dan dapat mengubah opsi rekening tanpa review migration.
- Menggunakan kode transfer sebagai primary key ditolak karena satu institusi
  dapat memiliki beberapa entri/unit usaha syariah dan primary key harus berupa
  string terpisah dari kode bisnis.

