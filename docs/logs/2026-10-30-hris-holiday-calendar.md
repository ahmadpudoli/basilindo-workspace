# Kalender Hari Libur HRIS Multi-company

## Perubahan

- Menambahkan master `hris.holidays` dan pivot `hris.holiday_companies` untuk hari libur global atau assignment beberapa perusahaan.
- Menambahkan CRUD Hari Libur di bawah Manajemen Kehadiran dengan filter tahun, jenis, status, pagination, scope perusahaan, dan permission granular.
- Cuti bersama wajib menandai pengurangan kuota dan mengurangi kuota efektif tahunan.
- Form Cuti & Sakit menampilkan daftar cuti bersama tahun aktif dan penanda tanggal libur.
- Service pengajuan memvalidasi tanggal libur di server untuk create dan resubmit sehingga tidak dapat dilewati melalui manipulasi browser.
- Audit CRUD hari libur dicatat melalui AuditLogger.
- Menambahkan migration incremental untuk membersihkan cache Spatie Permission setelah provisioning permission baru.
- Memperbaiki pembacaan cakupan perusahaan: pivot `holiday_companies` dibaca melalui koneksi HRIS, sedangkan master perusahaan dibaca melalui koneksi Core, sehingga aplikasi tidak lagi mencari tabel yang tidak ada bernama `hris_companies`.
- Form cakupan perusahaan diubah menjadi multiple select yang lebih ringkas. Untuk super admin, pilihan kosong diproses sebagai cakupan global; pengguna non-super admin tetap wajib memilih perusahaan agar boundary akses tidak melebar.
- Kontrol cakupan kemudian disajikan sebagai dropdown multi-select compact dengan pencarian nama perusahaan agar form tidak memanjang dan lebih mudah dipindai.
- Dropdown diselaraskan dengan komponen searchable multi-select standar Workspace agar menu tidak terpotong oleh card dan perilakunya konsisten dengan form lain.
- KPI Cuti & Sakit pada daftar pengajuan ditambah indikator cuti bersama dan popup rincian per KPI, termasuk formula kuota efektif, cuti terpakai, sisa cuti, dan sakit disetujui.
- Popup kuota cuti menampilkan tanggal cuti bersama pengurang kuota sebagai badge tanggal dan nama hari libur.
- Popup cuti terpakai menampilkan tanggal cuti yang disetujui sebagai badge serta keperluan dari setiap pengajuan.
- Popup sakit tercatat juga menampilkan tanggal sakit yang disetujui sebagai badge serta alasan setiap pengajuan.
- Menambahkan parameter global `hris.active_year` pada Pengaturan HRIS. Filter Cuti & Sakit menggunakan parameter ini sebagai default, dan semua KPI mengikuti tahun filter yang dipilih.
- Menambahkan filter Bulan pada daftar Cuti & Sakit. Jika dipilih, data aktivitas dan KPI mengikuti Bulan + Tahun; jika kosong, seluruh bulan pada Tahun digunakan.
- KPI pada tampilan scope perusahaan diperbaiki agar menghitung karyawan dalam scope yang sama dengan tabel, bukan hanya karyawan yang terhubung ke user login.
- Pada perangkat mobile, panel KPI dan filter daftar Cuti & Sakit dibuat collapsible dengan kondisi awal tertutup; desktop tetap menampilkan panel secara normal.
- Spacing mobile daftar Cuti & Sakit dirapikan: padding halaman, jarak antar panel, padding tombol, dan padding sel tabel diseragamkan. Tabel diberi area scroll horizontal agar kolom aksi tidak memaksa layout terpotong.
- Finalisasi layout responsif: KPI memakai 5 kolom pada layar lebar dan 2 kolom pada mobile, tinggi kartu KPI dipadatkan, jarak KPI-filter dirapatkan, serta batas panel filter dan tabel dipertegas.
- Menyamakan lebar panel KPI, filter, dan tabel pada mobile serta menormalkan margin horizontal agar seluruh blok sejajar dengan konten halaman.
- Memperbaiki CSS malformed yang tersimpan sebagai teks literal `\\n+` dan melakukan rebuild Vite; asset produksi kini memuat aturan responsive terbaru.
- Memastikan panel KPI tetap memakai CSS Grid saat dibuka di mobile sehingga lima KPI tampil dalam dua kolom, bukan satu kolom vertikal.
- Memadatkan kembali tinggi baris KPI mobile dengan row sizing berbasis konten, padding lebih kecil, dan menghilangkan helper hover yang tidak diperlukan pada layar sentuh.
- Menyamakan lebar panel Filter dengan panel Ringkasan KPI dan menetapkan jarak vertikal eksplisit agar Filter berada tepat di bawah KPI.
- Menambahkan aturan final mobile dengan `width: 100%`, margin horizontal nol, dan satu jarak 12px antar section KPI dan Filter untuk mencegah perbedaan posisi akibat aturan margin sebelumnya.
- Memperbaiki override `display` pada container KPI agar layar desktop/lebar kembali menggunakan grid 5 kolom, sementara mobile tetap menggunakan 2 kolom saat panel dibuka.
- Menetapkan `border-box` dan lebar maksimum pada container halaman agar panel filter serta tabel tidak melebar melewati padding konten; filter juga dibuat wrap pada breakpoint desktop-menengah dan tabel mempertahankan overflow horizontal yang aman.
- Menyamakan panel filter/tabel dengan pola card template desktop: shell section dikunci ke lebar konten, margin bawaan dihapus, dan jarak antar panel ditetapkan eksplisit sebesar 16px.
- Menyesuaikan filter dan tabel agar kembali memakai lebar natural block/card seperti halaman Biodata; aturan `width: 100%` yang menyebabkan full-bleed terhadap container dihapus pada desktop.
- Menemukan dan menghapus satu tag penutup `</div>` berlebih setelah panel KPI. Tag tersebut sebelumnya menutup `.workspace-content` terlalu awal sehingga Filter dan tabel dirender di luar container layout; Blade kemudian diverifikasi melalui `view:cache`.
- Menambahkan jarak bawah 12px pada panel collapsible mobile agar panel Filter tidak menempel langsung ke tabel.

## Verifikasi

- PHP lint berhasil untuk model, policy, controller, service, provider, dan route yang berubah.
- `git diff --check` berhasil.
- PHP lint controller tetap berhasil setelah perubahan UI mobile.
- `php artisan route:list` belum dapat dijalankan karena environment lokal menolak penulisan `storage/logs/laravel.log`; selain itu opsi `--columns` tidak tersedia pada versi Artisan yang aktif.

## Risiko tersisa

- Kebijakan khusus seperti apakah hari sakit boleh dilaporkan pada hari libur belum dibuat sebagai opsi; implementasi saat ini memblokir hari libur untuk cuti dan sakit sesuai requirement.
- Data hari libur nasional perlu dimasukkan melalui halaman master atau migration seed resmi setelah daftar kalender tahunan disetujui.
