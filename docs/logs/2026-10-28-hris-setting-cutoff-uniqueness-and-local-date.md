# HRIS setting: uniqueness cut-off dan format tanggal lokal

## Perubahan

- Menolak penyimpanan konfigurasi `hris.leave_submission_cutoff` jika sudah
  ada konfigurasi aktif untuk tahun yang sama.
- Pemeriksaan berlaku lintas scope Global dan perusahaan, serta diterapkan
  pada create, update, dan pemulihan konfigurasi arsip.
- Tanggal `Berlaku mulai` pada tabel menggunakan format `dd/mm/yyyy`.
- Nilai cut-off tetap memakai format teknis `YYYY-MM-DD` agar kompatibel dengan
  input tanggal dan resolusi konfigurasi server-side.

## Verifikasi

- `php -l workspace/app/Modules/Hris/Application/Services/HrisSettingsController.php`
- Menambahkan feature test untuk konflik Global-versus-perusahaan dan tampilan
  tanggal lokal. Static check dan kompilasi view berhasil; feature test lokal
  belum selesai karena proses test menunggu koneksi database.

## Risiko / catatan

- Data duplikat yang sudah ada tidak dihapus otomatis. Konfigurasi baru,
  perubahan, dan pemulihan arsip yang bertabrakan akan ditolak agar tidak
  menambah konflik.
