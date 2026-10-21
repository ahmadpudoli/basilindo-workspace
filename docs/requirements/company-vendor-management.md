# Company vendor management

## Aturan

- Modul Vendor mengelola profil vendor yang digunakan oleh perusahaan operasional, bukan menggandakan master identitas Company.
- Satu profil vendor mengacu ke satu master `Company` aktif dengan peran bisnis `vendor` melalui `vendor_company_id`.
- Satu profil vendor dapat digunakan oleh satu atau beberapa perusahaan operasional melalui pivot `vendor_operational_companies`.
- Pilihan perusahaan operasional hanya berisi company internal aktif dengan peran `operating_company` yang berada dalam scope user.
- Data vendor mencakup nama profil, kode, tax identifier, status, master perusahaan vendor, dan relasi perusahaan operasional.
- Vendor memiliki halaman daftar, detail, tambah, ubah, dan arsip soft delete.
- Daftar vendor memakai search, filter perusahaan/status, pagination, dan limit record yang konsisten dengan Company.
- Aksi arsip wajib melewati confirmation dialog dan tidak menghapus record permanen.
- Form tambah dan edit Vendor wajib memiliki validasi client-side terpusat untuk feedback cepat serta validasi server-side melalui Form Request; validasi tidak bergantung pada atribut HTML `required`.

## Batas domain

Master `Company` dapat diberi peran bisnis `vendor` untuk menandai badan usaha vendor, misalnya PT ABC. Profil pada modul Vendor menyimpan konteks penggunaan dan dapat menghubungkan PT ABC ke Carano sekaligus LTN. Dengan demikian, vendor yang sama tidak perlu dibuat ulang untuk setiap perusahaan operasional.

Data lama yang masih menggunakan `vendors.company_id` tetap dibaca sebagai fallback kompatibilitas. Migration normalisasi menyalin relasi lama ke pivot. Karena kolom legacy tersebut masih wajib pada database saat ini, data baru menyimpan perusahaan operasional pertama sebagai anchor `company_id`, sementara seluruh pilihan perusahaan disimpan pada pivot.
