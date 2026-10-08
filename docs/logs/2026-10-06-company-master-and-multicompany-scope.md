# Penyelarasan master perusahaan dan multi-company

## Perubahan

- Menetapkan `companies` sebagai master tunggal untuk Group Basilindo, perusahaan internal, client, vendor, dan partner.
- Memperjelas label dan kolom `Klasifikasi` serta `Peran bisnis` pada menu Master Perusahaan.
- Menambahkan filter klasifikasi dan peran bisnis pada master perusahaan.
- Menyembunyikan menu Vendor legacy dari navigasi tanpa menghapus tabel/data lama.
- Memperjelas relation manager assignment banyak perusahaan pada user dan hak akses per company.
- Memperbaiki modal assignment agar field perusahaan benar-benar tampil; sebelumnya custom form hanya menampilkan pilihan role tanpa pilihan perusahaan.
- Mengganti label aksi lanjutan menjadi `Tambahkan & Tambahkan Lainnya` dan menambahkan panduan tiga scope role di modal.
- Memperbaiki format panduan role dari teks multiline menjadi HTML bullet list agar tampil terstruktur di Filament Placeholder.
- Membatasi pilihan assignment user hanya ke perusahaan internal (`classification=internal`, `entity_type=company`); Group Basilindo dan pihak eksternal tidak dapat dipilih.
- Memaksa tabel perusahaan user melakukan reset/reload setelah AttachAction berhasil, termasuk aksi tambah berikutnya.
- Menghapus relasi `companies` yang mungkin tercache pada owner user sebelum reset agar hasil terbaru langsung diambil dari database.
- Mengubah form tambah perusahaan dari modal penuh menjadi slide-over agar tabel dan hasil penambahan tetap terlihat saat menambahkan beberapa perusahaan.
- Memperkuat refresh setelah aksi `attachAnother` dengan me-refresh owner user dari database sebelum reset tabel.
- Menambahkan event Livewire self-refresh agar jalur `Tambahkan & Tambahkan Lainnya` memicu render tabel kedua tanpa menutup slide-over.
- Menambahkan polling 500ms pada tabel assignment sebagai fallback untuk lifecycle `attachAnother` yang menahan render saat slide-over tetap terbuka.
- Menghapus polling 500ms yang membebani halaman dan menggantinya dengan refresh event-driven setelah attach, detach, dan bulk detach.
- Menargetkan event refresh secara eksplisit ke `CompaniesRelationManager`, bukan `self()`, agar tabel relation manager menerima render ulang saat action nested tetap terbuka.
- Menghubungkan event ke `$wire.$refresh()` pada elemen tabel melalui Alpine/Livewire, sehingga render ulang tetap terjadi saat `AttachAction` menahan slide-over.
- Mengembalikan alur tombol `Tambahkan` agar tetap menutup slide-over; hanya `Tambahkan & Tambahkan Lainnya` yang mempertahankan slide-over terbuka.
- Mengubah event refresh menjadi event browser global agar Alpine pada elemen tabel tetap memicu `$wire.$refresh()` saat slide-over attach masih mounted.
- Mengubah tombol `Tambahkan` agar ikut mempertahankan slide-over terbuka setelah berhasil, sesuai alur input multi-perusahaan.
- Menghapus tombol `Tambahkan & Tambahkan Lainnya`, menjadikan `Tambahkan` sebagai satu-satunya submit yang mempertahankan slide-over, dan mengganti `Batal` menjadi `Tutup`.
- Mengganti Filament `AttachAction` dengan custom `Action` yang menyimpan langsung ke relasi `company_user`; action ditahan setelah response refresh agar slide-over tetap terbuka tanpa pola close/reopen.
- Menambahkan kembali tombol `Tambahkan & Lainnya`; tombol normal menutup panel setelah save, sedangkan tombol ini menampilkan overlay loading, menyimpan, me-refresh tabel, lalu membuka kembali panel setelah response selesai.
- Menghapus overlay JavaScript custom yang salah ter-render sebagai teks; proses simpan kembali memakai indikator loading bawaan action Filament.
- Menghapus alur `attachAnother`, event browser, dan overlay custom yang memaksa reopen/refresh DOM saat Livewire morph; assignment kembali memakai satu lifecycle action Filament agar scope Alpine tabel tetap utuh.
- Menambahkan tooltip native Filament pada Role aplikasi, perusahaan, Peran user di perusahaan, serta kolom peran agar perbedaan akses global dan scope perusahaan dapat dipahami tanpa memenuhi layout.
- Membatasi `super_admin` hanya pada akun sistem melalui filter UI, role field read-only pada akun utama, tombol hapus tersembunyi, dan trigger PostgreSQL yang menolak assignment kedua maupun pelepasan role dari akun terlindungi.
- Memperbaiki callback dehydration field Role aplikasi agar membaca record langsung dari komponen Select; penyimpanan profil akun sistem tidak lagi mengirim sinkronisasi role kosong yang memicu penghapusan `super_admin`.
- Mengubah panduan role menjadi kartu informasi responsif dengan ringkasan dan tooltip per role agar lebih mudah dipindai di modal.
- Memperbarui domain model dan checklist.

## Verifikasi

- Memeriksa bahwa `company_user` dan relasi `User::companies()` tetap menjadi sumber scope akses.
- Memeriksa bahwa document query/policy sudah membatasi data berdasarkan company yang dimiliki user.
- Pemeriksaan test penuh masih memiliki kegagalan setup database duplicate constraint/table pada koneksi `core`, yang tidak disebabkan perubahan UI ini.
- Verifikasi tambahan: source tidak lagi mendaftarkan event `reopen-company-attach` atau komponen overlay assignment.

## Risiko tersisa

- `vendor_id` dan tabel/model Vendor lama masih dipertahankan untuk kompatibilitas. Migrasi data dokumen ke `document_parties` dengan role `vendor` perlu dijadwalkan sebagai pekerjaan terpisah sebelum legacy schema dihapus.
- `scope_role` sudah dicatat per user-perusahaan, tetapi policy granular untuk membedakan aksi ketiga role masih perlu ditambahkan.
