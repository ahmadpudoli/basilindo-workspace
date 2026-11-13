# Permission granular Pengaturan HRIS dan master penomoran

- Memisahkan permission Parameter HRIS, Workflow Approval HRIS, dan Kategori biaya reimbursement menurut aksi view/create/update/delete. Aksi delete mencakup arsip dan pemulihan; tidak ada penghapusan permanen.
- Master penomoran dipisah menjadi view/create/update. Tidak dibuat permission delete karena fungsi produk tidak menyediakan penghapusan aturan.
- Menambahkan migration incremental yang membuat katalog baru dan memetakan role yang memiliki permission lama ke permission granular terkait agar akses berjalan tetap terjaga.
- Controller memeriksa permission granular pada setiap endpoint dan navigasi menampilkan submenu berdasarkan permission masing-masing.
- Pemulihan tampilan Role: grup Core & Pengaturan kembali memuat dua entri yang diminta, Master penomoran dan Pengaturan HRIS; permission ketiga submenu HRIS dikelompokkan di bawah Pengaturan HRIS.
- Migration yang sebelumnya pending sudah dijalankan incremental: master kategori reimbursement, katalog Core & Pengaturan, dan permission granular.
- Verifikasi: `git diff --check`, PHP lint, serta `php artisan migrate:status` menunjukkan migration terkait sudah berstatus Ran. Suite test belum dijalankan.
- Risiko: UI tindakan per permission perlu dicek melalui browser; role baru memerlukan pemberian `view_*` selain permission CRUD agar dapat membuka halaman daftar.
