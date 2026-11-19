# HRIS claim history panel width

- Mengurangi kolom panel riwayat pada detail reimbursement dari sepertiga menjadi seperempat lebar di desktop.
- Memperluas area konteks dan rincian menjadi tiga perempat lebar; breakpoint Bootstrap tetap membuat kedua area menumpuk selebar layar pada perangkat kecil.
- Verifikasi: `php artisan view:cache` dan `git diff --check`.
