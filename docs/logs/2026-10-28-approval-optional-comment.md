# Catatan opsional pada approval HRIS

## Perubahan

- Popup konfirmasi `Setujui` sekarang menyediakan catatan persetujuan opsional.
- Catatan dikirim sebagai `comment` dan disimpan pada approval event bila diisi.
- Aturan alasan wajib untuk `Tolak` dan `Minta revisi` tetap dipertahankan.
- Lebar popup konfirmasi diperbesar agar pertanyaan, catatan, dan tombol lebih nyaman dibaca.
- Mempertahankan nilai `action=approve` dari tombol submit saat form diteruskan melalui popup konfirmasi.
- Menampilkan pesan validasi server yang spesifik pada halaman approval agar kegagalan minta revisi dapat ditindaklanjuti.
- Mengikat field popup revisi secara eksplisit ke form agar `comment` tetap terkirim saat modal dirender di dalam tabel.
