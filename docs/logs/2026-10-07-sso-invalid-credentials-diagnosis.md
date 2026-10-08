# Diagnosis kredensial SSO ditolak

## Temuan

- Record `admin@example.com` ada pada database `db_sso_basilindo`.
- Email sudah terverifikasi.
- Verifikasi hash terhadap password development `12345678` berhasil.
- Tidak ada listener aktif sebelum server dari workspace utama dijalankan.
- Log lama menunjukkan instance SSO dari workspace berbeda (`Legospay-File-Organizer`).

## Tindakan

- Cache konfigurasi SSO dibersihkan.
- Server SSO dijalankan dari `app/project-sso` pada port 8001.
- Server Project Management dijalankan dari `app/project-management` pada port 8002.
- Endpoint login SSO dan landing Project Management merespons HTTP 200.

## Catatan pengguna

- Cookie `127.0.0.1:8001` yang tersimpan dari instance lama perlu dihapus atau login diuji melalui private window.

## Tindak lanjut

- Issuer SSO pada File Organizer dan Project Management diubah menjadi `http://localhost:8001`.
- `APP_URL` Project SSO diubah menjadi `http://localhost:8001`.
- Service dijalankan ulang dari workspace `basilindo-workspace`; port 8001 dan 8002 merespons HTTP 200.

## Root cause final

- Port 8001 masih memiliki worker dari instance lama sehingga request browser tidak konsisten.
- Uji request Livewire pada instance lama menghasilkan HTTP 500 karena compiled view tidak dapat ditulis.
- Setelah listener lama dihentikan dan SSO dijalankan sebagai satu worker dari workspace aktif, uji login `admin@example.com` dan password development berhasil dengan redirect ke `/admin`.
