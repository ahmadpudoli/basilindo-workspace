@extends('layouts.app', ['title' => $isDraft ? 'Edit draft reimbursement' : 'Ajukan reimbursement'])
@section('content')
@php
    $isRevision = $isRevision ?? false;
    $claimItems = old('items', count($draftItems ?? []) > 0 ? $draftItems : [['receipt_date'=>'','activity_date'=>'','category'=>'','description'=>'','currency'=>'IDR','amount'=>'','attachment_reference'=>'']]);
    if ($isDraft) {
        foreach ($claimItems as $itemIndex => &$claimItem) {
            $claimItem['attachment_object_key'] ??= data_get($draftItems, $itemIndex.'.attachment_object_key');
        }
        unset($claimItem);
    }
    $expenseScope = old('expense_scope', $claimData['expense_scope'] ?? 'internal');
    $referenceType = old('work_reference_type', $claimData['work_reference_type'] ?? 'none');
    $hasFinanceApprover = $financeApprovers->isNotEmpty();
    $hasFinalApprover = $finalApprovers->isNotEmpty();
@endphp
<div class="mb-4"><div class="text-uppercase text-secondary small fw-semibold mb-2">Reimbursement</div><h1 class="h3 mb-1">{{ $isRevision ? 'Revisi pengajuan reimbursement' : ($isDraft ? 'Edit draft reimbursement' : 'Ajukan reimbursement') }}</h1><p class="text-secondary mb-0">{{ $isRevision ? 'Perbaiki data sesuai catatan approver, lalu kirim kembali untuk pemeriksaan.' : 'Lengkapi data pengajuan. Total biaya dihitung otomatis oleh sistem.' }}</p></div>
@if($isRevision)<div class="alert alert-warning workspace-alert mb-3"><strong>Revisi {{ $claim->revision_type === 'administrative' ? 'administrasi' : 'substansi' }} diminta oleh {{ $claim->revision_requested_role === 'finance' ? 'Finance' : 'Atasan' }}.</strong><div class="mt-1">{{ $claim->revision_reason }}</div></div>@endif
@if($errors->any())<div class="alert alert-danger workspace-alert mb-3"><strong>Periksa kembali data pengajuan:</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ $claimAction }}" class="card card-body workspace-leave-form workspace-leave-form-columns" id="claim-form" data-client-validation="hris-claim" data-attachment-upload-url="{{ $isDraft ? route('workspace.hris.claims.attachments.update', $claim) : route('workspace.hris.claims.attachments.store') }}" data-confirm="{{ $isRevision ? 'Kirim kembali revisi reimbursement untuk pemeriksaan sesuai jenis revisi.' : 'Kirim pengajuan reimbursement untuk diproses atasan? Setelah dikirim, draft tidak dapat dihapus lagi.' }}" data-confirm-value="submit" data-confirm-action="{{ $isRevision ? 'Kirim revisi' : 'Kirim pengajuan' }}" novalidate>
@csrf @if($isDraft) @method('PUT') @endif
<input type="hidden" name="employee_id" value="{{ $employee->id }}">
<div class="workspace-leave-column">
<section class="workspace-form-section mb-4"><h2 class="h6 mb-3">Informasi pengajuan</h2><div class="workspace-claim-type-info"><span class="workspace-claim-type-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 3.75h8l3 3v13.5l-2-1.5-2 1.5-2-1.5-2 1.5-2-1.5-2 1.5V5.75A2 2 0 0 1 7 3.75Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 9h5M9 12.5h6M9 16h3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span><strong>Reimbursement</strong><small>Penggantian biaya yang sudah dikeluarkan.</small></span><span class="badge rounded-pill text-bg-primary-subtle text-primary-emphasis">Pengajuan biaya</span></div><input type="hidden" name="type" value="reimbursement"><div class="workspace-floating-field"><input id="claim-request-number" class="form-control" value="{{ $requestNumber }}" readonly><label for="claim-request-number" class="form-label">Nomor pengajuan</label></div><div class="form-text mt-2">Nomor menggunakan master penomoran perusahaan dan dikunci saat pengajuan dikirim.</div></section>
<section class="workspace-leave-section"><div class="workspace-leave-section-heading"><div><h2>Rincian biaya <span class="text-danger">*</span></h2><p>Satu pengajuan dapat memiliki beberapa nota.</p></div><button type="button" class="btn btn-outline-primary btn-sm" id="add-claim-item">+ Tambah rincian</button></div><div id="claim-items">
@foreach($claimItems as $index => $item)
<div class="border rounded-3 p-3 mb-3 claim-item" data-has-existing-attachment="{{ filled($item['attachment_object_key'] ?? null) ? 'true' : 'false' }}">
    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] ?? '' }}">
    <div class="d-flex justify-content-between align-items-center mb-3"><span class="badge rounded-pill bg-primary-subtle text-primary">Rincian {{ $loop->iteration }}</span><button type="button" class="btn btn-sm btn-outline-danger remove-claim-item">Hapus</button></div>
    <div class="row g-3">
        <div class="col-md-6"><div class="workspace-floating-field"><input type="date" name="items[{{ $index }}][receipt_date]" value="{{ $item['receipt_date'] ?? '' }}" class="form-control" required><label class="form-label">Tanggal nota <span class="text-danger">*</span></label></div></div>
        <div class="col-md-6"><div class="workspace-floating-field"><input type="date" name="items[{{ $index }}][activity_date]" value="{{ $item['activity_date'] ?? '' }}" class="form-control"><label class="form-label">Tanggal kegiatan</label></div></div>
        <div class="col-md-6"><div class="workspace-floating-field"><select name="items[{{ $index }}][expense_category_id]" class="form-select" required><option value="">Pilih kategori biaya</option>@foreach($expenseCategories as $category)<option value="{{ $category->id }}" @selected((string) old("items.{$index}.expense_category_id", $item['expense_category_id'] ?? '') === (string) $category->id)>{{ $category->name }}@unless($category->is_active && ! $category->archived_at) (Diarsipkan)@endunless</option>@endforeach</select><label class="form-label">Kategori biaya <span class="text-danger">*</span></label></div></div>
        <div class="col-md-6"><div class="workspace-floating-field"><select name="items[{{ $index }}][currency]" class="form-select"><option value="IDR" @selected(($item['currency'] ?? 'IDR') === 'IDR')>IDR</option><option value="USD" @selected(($item['currency'] ?? '') === 'USD')>USD</option><option value="SGD" @selected(($item['currency'] ?? '') === 'SGD')>SGD</option></select><label class="form-label">Mata uang</label></div></div>
        <div class="col-md-6"><div class="workspace-floating-field"><input type="text" inputmode="decimal" autocomplete="off" data-amount-input name="items[{{ $index }}][amount]" value="{{ old("items.{$index}.amount", $item['amount'] ?? '') }}" class="form-control" placeholder="0,00" required><label class="form-label">Nominal <span class="text-danger">*</span></label></div></div>
        <div class="col-md-6"><div class="workspace-floating-field"><input name="items[{{ $index }}][attachment_reference]" value="{{ old("items.{$index}.attachment_reference", $item['attachment_reference'] ?? '') }}" class="form-control" placeholder="Contoh: INV-2026-00123"><label class="form-label">Referensi nota <span class="text-secondary">(Opsional)</span></label></div></div>
        <div class="col-12"><div class="workspace-floating-field"><textarea name="items[{{ $index }}][description]" class="form-control workspace-claim-compact-textarea" rows="2" required>{{ old("items.{$index}.description", $item['description'] ?? '') }}</textarea><label class="form-label">Deskripsi <span class="text-danger">*</span></label></div></div>
        <div class="col-12"><div class="claim-attachment-field"><div class="small fw-semibold mb-2">Bukti nota <span class="text-danger">*</span></div><input type="hidden" name="items[{{ $index }}][attachment_token]" value="{{ old("items.{$index}.attachment_token", '') }}" data-claim-attachment-token><input type="hidden" name="items[{{ $index }}][remove_attachment]" value="0" data-claim-remove-marker><input type="file" class="d-none" accept=".pdf,application/pdf,image/*" data-claim-attachment-input><div class="d-flex flex-wrap gap-2"><button type="button" class="btn btn-sm btn-outline-secondary" data-choose-claim-file>Pilih file</button><button type="button" class="btn btn-sm btn-outline-secondary" data-capture-claim-photo>Ambil foto</button></div><div class="claim-attachment-preview mt-2" data-claim-attachment-preview @if(blank($item['attachment_object_key'] ?? null)) hidden @endif @if($isDraft && filled($item['attachment_object_key'] ?? null) && data_get($draftItems ?? [], $index.'.id')) data-existing-preview-url="{{ route('workspace.hris.claims.expense-items.attachment', ['claim' => $claim, 'item' => data_get($draftItems, $index.'.id'), 'preview' => 1]) }}" @endif></div><div class="form-text" data-claim-attachment-name>{{ old("items.{$index}.attachment_token") ? 'Bukti nota siap diunggah.' : (filled($item['attachment_object_key'] ?? null) ? 'Bukti nota sudah terlampir.' : 'Bukti nota wajib · PDF atau gambar, maksimal 10 MB.') }}</div></div></div>
    </div>
