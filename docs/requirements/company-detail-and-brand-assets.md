# Company detail dan brand asset

## Aturan

- Halaman detail perusahaan menampilkan identitas, kode, logo, status, jenis entitas, klasifikasi, peran bisnis, parent, anak perusahaan, tanggal perubahan, dan visibilitas data.
- Logo perusahaan bersifat opsional dan hanya boleh berupa JPG, PNG, atau WEBP maksimal 2 MB.
- Binary logo disimpan pada private MinIO/S3 disk yang dikonfigurasi melalui environment aplikasi.
- Object key logo wajib menggunakan root modul `app-company`, lalu storage key perusahaan dan folder brand: `app-company/companies/{company_storage_key}/brand/{random_id}.{extension}`.
- Logo hanya dapat dibaca melalui route yang melewati Company policy; URL object storage tidak pernah ditampilkan langsung.
- Upload logo baru menggantikan referensi logo lama dan object lama dihapus setelah referensi baru berhasil disimpan.
- Input logo berada di bagian paling atas form, rata kanan, berbentuk panel kompak, dan selalu diberi label opsional.
- Form menampilkan preview logo tersimpan atau inisial kode, lalu menggantinya secara langsung ketika user memilih file baru.
- Daftar perusahaan menampilkan logo di sebelah nama melalui endpoint privat dengan `loading="lazy"`, `decoding="async"`, ukuran tetap, dan prioritas rendah agar gambar tidak menghambat render tabel.

## Kriteria penerimaan

- Detail perusahaan tanpa logo menampilkan empty state atau inisial kode.
- Detail perusahaan dengan logo menampilkan logo dari endpoint privat terotorisasi.
- User yang tidak dapat melihat perusahaan tidak dapat mengakses logo melalui endpoint.
- Form create/update menerima upload multipart dan menampilkan pesan validasi yang jelas.
