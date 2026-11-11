# 2026-11-03 — Realisasi lembur dan struktur organisasi Core

## Perubahan

- Menambahkan master unit organisasi pada Core untuk perusahaan operasional internal,
  termasuk hierarchy, status, company scope, permission granular, filter, sorting,
  pagination, serta safe archive.
- Menambahkan referensi unit organisasi pada data karyawan dengan validasi perusahaan
  dan unit aktif.
- Mengubah pengajuan lembur menjadi laporan realisasi multi-rincian yang menyimpan
  tanggal, jam mulai/selesai, uraian pekerjaan, durasi hasil pembulatan, jenis lembur,
  lokasi, dan snapshot unit/jabatan.
- Menyelesaikan workflow Atasan -> HR dengan aksi setujui, tolak, minta revisi
  substansi/administrasi, kirim ulang pemohon, notifikasi, serta approval event.
- Menambahkan daftar terfilter, detail realisasi, timeline, status Bahasa Indonesia,
  serta antrean approval HR untuk lembur.
- Menambahkan migration incremental yang membuat tabel baru, menambah kolom nullable,
  dan membackfill setiap lembur lama menjadi satu detail tanpa menghapus kolom/data.

## Verifikasi

- `php -l` pada model, controller, request, service, migration, dan test baru: lulus.
- `php artisan route:list` untuk route lembur dan unit organisasi: lulus.
- `php artisan view:cache` dengan cache sementara sistem: lulus.
- `php artisan migrate --pretend` mengungkap kelemahan migrator lintas koneksi pada
  proyek ini: operasi schema Core/HRIS tetap dieksekusi walau migration tidak tercatat.
  Tidak ada data yang dihapus. Migration kemudian dibuat idempotent dan dijalankan
  normal; ketiganya tercatat sukses pada batch 23.
- `php artisan migrate:status`: ketiga migration berstatus `Ran`.
- `php artisan test tests/Unit`: 4 test lulus dengan 6 assertion.
- Test feature HRIS terarah menemukan deadlock setup test lama: koneksi default berada
  pada kondisi `idle in transaction` saat koneksi HRIS mencoba membuat index pada tabel
  fisik yang sama. Proses dihentikan tanpa menyentuh database development. Test feature
  baru tetap ditambahkan untuk multi-rincian, routing ke HR, revisi, dan scope organisasi.
- `php artisan test tests/Feature/HrisWorkflowTest.php`: 6 test lulus dengan 15 assertion.
- Server lokal dijalankan pada `http://127.0.0.1:8080`; request ke
  `/workspace/hris/overtime` merespons `302` ke `/login`, sehingga route dan middleware
  aktif untuk akses terautentikasi.
- Form diperbarui agar identitas pemohon diambil dari akun login, alasan umum tidak lagi
  menjadi input wajib, dan penomoran lembur menggunakan master numbering Core dengan
  counter per periode.
- Menu `Core > Master penomoran` ditambahkan untuk mengatur format, token, perusahaan,
  dan periode counter; form lembur menampilkan preview nomor dari aturan aktif.

## Keputusan dan risiko tersisa

- Approval HR menyelesaikan validasi lembur. Approval Head Finance tetap menjadi
  workflow rekap penggajian terpisah dan belum dibuat karena domain payroll belum ada.
- Struktur organisasi dimiliki Core; HRIS hanya menyimpan reference ID dan snapshot.
- Migration sudah diterapkan secara incremental pada database development tanpa
  `migrate:fresh`, drop, truncate, atau penghapusan kolom/data.
