# Standard pagination Workspace

Pagination Workspace menggunakan Bootstrap 5 sebagai fondasi UI, dengan warna dan spacing Basilindo Workspace. Setiap daftar server-side wajib:

- menggunakan `paginate()` dan query yang sama untuk search, sorting, dan pagination;
- mempertahankan filter dan urutan pada link halaman;
- memakai interaksi AJAX/partial jika halaman sudah menyediakan pola interaktif, sehingga tidak perlu reload penuh;
- menampilkan ringkasan rentang data dan jumlah total;
- menyediakan filter dan pilihan jumlah record per halaman jika resource membutuhkannya;
- menggunakan baris tabel compact dengan padding vertikal yang tidak berlebihan;
- menyediakan state loading, empty state, dan error state yang layak.

`pos-laravel-admin` menjadi referensi pola pagination Bootstrap, sedangkan warna aktif, border, radius, dan ukuran diselaraskan dengan token visual Workspace.
