# Workflow revisi pengajuan cuti/sakit

## Perubahan

- Menambahkan status `needs_revision` / Revisi pada pengajuan cuti dan sakit.
- Atasan dan HR dapat meminta revisi dengan alasan serta klasifikasi
  `substantive` atau `administrative`.
- Pemohon dapat memperbaiki detail tanggal, alasan, dan dokumen lalu mengirim
  ulang. Tanggal tetap disimpan pada tabel detail multiple-row.
- Revisi substansi kembali ke atasan; revisi administratif kembali ke pihak
  yang meminta revisi. Permintaan dan pengiriman ulang dicatat di approval event.

## Risiko dan verifikasi

- Migration bersifat incremental dan menambah kolom nullable/default tanpa
  menghapus data lama.
- Alur claim/lembur tetap memakai keputusan approval lama; aksi revisi dibatasi
  pada workflow cuti/sakit.
