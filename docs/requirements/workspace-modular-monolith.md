# Requirement: Basilindo Workspace Modular Monolith

## Tujuan

Mengembangkan `app/file-organizer` menjadi satu Basilindo Workspace yang memuat lima modul bisnis dengan UI/UX File Organizer sebagai baseline visual dan interaksi.

## Modul dan tanggung jawab

### 1. Core / Identity

- Login lokal Laravel/Filament sebagai metode utama; adapter OIDC/SSO bersifat opsional.
- User, role, permission, session, audit login, dan application entitlement.
- Tidak membuat identitas user duplikat di modul lain.

### 2. Company

- Master group, perusahaan, anak perusahaan, vendor, partner, dan klasifikasi entitas.
- Membership user terhadap perusahaan dan scope role.
- Menjadi referensi bersama untuk CRM, Project Management, dan Document Management.

### 3. CRM

- Account/customer, contact, lead, opportunity, activity, pipeline, dan account team.
- Contact eksternal bukan user login workspace.
- Account yang berasal dari master Company memakai reference yang sama, bukan salinan perusahaan.

### 4. Project Management

- Project, member, task, ticket, milestone, timeline, status, dan project team.
- Project dapat dibuat dari opportunity CRM.
- Scope project mengikuti company dan membership yang telah diotorisasi.

### 5. Document Management

- Document, document type, metadata, upload private MinIO, versioning, verification, search, bundling, dan audit.
- Dokumen dapat dikaitkan dengan company, project, account, opportunity, vendor, dan reference bisnis.

## Aturan lintas modul

- Semua modul berjalan pada satu aplikasi dan satu lifecycle deployment.
- Boundary modul tetap dipisahkan melalui domain service, policy, event, dan permission.
- User dan Company tidak dibuat ulang oleh modul bisnis.
- Transaksi lintas modul wajib memakai service orchestration dan database transaction jika perubahan harus atomik.
- Integrasi yang tidak perlu atomik menggunakan event/job yang idempotent.
- UI mengikuti layout, warna, tabel, filter, status, empty state, dan pola navigasi File Organizer.
- `app/project-sso/` tidak menjadi dependency runtime Workspace dan dapat dipertahankan sebagai adapter/future federation.

## Acceptance criteria fase fondasi

- `app/file-organizer` ditetapkan sebagai Basilindo Workspace.
- Struktur lima boundary domain terdokumentasi dan tersedia di source tree.
- Menu global identity/company tidak diduplikasi di setiap modul bisnis.
- Existing Document Management tetap dapat dijalankan tanpa perubahan perilaku yang tidak terkait.
- Setiap modul memiliki daftar fitur awal dan owner data yang jelas.
- Role module CRM/Project diprovision secara additive pada existing install; migration tidak mencabut permission atau membuat company membership otomatis.

