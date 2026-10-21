# Panduan validasi form CRUD

## Tujuan

Setiap form tambah dan edit pada modul Workspace harus memberikan umpan balik validasi sebelum request dikirim dan tetap memvalidasi ulang data di server.

## Aturan implementasi

- Gunakan `data-client-validation="<resource>"` pada form dan pasang `novalidate` agar alur validasi client terpusat di `resources/js/app.js`, bukan bergantung pada validasi native browser atau atribut HTML saja.
- Tambahkan cabang validasi resource pada `validateWorkspaceForm()`. Validasi minimal harus mencakup field wajib, panjang maksimum, pilihan dropdown, format khusus, duplikasi item, dan aturan relasi yang dapat diperiksa dari data yang sudah tersedia di halaman.
- Tampilkan pesan error di bawah elemen atau container input yang bermasalah, tambahkan class `is-invalid`, fokuskan field pertama yang gagal, dan hentikan submit ketika ditemukan error.
- Untuk field dinamis, validasi baris atau komponen yang sedang tampil langsung dari DOM. Jangan hanya memeriksa hidden JSON atau menganggap atribut `required` sudah cukup.
- Pertahankan Form Request atau `$request->validate()` sebagai sumber validasi server. Validasi client hanya membantu pengalaman pengguna dan tidak boleh menggantikan authorization, scope, atau pemeriksaan database.
- Saat membuat CRUD baru, tambahkan test server-side untuk happy path dan failure path serta test view yang memastikan hook `data-client-validation` terpasang.

## Acceptance criteria

- Form create dan edit memiliki validasi client yang aktif sebelum request dikirim.
- Pesan validasi muncul di bawah input yang sesuai dan tidak menabrak field lain.
- Submit yang valid tetap diteruskan ke endpoint server.
- Request yang melewati client tetap ditolak bila melanggar validasi server atau authorization.
- Pola validasi terdokumentasi dan dapat digunakan ulang oleh CRUD berikutnya.
