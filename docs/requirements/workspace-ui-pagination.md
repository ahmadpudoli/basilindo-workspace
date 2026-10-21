# Standard pagination Workspace

Pagination Workspace menggunakan Bootstrap 5 sebagai fondasi UI, dengan warna dan spacing Basilindo Workspace. Setiap daftar server-side wajib:

- menggunakan `paginate()` dan query yang sama untuk search, sorting, dan pagination;
- mempertahankan filter dan urutan pada link halaman;
- menerima pencarian, filter, sorting kolom, dan jumlah baris melalui parameter GET yang divalidasi dan di-whitelist di server;
- menggunakan multi-select searchable untuk filter relasi ketika satu record dapat terkait dengan beberapa pilihan, lalu memproses semua nilai terpilih pada query server-side;
- mengatur sorting dan filter pada query sebelum `paginate()`, lalu meneruskan seluruh parameter dengan `withQueryString()`;
- memakai interaksi AJAX/partial jika halaman sudah menyediakan pola interaktif, sehingga tidak perlu reload penuh;
- menampilkan ringkasan rentang data dan jumlah total;
- menyediakan filter dan pilihan jumlah record per halaman jika resource membutuhkannya;
- menggunakan baris tabel compact dengan padding vertikal yang tidak berlebihan;
- menggunakan tombol aksi ukuran `btn-sm` dan tabel `table-sm` untuk daftar data yang padat;
- menyediakan state loading, empty state, dan error state yang layak.

`pos-laravel-admin` menjadi referensi pola pagination Bootstrap, sedangkan warna aktif, border, radius, dan ukuran diselaraskan dengan token visual Workspace.
