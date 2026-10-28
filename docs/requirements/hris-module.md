# Requirement Modul HRIS

Status: implementasi inti berjalan; verifikasi unit/feature tersedia.

## Tujuan

Menyediakan sumber data karyawan dan alur awal pengajuan cuti/sakit dalam
Basilindo Workspace. Modul memakai login, user, company scope, role, permission,
audit, dan layout aplikasi yang sudah tersedia.

## Keputusan requirement

- Istilah bisnis yang digunakan adalah **karyawan**, bukan mahasiswa.
- Satu karyawan terhubung ke satu perusahaan operasional dan dapat dikaitkan
  dengan satu akun Workspace.
- Karyawan memiliki atasan langsung untuk kebutuhan workflow fase berikutnya.
- Kuota cuti tahunan dapat dikonfigurasi per perusahaan dan tahun; default
  baseline adalah 12 hari.
- Sakit tidak memiliki kuota; sistem hanya mencatat jumlah hari dan riwayatnya.
- Pengajuan cuti/sakit dapat menggunakan tanggal lampau (backdate), sesuai
  kebijakan yang dikonfigurasi.
- Pengajuan aktif tidak boleh overlap dengan pengajuan aktif lain milik karyawan
  yang sama. Satu tanggal yang beririsan sudah cukup untuk menolak pengajuan.
- Pengajuan ditolak, dibatalkan, kadaluarsa, atau draft tidak memblokir tanggal.
- Parameter hari libur, cut-off, aturan approval, dan kebijakan lembur akan
  disimpan sebagai pengaturan, bukan hardcode.
- Reimbursement dan cash advance menjadi fase lanjutan.
- Notifikasi in-app dikirim dengan metadata ID saja; Web Push PWA memakai
  subscription browser terenkripsi, job queue, service worker, dan VAPID.

## Acceptance criteria fase 1

### Master karyawan

- Pengguna berizin dapat melihat karyawan sesuai company scope.
- Data karyawan memiliki nomor karyawan, nama, perusahaan, jabatan, atasan,
  tanggal mulai, dan status.
- Karyawan yang sudah memiliki relasi tidak dihapus permanen; arsip memakai
  soft delete/status archived.
- Nomor karyawan unik dalam perusahaan.

### Pengajuan cuti/sakit

- Pengguna dapat membuat pengajuan dengan tipe `leave` atau `sick`, satu atau
  beberapa tanggal pengajuan, alasan, dan lampiran reference opsional.
- Setiap tanggal disimpan sebagai detail `leave_request_dates`; jumlah hari
  dihitung server-side dari jumlah detail tanggal yang unik.
- Pengajuan lintas tahun kalender ditolak pada fase pertama karena saldo kuota
  dihitung per tahun; dukungan split-year menjadi requirement lanjutan.
- Cuti memeriksa saldo tahun berjalan; sakit tidak mengurangi saldo.
- Pengajuan baru ditolak jika salah satu tanggal overlap dengan detail tanggal
  pada pengajuan aktif.
- Setelah validasi berhasil, pengajuan langsung masuk status `pending_manager`
  (Menunggu atasan).
- Status workflow cuti/sakit yang didukung hanya Menunggu atasan, Menunggu HR,
  Revisi, Disetujui, dan Ditolak. Event submit tetap direpresentasikan oleh
  waktu dibuat dan timeline, bukan sebagai status terpisah.
- Atasan atau HR dapat meminta revisi dengan alasan wajib dan klasifikasi
  `substantive` atau `administrative`; pemohon menjadi satu-satunya pihak yang
  mengubah isi pengajuan.
- Revisi substansi selalu kembali ke approval atasan. Revisi administratif
  kembali ke pihak yang meminta revisi; untuk HR berarti langsung ke HR.
- Setiap permintaan revisi dan pengiriman ulang disimpan sebagai approval event
  beserta aktor, waktu, jenis revisi, dan alasan.
- Status dan tanggal pengajuan disimpan untuk kebutuhan timeline approval.
- Halaman daftar menyediakan filter personal berdasarkan jenis, status, dan
  periode, pagination standar, serta akses detail pengajuan.
- Ringkasan tahun aktif menampilkan kuota, penggunaan, dan sisa cuti; sakit
  hanya dihitung sebagai riwayat tanpa mengurangi kuota.
- Detail pengajuan menampilkan timeline dibuat, verifikasi manager/atasan,
  verifikasi HR, dan keputusan akhir beserta waktu, aktor, serta komentar.
- Badge status pada ringkasan detail pengajuan mengikuti lebar konten label dan
  tidak melebar memenuhi kolom.
- Form revisi pengajuan memakai struktur visual dan urutan section yang sama
  dengan form create, dengan jenis pengajuan tetap terkunci.
- Pengajuan cuti ditolak server-side bila belum ada konfigurasi kuota aktif yang
  berlaku untuk tahun tanggal pengajuan.