</div>
@endforeach
</div><div class="border-top pt-3 d-flex justify-content-between"><span class="fw-semibold">Total pengajuan</span><strong id="claim-total">IDR 0,00</strong></div></section>
</div>
<div class="workspace-leave-column">
<section class="workspace-leave-section workspace-claim-scope-section"><div class="workspace-leave-section-heading"><div><h2>Keperluan <span class="text-danger">*</span></h2><p>Pilih konteks penggunaan biaya.</p></div></div><div class="workspace-claim-scope-grid"><label class="workspace-claim-scope-card is-internal"><input type="radio" name="expense_scope" value="internal" @checked($expenseScope === 'internal')><span class="workspace-claim-scope-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M3.5 10.5 12 4l8.5 6.5v9a.5.5 0 0 1-.5.5h-16a.5.5 0 0 1-.5-.5v-9Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 20v-6h6v6M7.5 10h.01M16.5 10h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="workspace-claim-scope-copy"><strong>Operasional internal</strong><small>Biaya untuk kegiatan dan kebutuhan internal perusahaan.</small></span><span class="workspace-claim-scope-check" aria-hidden="true">✓</span></label><label class="workspace-claim-scope-card is-external"><input type="radio" name="expense_scope" value="external_project" @checked($expenseScope === 'external_project')><span class="workspace-claim-scope-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M3.5 20V6.5A1.5 1.5 0 0 1 5 5h5l2 2h7A1.5 1.5 0 0 1 20.5 8.5V20" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M3.5 11h17M8 15h3M8 18h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="workspace-claim-scope-copy"><strong>Project atau client</strong><small>Biaya terkait project atau opportunity tertentu.</small></span><span class="workspace-claim-scope-check" aria-hidden="true">✓</span></label></div><div id="claim-reference-field" class="mt-3" @if($expenseScope !== 'external_project') hidden @endif><label class="form-label fw-semibold">Referensi pekerjaan</label><div class="d-flex flex-wrap gap-3 mb-3"><label><input type="radio" name="work_reference_type" value="project" @checked($referenceType === 'project')> Project</label><label><input type="radio" name="work_reference_type" value="opportunity" @checked($referenceType === 'opportunity')> Opportunity</label><label><input type="radio" name="work_reference_type" value="none" @checked($referenceType === 'none')> Tanpa referensi</label></div><div id="claim-project-field" @if($referenceType !== 'project' || $expenseScope !== 'external_project') hidden @endif><select name="project_id" class="form-select"><option value="">Pilih project</option>@foreach($projects as $project)<option value="{{ $project->id }}" @selected((string) old('project_id', $claimData['project_id'] ?? '') === (string) $project->id)>{{ $project->name }}</option>@endforeach</select><div class="form-text">Project mengikuti akses perusahaan dan membership project user.</div></div><div id="claim-opportunity-field" @if($referenceType !== 'opportunity' || $expenseScope !== 'external_project') hidden @endif><select name="opportunity_id" class="form-select"><option value="">Pilih opportunity</option>@foreach($opportunities as $opportunity)<option value="{{ $opportunity->id }}" @selected((string) old('opportunity_id', $claimData['opportunity_id'] ?? '') === (string) $opportunity->id)>{{ $opportunity->name }}</option>@endforeach</select><div class="form-text">Opportunity mengikuti akses CRM user.</div></div></div></section>
<section id="claim-po-number-field" class="workspace-leave-section" @if($expenseScope !== 'external_project') hidden @endif><div class="workspace-floating-field"><input name="po_number" value="{{ old('po_number', $claimData['po_number'] ?? '') }}" class="form-control" maxlength="100"><label class="form-label">Nomor PO</label></div></section>
<section class="workspace-leave-section"><div class="workspace-leave-section-heading"><h2>Tujuan atau keterangan</h2></div><div class="workspace-floating-field"><textarea name="purpose" class="form-control workspace-claim-compact-textarea" rows="2" maxlength="2000">{{ old('purpose', $claimData['purpose'] ?? '') }}</textarea><label class="form-label">Keterangan umum</label></div></section>
<div class="accordion workspace-claim-info-accordion" id="claim-information-accordion">
    <div class="accordion-item border rounded-3 overflow-hidden">
        <h2 class="accordion-header" id="claim-information-heading"><button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#claim-information-content" aria-expanded="false" aria-controls="claim-information-content"><span class="workspace-claim-info-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 19v-1.2A4.8 4.8 0 0 1 8.3 13h1.4a4.8 4.8 0 0 1 4.8 4.8V19M16 5.5a3 3 0 0 1 0 5.8M16.5 13.2a4.5 4.5 0 0 1 4 4.5V19" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span>Informasi karyawan dan alur persetujuan</span></button></h2>
        <div id="claim-information-content" class="accordion-collapse collapse" aria-labelledby="claim-information-heading" data-bs-parent="#claim-information-accordion">
            <div class="accordion-body p-3">
                <section class="workspace-leave-section workspace-leave-employee-section"><div class="workspace-leave-section-heading"><h2>Informasi karyawan</h2></div><div class="workspace-employee-summary"><div><span>Nama</span><strong>{{ $employee->full_name }}</strong></div><div><span>NIP</span><strong>{{ $employee->employee_number }}</strong></div><div><span>Jabatan</span><strong>{{ $employee->job_title ?? 'Belum diatur' }}</strong></div><div><span>Perusahaan</span><strong>{{ $employee->company?->name ?? '-' }}</strong></div></div></section>
                <section class="workspace-leave-section workspace-leave-manager-section"><div class="workspace-leave-section-heading"><h2>Informasi atasan</h2></div><div class="workspace-approval-panel">@if($employee->manager)<strong>{{ $employee->manager->full_name }}</strong><span>{{ $employee->manager->job_title ?? 'Atasan langsung' }} · {{ $employee->manager->employee_number }}</span>@else<span class="text-danger">Atasan langsung belum dikonfigurasi.</span>@endif</div></section>
                <section class="workspace-leave-section"><div class="workspace-leave-section-heading"><h2>Informasi Finance</h2></div><div class="workspace-approval-panel {{ $hasFinanceApprover ? '' : 'is-missing' }}">@forelse($financeApprovers as $approver)<div class="workspace-approval-person"><span class="workspace-approval-avatar">{{ strtoupper(substr($approver->user?->name ?? '?', 0, 1)) }}</span><span><strong>{{ $approver->user?->name }}</strong><small>{{ $approver->user?->email }}</small></span></div>@empty<span class="text-danger">Approver Finance belum dikonfigurasi.</span>@endforelse</div></section>
                <section class="workspace-leave-section"><div class="workspace-leave-section-heading"><h2>Informasi approver final</h2></div><div class="workspace-approval-panel {{ $hasFinalApprover ? '' : 'is-missing' }}">@forelse($finalApprovers as $approver)<div class="workspace-approval-person"><span class="workspace-approval-avatar">{{ strtoupper(substr($approver->user?->name ?? '?', 0, 1)) }}</span><span><strong>{{ $approver->user?->name }}</strong><small>{{ $approver->user?->email }}</small></span></div>@empty<span class="text-secondary">Tidak diperlukan atau belum dikonfigurasi sesuai threshold.</span>@endforelse</div></section>
            </div>
        </div>
    </div>
