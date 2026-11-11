# Pemilih tabel referensi pekerjaan lembur

## Perubahan

- Mempertahankan dropdown Project dan Opportunity sebagai single-select; isi menu dropdown berupa tabel searchable.
- Menambahkan kolom nama, status, client, dan kontak untuk Opportunity.
- Menambahkan ringkasan referensi setelah baris dipilih.
- Project memakai client utama dari `project_parties`; project tanpa client menampilkan tanda `—`.

## Perbaikan lanjutan

- Memperbaiki konflik dropdown Bootstrap yang menyebabkan panel tabel dan Opportunity tidak tampil konsisten. Picker sekarang mengelola buka/tutup sendiri, menjaga tombol dan pencarian satu baris, serta memaksa state visibility saat jenis referensi berubah.
- Test render Opportunity ditambahkan untuk memastikan label, pencarian, nama opportunity, dan client hadir pada form.

## Verifikasi

- Coverage form HRIS diperbarui untuk memeriksa tabel, search, status, dan client.
- Perbaikan lanjutan memakai grid picker khusus dan mencegah wrapping Bootstrap pada input pencarian.
- Test render dan persistensi Opportunity lulus: 2 test, 22 assertions.
- Verifikasi langsung melalui Chrome DevTools Protocol: setelah memilih `External / Project` lalu `Opportunity`, panel Opportunity tampil (`display: block`), panel Project tersembunyi, grid picker memiliki dua kolom, dan pencarian tidak wrapping (`nowrap`).
- Verifikasi DOM menemukan markup legacy menempatkan kolom search (`.col-md-7`) sebagai child `.col-md-5`. JavaScript form kini menormalisasi struktur tersebut saat load agar kolom picker menjadi sibling dan bantuan tampil di bawah picker. Posisi aktual Chrome setelah perbaikan sudah sejajar.
- Search kini dipindahkan ke dalam menu dropdown; baris tabel menjadi single-select; tabel Project menampilkan Project/Client/Kontak, sedangkan Opportunity menampilkan Opportunity/Perusahaan pemilik/Client/Status/Kontak.
- Verifikasi langsung Chrome: search berada di dalam menu, 62 baris Opportunity ter-render, dan urutan header sesuai requirement.
- Memindahkan menu dropdown ke `document.body` dengan posisi fixed saat dibuka agar tidak terpotong oleh overflow card/section form; tinggi menu mengikuti ruang viewport dan tetap scrollable.
- Memperbaiki nesting legacy lain: field `Lokasi kerja` sebelumnya berada di dalam `#overtime-project-field`; kini dipindahkan menjadi sibling setelah referensi pekerjaan dengan jarak vertikal yang konsisten.
- Memperbaiki clipping terakhir: `min-width` bawaan menu lama dinonaktifkan saat menu dipindah ke `body`, lebar kini dibatasi viewport, dan kolom Project yang ditampilkan menjadi Project/Client/Kontak.
- Memperbaiki nesting `Opportunity` yang sebelumnya berada di dalam `Project`; Opportunity kini sibling langsung di blok Referensi Pekerjaan sehingga radio Opportunity benar-benar menampilkan picker dan detailnya.
- Menambahkan jarak 12px antara tombol picker dan panel detail referensi agar detail tidak menempel pada dropdown.
- Test terarah: `php artisan test tests/Feature/HrisWorkflowTest.php --filter='supports an opportunity'` lulus.
