# Standard pagination Workspace

Pagination Workspace menggunakan Bootstrap 5 sebagai fondasi UI, dengan warna dan spacing Basilindo Workspace. Setiap daftar server-side wajib:

- menggunakan pilihan jumlah baris standar `10`, `25`, `50`, dan `100`; default standar adalah `10` kecuali requirement fitur menetapkan default lain secara eksplisit;

- menggunakan `paginate()` dan query yang sama untuk search, sorting, dan pagination;
- mempertahankan filter dan urutan pada link halaman;
- menerima pencarian, filter, sorting kolom, dan jumlah baris melalui parameter GET yang divalidasi dan di-whitelist di server;
- menggunakan multi-select searchable untuk filter relasi ketika satu record dapat terkait dengan beberapa pilihan, lalu memproses semua nilai terpilih pada query server-side;
- mengatur sorting dan filter pada query sebelum `paginate()`, lalu meneruskan seluruh parameter dengan `withQueryString()`;
- memakai interaksi AJAX/partial jika halaman sudah menyediakan pola interaktif, sehingga tidak perlu reload penuh;
- menampilkan ringkasan rentang data dan jumlah total;
- menyediakan filter dan pilihan jumlah record per halaman jika resource membutuhkannya;
- menggunakan tinggi kontrol filter yang seragam, serta menempatkan tombol `Reset` dan `Terapkan` dalam satu grup aksi yang menyatu dengan radius luar pada sisi kiri dan kanan;
- menyusun lebar field filter dalam grid proporsional sesuai prioritas konten, menjaga grup aksi tetap rata kanan, dan memakai background primary pada tombol `Terapkan`;
- menyelaraskan posisi floating label multiselect dengan kontrolnya ketika wrapper multiselect memiliki ruang vertikal tambahan;
- mempertahankan offset floating label multiselect sebagai aturan akhir komponen agar tetap sejajar setelah wrapper filter diberi padding;
- setiap field pada panel filter wajib memakai struktur global `.workspace-floating-field`, dengan control sebelum label dan class input/select/multiselect yang sama seperti form create/edit;
- struktur panel filter daftar wajib mengikuti pola standar `form.workspace-list-filter-form` lalu `.row.g-2.align-items-end`, setiap field berada di dalam wrapper `col-*`, dan kontrol dibungkus `.workspace-floating-field`; layout khusus resource hanya boleh mengatur proporsi kolom melalui class barisnya;
- dropdown multiselect di dalam `.workspace-floating-field` wajib mengikuti tinggi dan padding kontrol input/select global agar tidak membuat field lebih tinggi;
- wrapper `.workspace-dropdown-multiselect` juga wajib memakai tinggi dan padding vertikal yang sama, bukan hanya tombol trigger di dalamnya;
- menggunakan baris tabel compact dengan padding vertikal yang tidak berlebihan;
- menggunakan tombol aksi ukuran `btn-sm` dan tabel `table-sm` untuk daftar data yang padat;
- menyediakan state loading, empty state, dan error state yang layak.

`pos-laravel-admin` menjadi referensi pola pagination Bootstrap, sedangkan warna aktif, border, radius, dan ukuran diselaraskan dengan token visual Workspace.
