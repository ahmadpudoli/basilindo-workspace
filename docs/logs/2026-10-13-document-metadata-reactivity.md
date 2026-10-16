# Reactivity metadata form dokumen — 2026-10-13

## Masalah

Panel metadata tambahan belum konsisten diperbarui ketika perusahaan internal atau jenis dokumen berubah. State jenis dokumen lama juga dapat tertinggal ketika perusahaan diganti.

## Perubahan

- `company_id` dibuat reactive dan saat berubah mereset proyek, jenis dokumen, serta metadata lama.
- Pilihan jenis dokumen difilter berdasarkan perusahaan internal yang dipilih, dengan jenis global tetap tersedia.
- `document_type_id` tetap reactive dan mereset metadata saat jenis berubah.
- Panel metadata membaca konfigurasi `required_fields` dari jenis dokumen terpilih dan merender field berdasarkan tipe string, numeric, date, atau datetime.
- Loading panel ditampilkan melalui `wire:loading` ketika Livewire memproses perubahan perusahaan atau jenis dokumen.
- Pembacaan `document_type_id` pada closure `Section` menggunakan path relatif container form `document_type_id`; `statePath('metadata')` hanya digunakan untuk menyimpan nilai field metadata.

## Acceptance criteria

1. Pilih perusahaan internal: jenis dokumen lama dan metadata lama dikosongkan.
2. Pilih jenis dokumen: panel metadata muncul di bawah klasifikasi dan menampilkan field yang dikonfigurasi.
3. Ganti jenis dokumen: field metadata lama hilang dan diganti field jenis baru.
4. Jenis dokumen global tetap muncul untuk semua perusahaan internal.
