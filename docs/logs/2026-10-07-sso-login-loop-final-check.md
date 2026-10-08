# Verifikasi loop login SSO

## Perubahan

- Menyamakan default `APP_URL`, issuer OIDC, dan logout redirect SSO ke host `localhost`.
- Membersihkan cache konfigurasi, route, view, dan Filament pada project SSO.
- Menghentikan worker PHP lama pada port 8001 dan menjalankan ulang worker dari workspace aktif dengan satu worker.

## Verifikasi

- `admin@example.com` dengan password development yang dikonfigurasi berhasil diautentikasi melalui Livewire.
- Request lanjutan ke `/admin` dengan cookie sesi yang sama menghasilkan HTTP 200 dan halaman Dasbor.
- `OidcDiscoveryTest`: 3 test lulus, 21 assertion.
- Endpoint `/admin/login` menghasilkan cookie `basilindo_sso_session_v2` pada host `localhost`.

## Catatan

- Perbedaan host `localhost` dan `127.0.0.1` tetap menghasilkan cookie berbeda di browser. Seluruh alur lokal sekarang menggunakan `localhost`.
- Warning permission pada cache result Pest tidak memengaruhi hasil test, tetapi perlu dirapikan pada setup Windows bila test dijalankan oleh akun berbeda.
