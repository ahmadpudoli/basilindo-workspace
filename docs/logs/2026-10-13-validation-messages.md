# Informative validation messages — 2026-10-13

## Perubahan

- Menambahkan translation validation Bahasa Indonesia untuk aturan umum seperti required, numeric, date, email, file, max, min, regex, dan unique.
- Menambahkan nama atribut bisnis agar pesan menampilkan `Tanggal dokumen`, `Perusahaan internal`, dan nama field yang mudah dipahami.
- Menambahkan pesan khusus untuk metadata Numeric integer dan Numeric Decimal.
- Menambahkan atribut `novalidate` pada form dokumen agar browser tidak mengambil alih dengan pesan `Please fill out this field`.
- Atribut `novalidate` juga dipasang langsung pada wrapper Form halaman create/edit Filament agar benar-benar diterapkan pada elemen form.
- Pilihan proyek pada form dokumen sekarang bergantung pada perusahaan internal dan relasi tersebut divalidasi kembali di server.
- Pilihan jenis dokumen juga bergantung pada perusahaan internal; jenis global tetap tersedia, sedangkan jenis khusus perusahaan lain ditolak.
- Input metadata Numeric dan Numeric Decimal kini membersihkan karakter yang tidak sesuai saat diketik atau ditempel, dengan validasi server sebagai pengaman akhir.
- Sanitasi input dipasang sebagai `oninput` native pada elemen input agar tetap berjalan walaupun Alpine tidak menginisialisasi ulang field setelah Livewire update.
- Tampilan angka metadata memakai pemisah ribuan titik dan desimal koma; server menormalisasi kembali ke format canonical tanpa kehilangan nilai.
- Field Nominal memakai format yang sama, tetap opsional, dan menolak nilai negatif atau format angka yang tidak valid di server.
- Konfigurasi format angka dipusatkan pada `config/formatting.php`; locale aplikasi `id`, timezone tampilan `Asia/Jakarta`, dan timezone storage `UTC` tersedia melalui `.env`.
- Path object dokumen dipusatkan pada prefix `app-document-management`, lalu dikelompokkan berdasarkan `storage_key` perusahaan dan tanggal dokumen; bucket default diperbaiki menjadi `app-workspace`.

## Verifikasi

- Pesan field tanggal kosong sekarang menjadi `Tanggal dokumen wajib diisi.`.
- Metadata integer menjelaskan bahwa nilai harus berupa angka bulat.
- Metadata decimal memberi contoh format angka desimal yang valid.
