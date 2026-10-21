# ADR-011 — Penghapusan Filament dan boundary UI modular

Status: accepted — 2026-10-16.

Filament dihapus dari runtime Workspace karena proyek diproyeksikan tumbuh besar
dan membutuhkan kontrol penuh atas UI serta biaya migrasi yang dapat diprediksi.
Laravel Blade + Bootstrap menjadi lapisan UI dasar. Livewire boleh digunakan
secara selektif untuk komponen reaktif, tetapi tidak boleh memuat aturan bisnis.

Struktur kode menggunakan `app/Modules/{Core,Company,CRM,ProjectManagement,DocumentManagement,HRIS}`.
Core adalah platform bersama; empat modul bisnis aktif dan HRIS future module
memakai satu aplikasi, satu database PostgreSQL, satu session/guard lokal, satu
Redis, dan satu deployment.

Alternatif yang ditolak: mempertahankan Filament sebagai panel utama karena
coupling resource/plugin dan upgrade major version; membuat aplikasi atau database
terpisah untuk setiap modul karena menggandakan identity, authorization, dan
operational overhead.

Konsekuensi: migration UI dilakukan bertahap. Domain service, policy, job, model,
query scope, storage, dan audit tidak boleh bergantung pada Filament sehingga dapat
digunakan oleh Blade maupun Livewire.