</div>
<aside class="claim-finance-reminder" role="note" aria-label="Pengingat bukti reimbursement"><span class="claim-finance-reminder-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3.5 2.8 20h18.4L12 3.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M12 9v5m0 2.5h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><span><strong>Penting</strong><span>Bukti reimbursement, seperti nota, kuitansi, struk, dan bukti lainnya, harap disimpan dan diserahkan kepada Finance pada akhir bulan pengajuan.</span></span></aside>
</div>
<div class="workspace-leave-actions d-flex justify-content-end gap-2"><a href="{{ route('workspace.hris.claims') }}" class="btn btn-light btn-sm">Batal</a>@if($isRevision)<button class="btn btn-outline-secondary btn-sm" type="submit" name="action" value="draft">Simpan perubahan</button>@else<button class="btn btn-outline-secondary btn-sm" type="submit" name="action" value="draft">Simpan draft</button>@endif<button class="btn btn-primary btn-sm" type="submit" name="action" value="submit">{{ $isRevision ? 'Kirim revisi' : 'Kirim pengajuan' }}</button></div>
</form>
<div class="modal fade" id="claim-camera-modal" tabindex="-1" aria-labelledby="claim-camera-title" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 class="modal-title fs-6" id="claim-camera-title">Ambil foto bukti nota</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body"><video class="claim-camera-video w-100 rounded-3" data-claim-camera-video autoplay playsinline></video><div class="alert alert-warning mt-3 mb-0" data-claim-camera-error hidden></div></div><div class="modal-footer"><button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary btn-sm" data-take-claim-photo disabled>Ambil foto</button></div></div></div></div>
<div class="modal fade" id="claim-attachment-preview-modal" tabindex="-1" aria-labelledby="claim-attachment-preview-title" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-xl claim-verification-modal"><div class="modal-content"><div class="modal-header"><h2 class="modal-title fs-6" id="claim-attachment-preview-title">Preview Bukti Nota</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body"><div class="claim-verification-grid"><section class="claim-verification-details"><h3 class="h6 mb-3">Rincian biaya</h3><dl class="claim-verification-list" data-claim-preview-details></dl></section><section class="claim-verification-document"><h3 class="h6 mb-3">Dokumen bukti</h3><div class="claim-image-viewer" data-claim-image-viewer><div class="claim-image-zoom-toolbar" data-claim-image-zoom-toolbar hidden><button type="button" class="btn btn-sm btn-outline-secondary" data-claim-zoom-out aria-label="Perkecil gambar">−</button><span data-claim-zoom-level aria-live="polite">100%</span><button type="button" class="btn btn-sm btn-outline-secondary" data-claim-zoom-in aria-label="Perbesar gambar">+</button><button type="button" class="btn btn-sm btn-outline-secondary" data-claim-zoom-reset>Sesuaikan</button></div><iframe class="claim-attachment-preview-frame" data-claim-preview-frame title="Preview Bukti Nota" referrerpolicy="no-referrer"></iframe></div></section></div></div></div></div></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('claim-form');
    const referenceDetails = @json($referenceDetails);
    const columns = form.querySelectorAll('.workspace-leave-column');
    const leftColumn = columns[0];
    const rightColumn = columns[1];
    const expenseDetails = leftColumn?.querySelector('#claim-items')?.closest('section');
    const expenseScope = rightColumn?.querySelector('.workspace-claim-scope-section');
    const poNumber = rightColumn?.querySelector('#claim-po-number-field');
    const purpose = rightColumn?.querySelector('[name="purpose"]')?.closest('section');
    const approvalInfo = rightColumn?.querySelector('.workspace-claim-info-accordion');
    if (leftColumn && rightColumn && expenseDetails && expenseScope && purpose && approvalInfo) {
        leftColumn.append(expenseScope);
        if (poNumber) leftColumn.append(poNumber);
        leftColumn.append(approvalInfo);
        rightColumn.append(purpose, expenseDetails);
    }
    const syncReference = () => {
        const external = form.querySelector('[name="expense_scope"]:checked')?.value === 'external_project';
        const reference = form.querySelector('[name="work_reference_type"]:checked')?.value || 'none';
        const referenceField = document.getElementById('claim-reference-field');
        const poNumberField = document.getElementById('claim-po-number-field');
        const poNumber = poNumberField.querySelector('[name="po_number"]');
        poNumberField.hidden = !external;
        poNumber.disabled = !external;
        const projectField = document.getElementById('claim-project-field');
        const opportunityField = document.getElementById('claim-opportunity-field');
        referenceField.hidden = !external;
        projectField.hidden = !external || reference !== 'project';
        opportunityField.hidden = !external || reference !== 'opportunity';
        projectField.querySelector('select').disabled = !external || reference !== 'project';
        opportunityField.querySelector('select').disabled = !external || reference !== 'opportunity';
    };
    form.querySelectorAll('[name="expense_scope"], [name="work_reference_type"]').forEach((input) => input.addEventListener('change', syncReference));
    syncReference();
    const list=document.getElementById('claim-items'), add=document.getElementById('add-claim-item'), total=document.getElementById('claim-total');
    if(!list||!add||!total)return;
    let nextIndex=Math.max(-1,...[...list.querySelectorAll('.claim-item [name^="items["]')].map(field=>Number(field.name.match(/^items\[(\d+)\]/)?.[1] ?? -1)))+1;
    const parseAmount = (value) => {
        const raw = String(value || '');
        const normalized = raw.includes(',') ? raw.replace(/\./g, '').replace(',', '.') : (/^\d+\.\d{1,2}$/.test(raw) ? raw : raw.replace(/\./g, ''));
        const amount = Number(normalized);
        return Number.isFinite(amount) ? amount : 0;
    };
    const formatAmount = (value) => value === '' ? '' : parseAmount(value).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const refresh=()=>{
        let amount=0;
        list.querySelectorAll('[data-amount-input]').forEach(input=>amount+=parseAmount(input.value));
        total.textContent=`IDR ${amount.toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2})}`;
        list.querySelectorAll('.claim-item').forEach((row,index)=>row.querySelector('.badge').textContent=`Rincian ${index+1}`);
    };
    list.querySelectorAll('[data-amount-input]').forEach(input=>{input.value=formatAmount(input.value);input.addEventListener('focus',()=>{input.value=input.value.replace(/\./g,'')});input.addEventListener('input',()=>{let value=input.value.replace(/[^0-9,]/g,'');const comma=value.indexOf(',');if(comma!==-1)value=value.slice(0,comma+1)+value.slice(comma+1).replace(/,/g,'').slice(0,2);input.value=value;refresh()});input.addEventListener('blur',()=>{input.value=formatAmount(input.value);refresh()})});
    add.addEventListener('click',()=>{const row=list.querySelector('.claim-item').cloneNode(true);row.dataset.hasExistingAttachment='false';row.querySelectorAll('input,textarea').forEach(field=>{field.value='';field.name=field.name.replace(/items\[\d+\]/,`items[${nextIndex}]`)});row.querySelectorAll('select').forEach(field=>{field.value=field.name.includes('[currency]')?'IDR':'';field.name=field.name.replace(/items\[\d+\]/,`items[${nextIndex}]`)});const preview=row.querySelector('[data-claim-attachment-preview]');preview.replaceChildren();preview.hidden=true;delete preview.dataset.filePreviewUrl;delete preview.dataset.existingPreviewUrl;row.querySelector('[data-claim-remove-marker]').value='0';row.querySelector('[data-claim-attachment-name]').textContent='Bukti nota wajib · PDF atau gambar, maksimal 10 MB.';list.append(row);const amountInput=row.querySelector('[data-amount-input]');amountInput.addEventListener('focus',()=>{amountInput.value=amountInput.value.replace(/\./g,'')});amountInput.addEventListener('input',()=>{let value=amountInput.value.replace(/[^0-9,]/g,'');const comma=value.indexOf(',');if(comma!==-1)value=value.slice(0,comma+1)+value.slice(comma+1).replace(/,/g,'').slice(0,2);amountInput.value=value;refresh()});amountInput.addEventListener('blur',()=>{amountInput.value=formatAmount(amountInput.value);refresh()});nextIndex+=1;refresh()});
    list.addEventListener('click',event=>{if(event.target.closest('.remove-claim-item')&&list.children.length>1){const row=event.target.closest('.claim-item');const preview=row.querySelector('[data-claim-attachment-preview]');if(preview.dataset.filePreviewUrl?.startsWith('blob:'))URL.revokeObjectURL(preview.dataset.filePreviewUrl);preview.querySelectorAll('[data-preview-object-url]').forEach(image=>URL.revokeObjectURL(image.dataset.previewObjectUrl));row.remove();refresh()}});
    const cameraModal = document.getElementById('claim-camera-modal');
    const attachmentPreviewModal = document.getElementById('claim-attachment-preview-modal');
    const attachmentPreviewFrame = attachmentPreviewModal?.querySelector('[data-claim-preview-frame]');
    const attachmentPreviewDetails = attachmentPreviewModal?.querySelector('[data-claim-preview-details]');
    const cameraVideo = cameraModal?.querySelector('[data-claim-camera-video]');
    const cameraError = cameraModal?.querySelector('[data-claim-camera-error]');
    let activeCameraInput = null;
    let cameraStream = null;
    const stopCamera = () => { cameraStream?.getTracks().forEach((track) => track.stop()); cameraStream = null; if (cameraVideo) cameraVideo.srcObject = null; };
    const formatFileSize = (bytes) => bytes < 1024 * 1024 ? `${Math.max(1, Math.round(bytes / 1024))} KB` : `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    const compressClaimImage = async (file) => {
        if (!file?.type?.startsWith('image/')) return file;
        if (typeof createImageBitmap !== 'function') return file;

        let bitmap;
        try { bitmap = await createImageBitmap(file); } catch { return file; }
        try {
        let maxDimension = 2400;
        let quality = 0.84;
        let bestBlob = null;
        for (let attempt = 0; attempt < 20; attempt++) {
            const ratio = Math.min(1, maxDimension / Math.max(bitmap.width, bitmap.height));
            const width = Math.max(1, Math.round(bitmap.width * ratio));
            const height = Math.max(1, Math.round(bitmap.height * ratio));
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const context = canvas.getContext('2d', { alpha: false });
            if (!context) return file;
            context.fillStyle = '#fff';
            context.fillRect(0, 0, width, height);
            context.drawImage(bitmap, 0, 0, width, height);
            const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));
            if (!blob) break;
            if (!bestBlob || blob.size < bestBlob.size) bestBlob = blob;
            if (blob.size <= 1.5 * 1024 * 1024) { bestBlob = blob; break; }

            if (quality > 0.68) {
                quality = Math.max(0.68, Math.round((quality - 0.08) * 100) / 100);
            } else if (maxDimension > 1600) {
                maxDimension = Math.max(1600, Math.round(maxDimension * 0.85));
                quality = 0.78;
            } else break;
        }
        if (!bestBlob || bestBlob.size >= file.size) return file;

            const baseName = file.name.replace(/\.[^.]+$/, '') || 'bukti-nota';
        return new File([bestBlob], `${baseName}.jpg`, { type: 'image/jpeg', lastModified: file.lastModified });
        } finally {
            bitmap.close?.();
        }
    };
    const renderAttachment = (row, file = null) => {
        const preview = row.querySelector('[data-claim-attachment-preview]');
        const label = row.querySelector('[data-claim-attachment-name]');
        if (preview.dataset.filePreviewUrl?.startsWith('blob:')) URL.revokeObjectURL(preview.dataset.filePreviewUrl);
        delete preview.dataset.filePreviewUrl;
        preview.querySelectorAll('[data-preview-object-url]').forEach((image) => URL.revokeObjectURL(image.dataset.previewObjectUrl));
        preview.replaceChildren();
        preview.hidden = !file && row.dataset.hasExistingAttachment !== 'true';
        if (preview.hidden) { label.textContent = 'Bukti nota wajib · PDF atau gambar, maksimal 10 MB.'; return; }
        if (file) preview.dataset.filePreviewUrl = URL.createObjectURL(file);
        else if (preview.dataset.existingPreviewUrl) preview.dataset.filePreviewUrl = preview.dataset.existingPreviewUrl;
        const addImage = (src, objectUrl = false) => { const image = document.createElement('img'); image.className = 'claim-attachment-thumbnail'; image.alt = 'Pratinjau bukti nota'; image.src = src; if (objectUrl) image.dataset.previewObjectUrl = src; preview.append(image); };
        if (file?.type?.startsWith('image/')) addImage(preview.dataset.filePreviewUrl);
        else if (!file && preview.dataset.existingPreviewUrl) {
            fetch(preview.dataset.existingPreviewUrl, { credentials: 'same-origin' }).then((response) => response.ok ? response.blob() : null).then((blob) => { if (!blob || !preview.isConnected || row.dataset.hasExistingAttachment !== 'true') return; if (blob.type.startsWith('image/')) addImage(URL.createObjectURL(blob), true); }).catch(() => {});
        }
        const detail = document.createElement('span'); detail.className = 'claim-attachment-file'; detail.innerHTML = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 3.75h8l4 4V20a.25.25 0 0 1-.25.25h-11.5A.25.25 0 0 1 6 20V4a.25.25 0 0 1 .25-.25Z" stroke="currentColor" stroke-width="1.6"/><path d="M14 4v4h4M9 13h6M9 16h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg><span></span>';
        detail.querySelector('span').textContent = file?.name || 'Bukti nota tersimpan'; preview.append(detail);
        const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'btn btn-sm btn-outline-danger'; remove.dataset.removeClaimAttachment = ''; remove.setAttribute('aria-label', 'Hapus lampiran'); remove.title = 'Hapus lampiran'; remove.innerHTML = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4.5 7h15M9 7V4.5h6V7m-8.5 0 .8 13h9.4l.8-13M10 10.5v6m4-6v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>'; preview.append(remove);
        const showPreview = document.createElement('button'); showPreview.type = 'button'; showPreview.className = 'btn btn-sm btn-outline-primary'; showPreview.dataset.previewClaimAttachment = ''; showPreview.setAttribute('aria-label', 'Preview bukti nota'); showPreview.title = 'Preview'; showPreview.innerHTML = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/></svg>'; preview.append(showPreview);
        if (file) label.textContent = `${file.name} · siap dilampirkan`;
        else if (label.textContent.includes('wajib') || !label.textContent) label.textContent = 'Bukti nota sudah terlampir.';
    };
    list.querySelectorAll('.claim-item').forEach((row) => { if (row.dataset.hasExistingAttachment === 'true') renderAttachment(row); });
    cameraModal?.addEventListener('hidden.bs.modal', stopCamera);
    attachmentPreviewModal?.addEventListener('hidden.bs.modal', () => { if (attachmentPreviewFrame) attachmentPreviewFrame.src = 'about:blank'; });
    cameraModal?.querySelector('[data-take-claim-photo]')?.addEventListener('click', () => {
        if (!cameraVideo?.videoWidth || !activeCameraInput) return;
        const canvas = document.createElement('canvas'); canvas.width = cameraVideo.videoWidth; canvas.height = cameraVideo.videoHeight;
        canvas.getContext('2d')?.drawImage(cameraVideo, 0, 0, canvas.width, canvas.height);
        canvas.toBlob((blob) => {
            if (!blob) { cameraError.hidden = false; cameraError.textContent = 'Foto tidak dapat diproses. Silakan coba lagi.'; return; }
            const file = new File([blob], `bukti-nota-${Date.now()}.jpg`, { type: 'image/jpeg' });
            try { const transfer = new DataTransfer(); transfer.items.add(file); activeCameraInput.files = transfer.files; activeCameraInput.dispatchEvent(new Event('change', { bubbles: true })); bootstrap.Modal.getOrCreateInstance(cameraModal).hide(); }
            catch { cameraError.hidden = false; cameraError.textContent = 'Browser belum mendukung pengambilan foto langsung. Gunakan tombol pilih file.'; }
        }, 'image/jpeg', .88);
    });
    form.addEventListener('click', async (event) => {
        const row = event.target.closest('.claim-item');
        const input = row?.querySelector('[data-claim-attachment-input]');
        if (!input) return;
        if (event.target.closest('[data-choose-claim-file]')) { input.removeAttribute('capture'); input.click(); }
        if (event.target.closest('[data-capture-claim-photo]')) {
            activeCameraInput = input;
            if (!window.isSecureContext || !navigator.mediaDevices?.getUserMedia) { input.setAttribute('capture', 'environment'); input.click(); return; }
            cameraError.hidden = true; cameraError.textContent = ''; cameraModal.querySelector('[data-take-claim-photo]').disabled = true;
            bootstrap.Modal.getOrCreateInstance(cameraModal).show();
            try { if (!navigator.mediaDevices?.getUserMedia) throw new Error('Browser tidak menyediakan akses kamera.'); cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false }); cameraVideo.srcObject = cameraStream; await cameraVideo.play(); cameraModal.querySelector('[data-take-claim-photo]').disabled = false; }
            catch (error) { cameraError.hidden = false; cameraError.textContent = error.name === 'NotAllowedError' ? 'Izin kamera ditolak. Izinkan akses kamera pada browser, atau gunakan pilih file.' : `${error.message || 'Kamera tidak dapat diakses.'} Anda tetap dapat menggunakan pilih file.`; }
        }
        if (event.target.closest('[data-remove-claim-attachment]')) {
            const preview = row.querySelector('[data-claim-attachment-preview]'); if (preview.dataset.filePreviewUrl?.startsWith('blob:')) URL.revokeObjectURL(preview.dataset.filePreviewUrl); delete preview.dataset.filePreviewUrl; preview.querySelectorAll('[data-preview-object-url]').forEach((image) => URL.revokeObjectURL(image.dataset.previewObjectUrl)); preview.replaceChildren(); preview.hidden = true;
            input.value = ''; row.querySelector('[data-claim-attachment-token]').value = ''; row.querySelector('[data-claim-remove-marker]').value = '1'; row.dataset.hasExistingAttachment = 'false'; row.querySelector('[data-claim-attachment-name]').textContent = 'Bukti nota wajib · PDF atau gambar, maksimal 10 MB.';
        }
        if (event.target.closest('[data-preview-claim-attachment]')) {
            const previewUrl = row.querySelector('[data-claim-attachment-preview]')?.dataset.filePreviewUrl;
            if (!previewUrl || !attachmentPreviewFrame) return;
            const getValue = (suffix) => row.querySelector(`[name$="[${suffix}]"]`)?.value?.trim() || '—';
            const category = row.querySelector('[name$="[expense_category_id]"]');
            const values = [
                ['Tanggal nota', getValue('receipt_date')],
                ['Tanggal kegiatan', getValue('activity_date')],
                ['Kategori biaya', category?.selectedOptions?.[0]?.textContent?.trim() || '—'],
                ['Nominal', `${getValue('currency')} ${getValue('amount')}`],
                ['Referensi nota', getValue('attachment_reference')],
                ['Deskripsi', getValue('description')],
                ['Nama file', row.querySelector('[data-claim-attachment-input]')?.files?.[0]?.name || 'Bukti nota tersimpan'],
            ];
            attachmentPreviewDetails?.replaceChildren(...values.flatMap(([label, value]) => {
                const term = document.createElement('dt'); term.className = 'claim-verification-label'; term.textContent = label;
                const description = document.createElement('dd'); description.className = 'claim-verification-value'; description.textContent = value;
                return [term, description];
            }));
            attachmentPreviewFrame.src = previewUrl;
            bootstrap.Modal.getOrCreateInstance(attachmentPreviewModal).show();
        }
    });
    form.addEventListener('change', (event) => {
        if (!event.target.matches('[data-claim-attachment-input]')) return;
        const row = event.target.closest('.claim-item');
        const input = event.target;
        const file = input.files?.[0];
        row.querySelector('[data-claim-attachment-token]').value = '';
        row.dataset.hasExistingAttachment = 'false';
        row.querySelector('[data-claim-remove-marker]').value = '0';
        row.querySelectorAll('.client-validation-error').forEach((error) => error.remove());
        if (!file) { renderAttachment(row); return; }

        const preparationId = Number(row.dataset.attachmentPreparationId || 0) + 1;
        row.dataset.attachmentPreparationId = String(preparationId);
        row.dataset.attachmentProcessing = 'true';
        row.querySelector('[data-claim-attachment-name]').textContent = file.type.startsWith('image/') ? 'Mengoptimalkan ukuran gambar…' : 'Bukti nota siap diunggah.';
        row.attachmentPreparation = compressClaimImage(file).then((preparedFile) => {
            if (row.dataset.attachmentPreparationId !== String(preparationId) || input.files?.[0] !== file) return;
            if (preparedFile !== file) {
                try { const transfer = new DataTransfer(); transfer.items.add(preparedFile); input.files = transfer.files; }
                catch { row.querySelector('[data-claim-attachment-name]').textContent = 'Gambar tidak dapat diganti dengan versi hasil kompresi; file asli akan digunakan.'; renderAttachment(row, file); return; }
            }
            renderAttachment(row, preparedFile);
            row.querySelector('[data-claim-attachment-name]').textContent = preparedFile !== file
                ? `${preparedFile.name} · ${formatFileSize(file.size)} → ${formatFileSize(preparedFile.size)}`
                : (file.type.startsWith('image/') ? `${file.name} · ukuran asli ${formatFileSize(file.size)}` : `${file.name} · siap dilampirkan`);
        }).catch(() => {
            if (row.dataset.attachmentPreparationId !== String(preparationId)) return;
            renderAttachment(row, file);
            row.querySelector('[data-claim-attachment-name]').textContent = `${file.name} · gambar tidak dapat dikompres browser, file asli akan digunakan`;
        }).finally(() => {
            if (row.dataset.attachmentPreparationId === String(preparationId)) delete row.dataset.attachmentProcessing;
        });
    });
    const uploadAndSubmit = async (submitter, submit) => {
        await Promise.all([...list.querySelectorAll('.claim-item')].map((row) => row.attachmentPreparation).filter(Boolean));
        const pending = [...list.querySelectorAll('.claim-item')].map((row) => ({
            row,
            file: row.querySelector('[data-claim-attachment-input]')?.files?.[0],
            tokenField: row.querySelector('[data-claim-attachment-token]'),
        })).filter((item) => item.file && !item.tokenField?.value);
        if (!pending.length) { submit(); return; }
        const buttons = [...form.querySelectorAll('button[type="submit"]')];
        const attachmentControls = [...form.querySelectorAll('#add-claim-item, .remove-claim-item, [data-choose-claim-file], [data-capture-claim-photo]')];
        window.dispatchEvent(new CustomEvent('workspace:show-loading', { detail: submitter?.value === 'draft' ? 'Menyimpan draft...' : 'Mengirim pengajuan...' }));
        buttons.forEach((button) => { button.disabled = true; });
        attachmentControls.forEach((button) => { button.disabled = true; });
        const token = form.querySelector('[name="_token"]')?.value || '';
        try {
            for (let index = 0; index < pending.length; index++) {
                const item = pending[index];
                const label = item.row.querySelector('[data-claim-attachment-name]');
                label.textContent = `Mengunggah bukti ${index + 1} dari ${pending.length}…`;
                const body = new FormData();
                body.append('file', item.file);
                const response = await fetch(form.dataset.attachmentUploadUrl, { method: 'POST', body, headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' }, credentials: 'same-origin' });
                const result = await response.json().catch(() => ({}));
                if (!response.ok || !result.token) throw new Error(result.message || Object.values(result.errors || {}).flat()[0] || 'Bukti nota gagal diunggah. Coba kembali.');
                item.tokenField.value = result.token;
                item.row.dataset.hasExistingAttachment = 'true';
                label.textContent = `${result.name} · siap dikirim`;
            }
            submit();
        } catch (error) {
            window.dispatchEvent(new CustomEvent('workspace:hide-loading'));
            const alert = document.createElement('div');
            alert.className = 'invalid-feedback d-block client-validation-error mt-2';
            alert.textContent = error.message;
            pending.find((item) => !item.tokenField?.value)?.row.querySelector('.claim-attachment-field')?.append(alert);
            delete form.dataset.confirmHandled;
            form.querySelector('[data-workspace-confirm-value]')?.remove();
            buttons.forEach((button) => { button.disabled = false; });
            attachmentControls.forEach((button) => { button.disabled = false; });
        }
    };
    form.addEventListener('submit', (event) => {
        if (form.dataset.claimUploadComplete === 'true') return;
        const hasPendingFile = [...list.querySelectorAll('.claim-item')].some((row) => row.querySelector('[data-claim-attachment-input]')?.files?.[0] && !row.querySelector('[data-claim-attachment-token]')?.value);
        if (!hasPendingFile) return;
        event.preventDefault();
        const submitter = event.submitter;
        uploadAndSubmit(submitter, () => {
            form.querySelectorAll('[data-claim-submit-action]').forEach((field) => field.remove());
            if (submitter?.name && submitter.value) { const field = document.createElement('input'); field.type = 'hidden'; field.name = submitter.name; field.value = submitter.value; field.dataset.claimSubmitAction = 'true'; form.append(field); }
            form.dataset.claimUploadComplete = 'true';
            form.submit();
        });
    });
    form.addEventListener('workspace:before-confirmed-submit', (event) => {
        if (form.dataset.claimUploadComplete === 'true') return;
        const hasPendingFile = [...list.querySelectorAll('.claim-item')].some((row) => row.querySelector('[data-claim-attachment-input]')?.files?.[0] && !row.querySelector('[data-claim-attachment-token]')?.value);
        if (!hasPendingFile) return;
        event.preventDefault();
        uploadAndSubmit(event.detail.submitter, event.detail.submit);
    });
    refresh();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('claim-form');
    if (!form) return;

    const makeReferenceDropdown = (select, label) => {
        if (!select || select.dataset.dropdownReady) return;
        const wrapper = document.createElement('div');
        wrapper.className = 'dropdown w-100';
        wrapper.dataset.opportunityTablePicker = '1';
        wrapper.innerHTML = `<button type="button" class="form-select text-start d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span>${label}</span><span class="small text-secondary">▾</span></button><div class="dropdown-menu claim-reference-menu p-2"><div class="claim-reference-search"><span class="claim-reference-search-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><input type="search" class="form-control" placeholder="Cari referensi, perusahaan, client, atau status..." aria-label="Cari ${label.toLowerCase()}"></div><div class="table-responsive claim-reference-table-scroll"><table class="table table-sm align-middle mb-0"><thead><tr><th>Referensi</th><th>Perusahaan pemilik</th><th>Client</th><th>Status</th><th class="text-end">Pilih</th></tr></thead><tbody></tbody></table></div></div>`;
        const buttonLabel = wrapper.querySelector('button span');
        const body = wrapper.querySelector('tbody');
        const summary = document.createElement('div');
        summary.className = 'alert alert-primary-subtle border-primary-subtle mt-3 mb-3';
        summary.hidden = true;
        summary.innerHTML = '<div class="small text-secondary mb-1">Referensi terpilih</div><div class="fw-semibold mb-2" data-reference-summary-name>—</div><div class="d-flex flex-wrap gap-1 mb-2" data-reference-summary-badges></div><div class="small" data-reference-summary-client>Client: —</div><div class="small" data-reference-summary-contact>Kontak: —</div>';
        const renderSummary = (option) => {
            if (!option?.value) { summary.hidden = true; return; }
            const details = referenceDetails[String(option.value)] || { company: '—', client: '—', status: 'Aktif' };
            summary.hidden = false;
            summary.querySelector('[data-reference-summary-name]').textContent = option.textContent;
            summary.querySelector('[data-reference-summary-badges]').innerHTML = details.kind === 'opportunity' ? `<span class="badge rounded-pill text-bg-${details.classification === 'Sales' ? 'success' : details.classification === 'Dibatalkan' ? 'danger' : details.classification === 'Lost' ? 'warning text-dark' : details.classification === 'Diabaikan' ? 'secondary' : 'primary'}">${details.classification}</span><span class="badge rounded-pill text-bg-light">Stage: ${details.stage}</span><span class="badge rounded-pill text-bg-secondary">Closing: ${details.closing}</span>` : `<span class="badge rounded-pill text-bg-primary">${details.status}</span><span class="badge rounded-pill text-bg-light">Project</span>`;
            summary.querySelector('[data-reference-summary-client]').textContent = `Client: ${details.client}`;
            summary.querySelector('[data-reference-summary-contact]').textContent = `Kontak: ${details.contact || '—'}`;
        };
        [...select.options].forEach((option) => {
            if (!option.value) return;
            const row = document.createElement('tr');
            row.className = 'reference-choice-row';
            const details = referenceDetails[String(option.value)] || { company: '—', client: '—', status: 'Aktif' };
            row.innerHTML = `<td class="fw-semibold"></td><td class="text-secondary"></td><td class="text-secondary"></td><td><span class="badge rounded-pill text-bg-primary"></span></td>`;
            row.children[0].textContent = option.textContent;
            row.children[1].textContent = details.company;
            row.children[2].textContent = details.client;
            row.children[3].querySelector('.badge').textContent = details.status;
            row.addEventListener('click', () => {
                select.value = option.value;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                buttonLabel.textContent = option.textContent;
                renderSummary(option);
                bootstrap.Dropdown.getOrCreateInstance(wrapper.querySelector('button')).hide();
            });
            row.dataset.search = [option.textContent, details.company, details.client, details.status].join(' ').toLowerCase();
            body.append(row);
        });
        wrapper.querySelector('input[type="search"]').addEventListener('input', (event) => { const query = event.target.value.toLowerCase().trim(); body.querySelectorAll('tr').forEach((row) => { row.hidden = query !== '' && !row.dataset.search.includes(query); }); });
        select.classList.add('d-none');
        select.parentElement.insertBefore(wrapper, select);
        wrapper.parentElement.insertBefore(summary, select);
        const selected = select.options[select.selectedIndex];
        if (selected?.value) { buttonLabel.textContent = selected.textContent; renderSummary(selected); }
        select.dataset.dropdownReady = '1';
    };
    makeReferenceDropdown(document.querySelector('#claim-project-field select'), 'Pilih project');
    makeReferenceDropdown(document.querySelector('#claim-opportunity-field select'), 'Pilih opportunity');
    const ensureReferenceDropdowns = () => {
        makeReferenceDropdown(document.querySelector('#claim-project-field select'), 'Pilih project');
        makeReferenceDropdown(document.querySelector('#claim-opportunity-field select'), 'Pilih opportunity');
    };
    form.querySelectorAll('[name="work_reference_type"], [name="expense_scope"]').forEach((input) => input.addEventListener('change', () => window.setTimeout(ensureReferenceDropdowns, 0)));
    window.setTimeout(ensureReferenceDropdowns, 0);
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const select = document.querySelector('#claim-opportunity-field select');
    if (!select || select.parentElement.querySelector('[data-claim-opportunity-dropdown]') || select.previousElementSibling?.classList.contains('dropdown')) return;
    const wrapper = document.createElement('div');
    wrapper.className = 'dropdown w-100';
    wrapper.dataset.claimOpportunityDropdown = '1';
    wrapper.innerHTML = '<button type="button" class="form-select text-start d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside"><span>Pilih opportunity</span><span class="small text-secondary">▾</span></button><div class="dropdown-menu p-2" style="min-width:min(820px, calc(100vw - 3rem)); max-height:420px; overflow-y:auto"><div class="input-group mb-2" style="display:flex!important; flex-wrap:nowrap!important"><span class="input-group-text">⌕</span><input type="search" class="form-control" placeholder="Cari opportunity, status, client, atau kontak..."></div><div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>OPPORTUNITY</th><th>PERUSAHAAN PEMILIK</th><th>CLIENT</th><th>STATUS</th><th class="text-end">PILIH</th></tr></thead><tbody></tbody></table></div></div>';
    const body = wrapper.querySelector('tbody');
    const buttonLabel = wrapper.querySelector('button span');
    [...select.options].forEach((option) => {
        if (!option.value) return;
        const details = referenceDetails[String(option.value)] || { company: '—', client: '—', status: 'Prospek' };
        const row = document.createElement('tr');
        row.innerHTML = '<td class="fw-semibold"></td><td></td><td></td><td><span class="badge rounded-pill text-bg-primary"></span></td>';
        row.children[0].textContent = option.textContent;
        row.children[1].textContent = details.company;
        row.children[2].textContent = details.client;
        row.children[3].querySelector('.badge').textContent = details.status;
        row.dataset.search = `${option.textContent} ${details.company} ${details.client} ${details.status}`.toLowerCase();
        row.addEventListener('click', () => { select.value = option.value; select.dispatchEvent(new Event('change', { bubbles: true })); buttonLabel.textContent = option.textContent; bootstrap.Dropdown.getOrCreateInstance(wrapper.querySelector('button')).hide(); });
        body.append(row);
    });
    wrapper.querySelector('input[type="search"]').addEventListener('input', (event) => { const query = event.target.value.toLowerCase().trim(); body.querySelectorAll('tr').forEach((row) => { row.hidden = query !== '' && !row.dataset.search.includes(query); }); });
    select.classList.add('d-none');
    select.parentElement.insertBefore(wrapper, select);
    const selected = select.options[select.selectedIndex];
    if (selected?.value) buttonLabel.textContent = selected.textContent;
});
</script>
<script>
window.addEventListener('load', function () {
    const select = document.querySelector('#claim-opportunity-field select');
    const field = document.getElementById('claim-opportunity-field');
    if (!select || !field || field.querySelector('[data-opportunity-table-picker]')) return;
    const details = @json($referenceDetails);
    const picker = document.createElement('div');
    picker.dataset.opportunityTablePicker = '1';
    picker.className = 'dropdown w-100';
    picker.innerHTML = '<button type="button" class="form-select text-start d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside"><span>Pilih opportunity</span><span>⌄</span></button><div class="dropdown-menu p-2" style="min-width:820px;max-width:calc(100vw - 3rem);max-height:420px;overflow-y:auto"><div class="input-group mb-2" style="display:flex!important;flex-wrap:nowrap!important"><span class="input-group-text">⌕</span><input class="form-control" type="search" placeholder="Cari opportunity, status, client, atau kontak..."></div><div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>OPPORTUNITY</th><th>PERUSAHAAN PEMILIK</th><th>CLIENT</th><th>STATUS</th><th></th></tr></thead><tbody></tbody></table></div></div>';
    const body = picker.querySelector('tbody');
    const label = picker.querySelector('button span');
    Array.from(select.options).forEach(function (option) {
        if (!option.value) return;
        const item = details[String(option.value)] || { company: '—', client: '—', status: 'Prospek' };
        const row = document.createElement('tr');
        row.innerHTML = '<td class="fw-semibold"></td><td></td><td></td><td><span class="badge rounded-pill text-bg-primary"></span></td>';
        row.children[0].textContent = option.textContent;
        row.children[1].textContent = item.company;
        row.children[2].textContent = item.client;
        row.children[3].querySelector('.badge').textContent = item.status;
        row.dataset.search = (option.textContent + ' ' + item.company + ' ' + item.client + ' ' + item.status).toLowerCase();
        row.addEventListener('click', function () { select.value = option.value; label.textContent = option.textContent; select.dispatchEvent(new Event('change', { bubbles: true })); });
        body.appendChild(row);
    });
    picker.querySelector('input[type="search"]').addEventListener('input', function (event) { const query = event.target.value.toLowerCase().trim(); body.querySelectorAll('tr').forEach(function (row) { row.hidden = query !== '' && !row.dataset.search.includes(query); }); });
    select.classList.add('d-none');
    field.insertBefore(picker, select);
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const details = @json($referenceDetails);
    const statusClass = (value) => value === 'Sales' ? 'success' : value === 'Dibatalkan' ? 'danger' : value === 'Lost' ? 'warning text-dark' : value === 'Diabaikan' ? 'secondary' : 'primary';
    const renderOpportunity = () => {
        const field = document.getElementById('claim-opportunity-field');
        const select = field?.querySelector('select');
        if (!field || !select) return;

        field.querySelectorAll('.dropdown, .alert-primary-subtle, [data-reference-summary]').forEach((node) => node.remove());
        field.querySelectorAll('[data-bs-toggle="dropdown"]').forEach((node) => node.closest('.dropdown')?.remove());
        select.classList.remove('d-none');
        select.style.display = 'none';
        delete select.dataset.dropdownReady;

        const wrapper = document.createElement('div');
        wrapper.className = 'dropdown w-100';
        wrapper.dataset.opportunityTablePicker = '1';
        wrapper.innerHTML = '<button type="button" class="form-select text-start d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside"><span>Pilih opportunity</span><span class="small text-secondary">⌄</span></button><div class="dropdown-menu claim-reference-menu p-2"><div class="claim-reference-search"><span class="claim-reference-search-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><input class="form-control" type="search" placeholder="Cari opportunity, status, client, atau kontak..." aria-label="Cari opportunity"></div><div class="table-responsive claim-reference-table-scroll"><table class="table table-sm align-middle mb-0"><thead><tr><th>OPPORTUNITY</th><th>PERUSAHAAN PEMILIK</th><th>CLIENT</th><th>STATUS</th></tr></thead><tbody></tbody></table></div></div>';
        const button = wrapper.querySelector('button');
        const label = wrapper.querySelector('button span');
        const body = wrapper.querySelector('tbody');
        const summary = document.createElement('div');
        summary.dataset.referenceSummary = '1';
        summary.className = 'alert alert-primary-subtle border-primary-subtle mt-3 mb-3';
        summary.hidden = true;
        summary.innerHTML = '<div class="small text-secondary mb-1">Referensi terpilih</div><div class="fw-semibold mb-2" data-summary-name>—</div><div class="d-flex flex-wrap gap-1 mb-2" data-summary-badges></div><div class="small" data-summary-client>Client: —</div><div class="small" data-summary-contact>Kontak: —</div>';
        const updateSummary = (option) => {
            const item = details[String(option.value)] || {};
            summary.hidden = !option.value;
            if (!option.value) return;
            summary.querySelector('[data-summary-name]').textContent = option.textContent;
            summary.querySelector('[data-summary-badges]').innerHTML = `<span class="badge rounded-pill text-bg-${statusClass(item.classification)}">${item.classification || 'Prospek'}</span><span class="badge rounded-pill text-bg-light">Stage: ${item.stage || '—'}</span><span class="badge rounded-pill text-bg-secondary">Closing: ${item.closing || '—'}</span>`;
            summary.querySelector('[data-summary-client]').textContent = `Client: ${item.client || '—'}`;
            summary.querySelector('[data-summary-contact]').textContent = `Kontak: ${item.contact || '—'}`;
        };
        [...select.options].forEach((option) => {
            if (!option.value) return;
            const item = details[String(option.value)] || {};
            const row = document.createElement('tr');
            row.className = 'reference-choice-row';
            row.innerHTML = '<td class="fw-semibold"></td><td class="text-secondary"></td><td class="text-secondary"></td><td></td>';
            row.children[0].textContent = option.textContent;
            row.children[1].textContent = item.company || '—';
            row.children[2].textContent = item.client || '—';
            row.children[3].innerHTML = `<span class="badge rounded-pill text-bg-${statusClass(item.classification)}">${item.classification || 'Prospek'}</span><span class="badge rounded-pill text-bg-light">Stage: ${item.stage || '—'}</span><span class="badge rounded-pill text-bg-secondary">Closing: ${item.closing || '—'}</span>`;
            row.dataset.search = [option.textContent, item.company, item.client, item.contact, item.classification, item.stage, item.closing].join(' ').toLowerCase();
            row.addEventListener('click', () => { select.value = option.value; label.textContent = option.textContent; updateSummary(option); select.dispatchEvent(new Event('change', { bubbles: true })); if (window.bootstrap) bootstrap.Dropdown.getOrCreateInstance(button).hide(); });
            body.append(row);
        });
        wrapper.querySelector('input').addEventListener('input', (event) => { const query = event.target.value.toLowerCase().trim(); body.querySelectorAll('tr').forEach((row) => { row.hidden = query !== '' && !row.dataset.search.includes(query); }); });
        select.classList.add('d-none');
        select.style.display = 'none';
        field.insertBefore(wrapper, select);
        field.insertBefore(summary, select);
        const selected = select.options[select.selectedIndex];
        if (selected?.value) { label.textContent = selected.textContent; updateSummary(selected); }
        select.dataset.dropdownReady = '1';
    };
    renderOpportunity();
});
</script>
@endsection