- Pesan validasi form tidak ditampilkan sebagai panel global duplikat; error
  ditampilkan satu kali pada field atau section yang relevan.
- Pengajuan cuti memerlukan konfigurasi `hris.leave_submission_cutoff` yang
  valid untuk tahun tanggal cuti; konfigurasi kosong atau tidak valid menolak
  pengajuan, sedangkan pengajuan sakit tidak terpengaruh cut-off cuti.
- Pengajuan cuti setelah tanggal cut-off tahun terkait ditolak server-side;
  tanggal cut-off berlaku sampai akhir hari pada timezone aplikasi.
- Pengajuan ulang yang tanggalnya beririsan dengan pengajuan aktif lain
  ditolak kembali ke form revisi dengan pesan validasi, bukan menjadi error
  internal server.
- Pesan konflik tanggal membedakan tanggal yang sudah disetujui dan tanggal
  yang masih menunggu proses persetujuan atasan atau pemeriksaan HR.
- Pengiriman ulang revisi mencatat event approval melalui model domain HRIS
  yang benar dan dijalankan dalam transaksi yang sama dengan perubahan data.
- Waktu pada kartu dan timeline `Pengajuan dibuat` selalu memakai waktu
  pengajuan pertama; pengiriman ulang hanya memperbarui `revision_submitted_at`.
- Notifikasi yang ditujukan kepada approver membuka halaman detail approval
  pada child menu Persetujuan HRIS, sedangkan notifikasi status untuk pemohon
  tetap membuka detail pengajuan biasa.
- Detail approval menggunakan halaman terpisah dari detail pemohon dan
  menyediakan tindakan setujui, tolak, serta minta revisi.
- Tindakan approval pada detail dipisahkan menjadi blok berjudul dengan
  penjelasan singkat agar perbedaan Setujui, Tolak, dan Minta revisi jelas.
- Blok Minta revisi menyediakan panel informasi collapsible untuk menjelaskan
  perbedaan revisi substansi dan revisi administrasi.
- Aksi Tolak dan Minta revisi pada halaman detail approval menggunakan modal
  konfirmasi/input agar halaman tetap ringkas; setelah keputusan tersimpan,
  pengguna kembali ke daftar Persetujuan HRIS.
- Tabel parameter HRIS menampilkan nama bisnis yang sama dengan label dropdown
  sekaligus tetap mempertahankan kode parameter teknis.
- Field nilai parameter HRIS menyesuaikan tipe parameter: tanggal untuk cut-off,
  angka untuk kuota/maksimum/ambang, dan teks untuk parameter lain.
- Setiap parameter HRIS menampilkan deskripsi panduan yang berubah sesuai pilihan
  pengguna.
- `hris.overtime_maximum_minutes` bersifat opsional; jika belum dikonfigurasi,
  sistem tidak menerapkan batas maksimum durasi lembur.
- `hris.leave_submission_cutoff` hanya boleh memiliki satu konfigurasi aktif
  untuk setiap tahun kalender; aturan ini berlaku lintas scope Global dan
  perusahaan.
- Tanggal berlaku pada tabel parameter ditampilkan dalam format lokal Indonesia
  `dd/mm/yyyy`; nilai cut-off tetap menggunakan format teknis `YYYY-MM-DD`.
- Kegagalan validasi pada halaman Pengaturan HRIS menampilkan warning yang
  menjelaskan bahwa parameter belum disimpan beserta alasan validasinya.
- Aksi arsip parameter HRIS menggunakan modal konfirmasi Workspace, bukan
  dialog konfirmasi native browser.
- User yang memiliki bawahan atau permission approval dapat membuka daftar
  approval khusus untuk cuti, sakit, lembur, reimbursement, dan cash advance.
- Permission `view_hris_approvals` menjadi permission granular untuk menu inbox
  approval; data approval manager dibatasi pada bawahan langsung user.

## Status

Workflow cuti/sakit menggunakan `pending_manager`, `pending_hr`,
`needs_revision`, `approved`, dan `rejected`. `needs_revision` bukan keputusan
akhir; pemohon dapat memperbaiki dan mengirim ulang sesuai klasifikasi revisi.

## Parameter yang disiapkan

Kunci parameter menggunakan namespace `hris.*`, memiliki nilai JSON dan dapat
memiliki tanggal berlaku, antara lain:

- `hris.annual_leave_quota`;
- `hris.leave_backdate_days`;
- `hris.leave_submission_cutoff`;
- `hris.work_calendar`;
- `hris.approval_workflow`;
- `hris.overtime_rounding` dan `hris.overtime_maximum`.

## Di luar fase ini

Perhitungan hari kerja berdasarkan kalender libur dan upload evidence langsung
ke object storage masih memerlukan konfigurasi operasional tambahan. Core
workflow lembur, reimbursement, cash advance, approval event, notifikasi
in-app, dan Web Push adapter sudah tersedia; delivery memerlukan VAPID key dan
worker queue aktif.
