# Keputusan PWA Workspace

Status: diterima.

Manifest dan registrasi service worker hanya ditawarkan pada halaman authenticated. Service worker menggunakan network-only untuk request GET dan tidak menyimpan halaman/data privat agar setiap pembukaan tetap melewati session, policy, dan scope Workspace. PWA awal tidak menyediakan mode offline. Safari iOS memakai petunjuk Add to Home Screen karena tidak menyediakan event prompt instalasi standar.
