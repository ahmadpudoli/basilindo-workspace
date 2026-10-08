# Log pekerjaan — SSO invalid_grant

## Perubahan

- Menyimpan transaksi PKCE berdasarkan `state`, bukan hanya satu verifier global dalam session.
- Menambahkan fallback untuk callback yang dimulai sebelum perubahan ini.
- Menangani `RequestException` dari token endpoint dan mengarahkan user ke login SSO baru.
- Menambahkan log aman yang tidak memuat code, verifier, token, atau secret.

## Verifikasi

- Syntax check controller dilakukan setelah perubahan.
- Jalur `invalid_grant` didokumentasikan sebagai transaksi code expired/terpakai atau login tab yang saling menimpa.

## Risiko tersisa

- Jika `SSO_REDIRECT_URI`, client secret, atau konfigurasi client di provider tidak sama, provider tetap akan menolak token exchange dan perlu diperbaiki di environment.
