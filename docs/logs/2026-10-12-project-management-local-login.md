# Log pekerjaan — Login lokal Project Management

## Perubahan

- Menghapus override halaman login Project Management yang selalu mengarahkan
  ke route OIDC `/auth/sso`.
- Menyamakan `App\Filament\Pages\Auth\Login` dengan File Organizer sehingga
  Filament merender form login lokal menggunakan guard aplikasi.
- Mengubah cache lokal dari database ke file karena tabel `pm_cache` belum ada
  pada database legacy dan error terjadi saat Livewire rate limiter membaca
  cache setelah login.
- Menyetel `SSO_ENABLED=false` pada environment lokal agar konfigurasi sejalan
  dengan login lokal.
- Memperbaiki relasi assignee Ticket dengan model projection user pada koneksi
  tanpa prefix, sehingga query menggabungkan `core_users` dan
  `pm_ticket_users` tanpa mencari `core_ticket_users`.
- Menetapkan foreign key pivot Ticket secara eksplisit ke `ticket_id` dan
  `user_id`; tanpa ini Laravel menebak `project_management_user_id` dari nama
  model projection.
- Memperbarui README, requirement, dan checklist agar perilaku login legacy
  terdokumentasi.

## Verifikasi

- PHP lint berhasil pada halaman login yang berubah.
- `php artisan route:list --path=admin/login` menampilkan route
  `filament.admin.auth.login` ke halaman login lokal.
- `php artisan migrate:status` mengonfirmasi migration repository database
  legacy belum tersedia; cache file menghindari dependency tersebut pada local.
- PHP lint dan inspeksi relasi memastikan Ticket menggunakan koneksi
  `project_management` untuk pivot `pm_ticket_users`.
- Test panel gagal sebelum assertion karena PHP environment tidak memiliki
  driver SQLite (`could not find driver`); ini kendala environment, bukan
  kegagalan perubahan login.

## Risiko tersisa

- Folder Project Management tetap merupakan sumber legacy; runtime target
  arsitektur terbaru tetap `app/workspace` pada `http://localhost:8080`.
- Route/controller SSO legacy masih ada untuk kompatibilitas, tetapi tidak lagi
  dipanggil oleh halaman login.
