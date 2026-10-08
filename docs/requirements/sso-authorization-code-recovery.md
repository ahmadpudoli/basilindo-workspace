# Requirement — Pemulihan authorization code SSO

## Acceptance criteria

- Verifier PKCE disimpan per `state`, sehingga beberapa tab login tidak saling menimpa.
- Callback tetap menerima transaksi lama yang masih memakai session key legacy.
- Authorization code yang expired atau sudah digunakan tidak menampilkan exception mentah kepada pengguna.
- Log hanya mencatat status dan metadata error provider; authorization code, verifier, access token, dan secret tidak dicatat.
- Setelah callback sukses atau gagal, transaksi state terkait dibersihkan.
- Sinkronisasi role dari claim SSO tidak boleh menghapus `super_admin` dari akun sistem yang dilindungi.
- Logout dari panel Filament harus mengakhiri session lokal aplikasi dan kembali ke halaman publik aplikasi.
- Logout aplikasi tidak boleh menghapus session aplikasi lain atau session global Identity Provider.
- Login baru dengan client aplikasi harus meminta autentikasi SSO ulang menggunakan `prompt=login`.
- Role `member` dari application access SSO harus dipetakan ke role `member` File Organizer agar user baru dapat mengakses panel.
