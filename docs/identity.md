# Identitas Workspace

Workspace menggunakan login lokal Laravel/Filament pada `/login`.
User, password, role, permission, company membership, session, dan audit login
disimpan dan dikelola oleh modul Core dalam aplikasi yang sama.

Tidak ada SSO, OIDC, Google login, atau aplikasi Identity Provider terpisah pada
runtime Workspace. Jika federation eksternal diperlukan di masa depan, hal itu
harus menjadi ADR baru dan adapter yang tidak mengubah ownership authorization
Workspace.
