# Log pekerjaan — Konflik port Workspace dan File Organizer legacy

## Perubahan

- Mengidentifikasi bahwa trace `fo_documents` berasal dari File Organizer
  legacy yang berjalan pada port `8080`, bukan dari `app/workspace`.
- Mengubah script Composer File Organizer legacy ke port `8001` sesuai mapping
  legacy agar tidak mengambil port Workspace `8080`.

## Verifikasi

- Dashboard Workspace tidak menjalankan query statistik `fo_documents`.
- Dashboard File Organizer legacy memang menjalankan query tersebut.
- Terdapat dua proses yang listen pada port `8080`; proses lama harus
  dihentikan sebelum Workspace dijalankan.

## Risiko tersisa

- Proses PHP yang sudah berjalan tidak berubah otomatis; perlu dihentikan dan
  dijalankan ulang dari folder aplikasi yang benar.
