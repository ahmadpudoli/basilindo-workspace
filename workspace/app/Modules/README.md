# Workspace Modules

Direktori ini adalah boundary kode aplikasi. Core adalah platform bersama; Company,
CRM, ProjectManagement, dan DocumentManagement adalah modul bisnis aktif. HRIS
disiapkan sebagai modul bisnis berikutnya.

Struktur standar modul:

```text
ModuleName/
├── Application/    # use case, command/query service, DTO
├── Domain/         # rule domain, contract, value object
├── Http/           # controller, request, resource/transformer
├── Livewire/       # hanya komponen UI yang reaktif
├── Policies/       # policy khusus modul
├── Providers/      # route/view/binding provider bila diperlukan
└── Resources/      # Blade views, translations, assets modul
```

Fitur baru wajib ditempatkan pada modul pemiliknya. Jangan membuat kembali
folder legacy `app/Domain`, `app/Services`, atau `app/Policies`.
