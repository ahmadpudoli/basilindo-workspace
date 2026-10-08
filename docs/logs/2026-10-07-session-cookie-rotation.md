# Rotasi otomatis cookie session

## Masalah

Cookie browser terikat pada host dan nama cookie. Perpindahan antara `127.0.0.1` dan `localhost`, perubahan konfigurasi, atau session lama yang tidak valid dapat menyebabkan CSRF/session mismatch dan login terlihat berulang.

## Perubahan

- SSO memakai `basilindo_sso_session_v2`.
- File Organizer memakai `basilindo_file_organizer_session_v2`.
- Project Management memakai `basilindo_project_management_session_v2`.

Aplikasi otomatis mengabaikan cookie dengan nama lama dan membuat session baru. User tidak perlu menghapus cookie secara manual untuk rotasi konfigurasi ini.

## Batasan browser

Cookie pada `127.0.0.1` tidak dapat dihapus oleh response dari `localhost`, dan sebaliknya. Karena itu seluruh URL aplikasi diarahkan konsisten ke `localhost`.
