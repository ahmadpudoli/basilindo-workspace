# Penataan form cuti/sakit

Tanggal: 2026-11-18

## Perubahan

- Memindahkan jenis pengajuan, dokumen pendukung, dan informasi karyawan/alur persetujuan ke kolom kiri setelah nomor pengajuan.
- Memindahkan panel kuota/tanggal dan field alasan ke kolom kanan.
- Mengubah informasi karyawan, atasan, dan approver HR menjadi accordion default collapsed, dengan warna netral biru muda dan kontras teks yang jelas.
- Merapatkan kartu jenis Cuti/Sakit, menghilangkan garis di bawah nomor pengajuan dan garis di sekitar bagian jenis/dokumen.
- Menyamakan tinggi nomor urut dan tombol hapus dengan input tanggal pada setiap baris.
- Menyetarakan tinggi input tanggal, badge nomor, dan tombol hapus menjadi 32 px.
- Menjaga kontrol tanggal tetap rata di bagian atas ketika pesan validasi muncul di bawahnya.
- Menghapus panel draft pada halaman daftar Cuti & Sakit dan mengarahkan penyimpanan draft baru/perubahan draft ke halaman edit draft.
- Memperbaiki 404 setelah edit draft: layanan sebelumnya menghapus record lalu membuat record baru; kini perubahan tanggal dan data disimpan pada record draft yang sama.
- Menghilangkan garis horizontal pada panel detail antara daftar tanggal dan alasan pengajuan.
- Menebalkan label Alasan pengajuan pada detail cuti/sakit.
- Mengubah panel cuti bersama menjadi collapsible default collapsed, dengan jumlah hari tetap terlihat pada header.
- Perubahan berlaku untuk create, edit draft, dan revisi karena memakai resource Blade yang sama.

## Verifikasi

- Verifikasi Blade cache dan `git diff --check` dijalankan setelah perubahan.

## Risiko tersisa

- Reorder elemen form memakai JavaScript DOM setelah halaman dimuat, konsisten dengan pengaturan layout accordion pada form lembur.
