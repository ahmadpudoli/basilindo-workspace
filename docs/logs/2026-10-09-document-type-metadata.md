# Metadata tambahan bertipe pada Jenis Dokumen

## Perubahan

- Mengganti editor `KeyValue` menjadi repeater dengan nama field, tipe, dan mandatory.
- Menyimpan konfigurasi metadata sebagai object `type` dan `mandatory`, sambil membaca format legacy.
- Menyesuaikan form dokumen agar string/numeric memakai input teks, date memakai datepicker, dan datetime memakai date-time picker.
- Merapikan form dokumen menjadi section compact dan memperbaiki normalisasi konfigurasi berbentuk daftar agar metadata optional ikut tampil.
- Menyetel seluruh section menjadi full-width, panel upload compact, dan grid internal konsisten untuk mengurangi ruang kosong.
- Memperbaiki schema dinamis Filament dengan keyed Grid dan `fill()` setelah jenis dokumen berubah; panel metadata kini berada di dalam Klasifikasi dan relasi.
- Memperbaiki pencarian komponen nested dengan `withHidden: true` setelah panel Metadata dipindahkan ke dalam section Klasifikasi.
- Audit database menemukan konfigurasi Invoice/Quotation berisi tipe dan mandatory tetapi nama field hilang; kondisi ini menyebabkan tidak ada input yang dapat dirender. Editor kini memakai `field_name` dan menampilkan baris invalid agar dapat diperbaiki, tanpa menebak nama bisnis.
- Memperbaiki error namespace Placeholder dan memindahkan konversi repeater ke hook Create/Edit agar `required_fields` benar-benar disimpan sebagai map nama field ke tipe/mandatory.
- Menetapkan state path panel metadata ke array `metadata`, memakai path absolut untuk membaca jenis dokumen, dan path field relatif agar `getRawState()` tidak lagi membaca integer.
- Memindahkan panel metadata keluar dari child schema Klasifikasi untuk menghindari pewarisan state path scalar saat Livewire melakukan POST update; panel tetap berurutan tepat setelah Klasifikasi.
- Menghapus `formatStateUsing()` dari Repeater metadata karena stack trace reorder menunjukkan formatter tersebut membuat snapshot item terbaca sebagai integer; hydration edit kini ditangani sepenuhnya oleh page hook.
- Menambahkan validasi server-side untuk tipe metadata dan membedakan field mandatory dengan optional.

## Verifikasi

- Menambahkan feature test untuk metadata optional, numeric, date, dan datetime.
- PHP lint untuk file yang berubah lolos. Test feature dijalankan, tetapi gagal pada baseline seeder karena tabel `doc_audit_events` belum tersedia (`SQLSTATE[42P01]`); bukan karena assertion metadata.

## Risiko tersisa

- Data metadata konfigurasi legacy masih diterima dan dianggap mandatory sampai seluruh konfigurasi lama dimigrasikan.
