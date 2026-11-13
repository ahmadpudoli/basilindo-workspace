<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $claim->request_number }} · {{ $claim->employee?->company?->name }}</title>
    @vite('resources/js/claim-print.js')
    <style>
        @page { size: A4 portrait; margin: 14mm; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 32px; color: #172033; background: #eef3f9; font: 14px/1.55 Arial, sans-serif; }
        .paper { width: min(210mm, calc(100vw - 64px)); min-height: 297mm; max-width: 900px; margin: 0 auto 24px; padding: 34px 42px; background: #fff; border: 1px solid #dce5f0; border-radius: 14px; box-shadow: 0 14px 38px #1e3a5f12; }
        .toolbar { display: flex; justify-content: flex-end; margin-bottom: 18px; }
        button { padding: 9px 16px; border: 0; border-radius: 6px; background: #0875cf; color: #fff; font-weight: 700; cursor: pointer; }
        .letterhead { position: relative; padding: 0 0 18px; border-bottom: 3px solid transparent; border-image: linear-gradient(90deg, #0875cf, #8b5cf6) 1; }
        .company { margin: 0; color: #0759a1; font-size: 25px; font-weight: 700; letter-spacing: .2px; }
        .letterhead-caption { margin-top: 3px; color: #718096; font-size: 11px; letter-spacing: 1.4px; text-transform: uppercase; }
        .document-title { margin: 25px 0 3px; font-size: 23px; }
        .muted { color: #65738a; }
        .status { display: inline-block; padding: 4px 10px; border-radius: 20px; background: #dcfce7; color: #166534; font-weight: 700; }
        .summary { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 24px; margin: 20px 0; padding: 20px; border: 1px solid #dbe3ee; border-radius: 10px; background: linear-gradient(135deg, #f7fbff, #faf8ff); }
        .field label { display: block; margin-bottom: 2px; color: #65738a; font-size: 12px; }
        .field strong { overflow-wrap: anywhere; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 9px 8px; border: 1px solid #dbe3ee; text-align: left; vertical-align: top; }
        th { background: #f2f6fb; font-size: 12px; text-transform: uppercase; }
        .number { text-align: right; white-space: nowrap; }
        tfoot th { text-align: right; }
        .signatures { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-top: 42px; }
        .signature { min-height: 135px; padding: 14px; border: 1px solid #dce5f0; border-radius: 10px; background: #fbfdff; }
        .signature-title { color: #66758c; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
        .approval-check { display: flex; align-items: center; gap: 9px; margin: 16px 0 8px; color: #15803d; font-size: 15px; font-weight: 700; }
        .check-icon { display: inline-grid; width: 28px; height: 28px; place-items: center; border-radius: 50%; background: #dcfce7; color: #15803d; font-size: 18px; }
        .signer { font-weight: 700; }
        .approval-time { margin-top: 2px; color: #65738a; font-size: 12px; }
        .requester-signature-space { height: 48px; margin-top: 8px; border-bottom: 1px solid #8793a5; }
        .attachment-page { break-before: page; page-break-before: always; }
        .attachment-page { min-height: 0; height: auto; }
        .attachment-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding-bottom: 12px; border-bottom: 2px solid #dce7f3; }
        .attachment-kicker { color: #0875cf; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .attachment-title { margin: 4px 0 0; font-size: 20px; }
        .image-attachment { display: flex; align-items: center; gap: 14px; margin: 7px 0; padding: 8px 10px; border: 1px solid #dce5f0; border-radius: 8px; break-inside: avoid; page-break-inside: avoid; }
        .image-attachment img { display: block; flex: 0 1 auto; width: auto; max-width: 72%; max-height: 90mm; object-fit: contain; }
        .image-attachment figcaption { flex: 1 1 auto; min-width: 0; color: #526178; font-size: 12px; }
        .image-attachment figcaption strong { display: block; margin-bottom: 3px; color: #172033; font-size: 13px; }
        .image-attachment figcaption small { display: block; margin-top: 4px; }
        .pdf-print-status { margin: 10px 0; padding: 10px 12px; border-radius: 8px; background: #f4f7fb; color: #526178; }
        .pdf-print-document, .pdf-pages { display: contents; }
        .pdf-page-sheet { break-before: page; page-break-before: always; display: flex; flex-direction: column; width: min(210mm, calc(100vw - 64px)); min-height: 297mm; max-width: 900px; margin: 0 auto 24px; padding: 14mm; border: 1px solid #dce5f0; border-radius: 14px; background: #fff; box-shadow: 0 14px 38px #1e3a5f12; }
        .pdf-page-sheet.is-first-attachment-page { break-before: auto; page-break-before: auto; }
        .pdf-page-heading { display: flex; justify-content: space-between; gap: 12px; padding-bottom: 8px; border-bottom: 1px solid #dce5f0; color: #526178; font-size: 12px; }
        .pdf-page-image { display: block; align-self: center; flex: 1 1 auto; max-width: 100%; max-height: 250mm; margin: auto; object-fit: contain; }
        .pdf-page-error { color: #b42318; }
        .attachment-empty { padding: 30px; border: 1px dashed #c6d3e2; border-radius: 8px; color: #65738a; text-align: center; }
        @media print {
            body { padding: 0; background: #fff; }
            .toolbar { display: none; }
            .paper { width: auto; max-width: none; min-height: 269mm; height: auto; margin: 0; padding: 0; border: 0; border-radius: 0; box-shadow: none; }
            .attachment-page { min-height: 0; height: auto; margin: 0; break-before: auto; page-break-before: auto; }
            .image-attachment img { max-height: 90mm; }
            .pdf-page-sheet { break-before: page; page-break-before: always; width: auto; max-width: none; min-height: 269mm; height: 269mm; margin: 0; padding: 0; border: 0; border-radius: 0; box-shadow: none; }
            .pdf-page-image { max-height: 250mm; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; }
        }
        @media (max-width: 700px) { .signatures { grid-template-columns: 1fr; } }
        @media (max-width: 600px) { body { padding: 12px; } .paper, .pdf-page-sheet { width: calc(100vw - 24px); padding: 22px 18px; } .image-attachment { align-items: flex-start; flex-direction: column; } .image-attachment img { max-width: 100%; max-height: 100mm; } .summary { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<main class="paper">
    <div class="toolbar"><button type="button" data-print-document disabled>Memuat pratinjau…</button></div>
    <header class="letterhead"><h1 class="company">{{ $claim->employee?->company?->name ?? 'Perusahaan' }}</h1><div class="letterhead-caption">Dokumen resmi pengajuan biaya</div></header>
    <h2 class="document-title">Dokumen Pengajuan Reimbursement</h2>
    <div class="muted">Nomor pengajuan: {{ $claim->request_number }}</div>
    <section class="summary">
        <div class="field"><label>Status pengajuan</label><strong><span class="status">{{ $claim->status_label }}</span></strong></div>
        <div class="field"><label>Jenis</label><strong>Reimbursement</strong></div>
        <div class="field"><label>Nama karyawan</label><strong>{{ $claim->employee?->full_name ?? '—' }}</strong></div>
        <div class="field"><label>Tanggal pengajuan awal</label><strong>{{ ($claim->submitted_at ?: $claim->created_at)?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '—' }} WIB</strong></div>
        @if($claim->final_approved_at)<div class="field"><label>Persetujuan akhir</label><strong>{{ $claim->finalApprover?->name ?? '—' }}</strong><div class="muted">{{ $claim->final_approved_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }} WIB</div></div>@endif
        <div class="field"><label>Keperluan</label><strong>{{ $claim->purpose ?: ($claim->expense_scope === 'external_project' ? 'Project atau client' : 'Operasional internal') }}</strong></div>
        <div class="field"><label>Project / referensi</label><strong>{{ $claim->reference_name_snapshot ?: $claim->project?->name ?: $claim->opportunity?->name ?: '—' }}</strong></div>
        <div class="field"><label>Client</label><strong>{{ $claim->opportunity?->account?->name ?: $claim->project?->clients?->first()?->name ?: '—' }}</strong></div>
        <div class="field"><label>Nomor PO</label><strong>{{ $claim->po_number ?: '—' }}</strong></div>
    </section>
    <h3>Rincian biaya</h3>
    <table>
        <thead><tr><th>#</th><th>Tanggal nota</th><th>Kategori</th><th>Deskripsi</th><th class="number">Nominal</th></tr></thead>
        <tbody>
        @foreach($claim->expenseItems as $item)
            <tr><td>{{ $item->line_number }}</td><td>{{ $item->receipt_date?->format('d/m/Y') ?? '—' }}</td><td>{{ $item->category }}</td><td>{{ $item->description }}</td><td class="number">{{ $item->currency }} {{ number_format((float) $item->amount, 2, ',', '.') }}</td></tr>
        @endforeach
        </tbody>
        <tfoot><tr><th colspan="4">Total pengajuan</th><th class="number">{{ $claim->currency }} {{ number_format((float) $claim->amount, 2, ',', '.') }}</th></tr></tfoot>
    </table>
    <div class="signatures">
        <section class="signature"><div class="signature-title">Pemohon</div><div class="requester-signature-space"></div><div class="signer">{{ $claim->employee?->full_name ?? '—' }}</div><div class="approval-time">Diajukan {{ ($claim->submitted_at ?: $claim->created_at)?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '—' }} WIB</div></section>
        <section class="signature"><div class="signature-title">Persetujuan Atasan</div>@if($claim->manager_approved_at)<div class="approval-check"><span class="check-icon">✓</span><span>Disetujui</span></div><div class="signer">{{ $claim->managerApprover?->name ?? 'Atasan' }}</div><div class="approval-time">{{ $claim->manager_approved_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }} WIB</div>@else<div class="muted">Belum disetujui</div>@endif</section>
        <section class="signature"><div class="signature-title">Persetujuan Finance</div>@if($claim->finance_approved_at)<div class="approval-check"><span class="check-icon">✓</span><span>Disetujui</span></div><div class="signer">{{ $claim->financeApprover?->name ?? 'Finance' }}</div><div class="approval-time">{{ $claim->finance_approved_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }} WIB</div>@else<div class="muted">Belum disetujui</div>@endif</section>
    </div>
</main>
@forelse($attachmentPages as $page)
    @if($page['type'] === 'images')
        <section class="paper attachment-page"><header class="attachment-heading"><div><div class="attachment-kicker">Dokumen pendukung</div><h2 class="attachment-title">Lampiran bukti rincian biaya</h2></div><strong>{{ $claim->request_number }}</strong></header>
            @foreach($page['attachments'] as $attachment)
                <figure class="image-attachment"><img src="{{ $attachment['url'] }}" alt="Bukti rincian biaya {{ $attachment['item']->line_number }}"><figcaption><strong>Rincian #{{ $attachment['item']->line_number }} · {{ $attachment['item']->category }}</strong>{{ $attachment['item']->description }}<small>{{ $attachment['item']->currency }} {{ number_format((float) $attachment['item']->amount, 2, ',', '.') }} · Tanggal nota: {{ $attachment['item']->receipt_date?->format('d/m/Y') ?? '—' }}</small></figcaption></figure>
            @endforeach
        </section>
    @elseif($page['type'] === 'pdf')
        @php($attachment = $page['attachments'][0])
        <div class="pdf-print-document" data-pdf-print data-pdf-url="{{ $attachment['url'] }}" data-line-number="{{ $attachment['item']->line_number }}" data-category="{{ $attachment['item']->category }}" data-description="{{ $attachment['item']->description }}"><div class="pdf-print-status" data-pdf-status>Memuat PDF lampiran rincian #{{ $attachment['item']->line_number }}…</div><div class="pdf-pages" data-pdf-pages></div></div>
    @else
        @php($attachment = $page['attachments'][0])
        <section class="paper attachment-page"><header class="attachment-heading"><div><div class="attachment-kicker">Dokumen pendukung</div><h2 class="attachment-title">Lampiran bukti rincian biaya</h2></div><strong>{{ $claim->request_number }}</strong></header><div class="attachment-empty">Bukti rincian #{{ $attachment['item']->line_number }} tersedia sebagai {{ $attachment['mime_type'] }} dan tidak dapat ditampilkan di pratinjau cetak.</div></section>
    @endif
@empty
    <section class="paper attachment-page"><header class="attachment-heading"><div><div class="attachment-kicker">Dokumen pendukung</div><h2 class="attachment-title">Lampiran bukti rincian biaya</h2></div><strong>{{ $claim->request_number }}</strong></header><div class="attachment-empty">Tidak ada file lampiran pada rincian biaya pengajuan ini.</div></section>
@endforelse
</body>
</html>
