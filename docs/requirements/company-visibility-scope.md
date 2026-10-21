# Company visibility scope

## Tujuan

Menentukan apakah data perusahaan dapat digunakan secara global oleh seluruh perusahaan operasional atau hanya oleh perusahaan operasional tertentu.

## Aturan bisnis

- Field **Terlihat pada perusahaan operasional** bersifat opsional.
- Jika field tersebut kosong, perusahaan dianggap memiliki visibilitas **global**. Semua perusahaan operasional dapat menggunakan data tersebut, selama user memiliki hak akses ke perusahaan operasionalnya.
- Jika field tersebut diisi, hanya perusahaan operasional yang dipilih yang dapat melihat dan menggunakan data perusahaan tersebut.
- Hak akses user tetap menjadi batas akhir. Visibilitas global tidak memberikan akses kepada user yang tidak memiliki assignment perusahaan.
- `super_admin` dapat melihat dan mengelola seluruh perusahaan sesuai policy administrasi.
- Hanya perusahaan aktif dengan `entity_type=company`, `classification=internal`, dan peran bisnis `operating_company` yang dapat menjadi target visibilitas.
- Perusahaan dengan klasifikasi `external` tidak dapat memiliki peran bisnis `operating_company`.

## Cakupan implementasi

Aturan ini berlaku untuk:

- daftar dan dashboard Company;
- pilihan perusahaan pada form Vendor, CRM, Project Management, Document Type, Document, dan Bundle;
- policy/query master data perusahaan yang dipakai lintas modul.

Data operasional yang dimiliki oleh perusahaan tertentu, seperti dokumen, account CRM, ticket, dan project, tetap memakai company scope user secara langsung. Record tersebut tidak menjadi global hanya karena master perusahaan yang dirujuk memiliki visibilitas global.

## Kriteria penerimaan

- Form tambah dan edit Company memiliki validasi client-side terpusat dan validasi server-side melalui Form Request, tanpa bergantung pada atribut HTML `required`.

- Perusahaan tanpa baris pada tabel `core_company_operational_visibility` tampil untuk semua user yang memiliki minimal satu perusahaan operasional.
- Perusahaan dengan baris visibilitas hanya tampil ketika user memiliki salah satu perusahaan operasional yang dipilih.
- User tanpa assignment perusahaan tidak memperoleh akses melalui aturan global.
- Selector perusahaan pada modul lain tidak menampilkan perusahaan operasional di luar scope user.
- Pengaturan visibilitas dapat dikosongkan kembali untuk mengembalikan perusahaan menjadi global.

Daftar Company juga menyediakan filter multi-pilih berdasarkan peran bisnis: perusahaan operasional, client, vendor, dan partner. Filter ini diproses bersama pencarian, klasifikasi, status, sorting, dan pagination dalam satu query server-side tanpa refresh halaman.
