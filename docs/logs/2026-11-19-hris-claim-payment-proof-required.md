# Bukti pembayaran wajib

## Perubahan

- Upload bukti pembayaran menjadi wajib di popup, validasi client, dan validasi server.
- Batas maksimal 5 MB per file dan format JPEG, PNG, WebP, atau PDF tetap diterapkan.
- Field ID bukti manual tetap opsional sebagai referensi tambahan; tidak menggantikan lampiran file.

## Verifikasi

- Meninjau atribut required, validasi client saat file kosong, dan rule server `required`.
- `git diff --check` bersih. Test suite tidak dijalankan.
