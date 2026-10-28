@extends('layouts.app', ['title' => 'Ajukan Cuti atau Sakit'])
@section('content')
@php($dateRows = old('dates', ['']))
@php($hasHrApprover = $hrApprovers->isNotEmpty())
<div class="mb-4"><div class="text-uppercase text-secondary small fw-semibold mb-2">Kehadiran</div><h1 class="h3 mb-1">Ajukan cuti atau sakit</h1><p class="text-secondary mb-0">Lengkapi data pengajuan. Tanggal yang beririsan dengan pengajuan aktif akan ditolak.</p></div>
<form method="POST" action="{{ route('workspace.hris.leave-requests.store') }}" enctype="multipart/form-data" class="card card-body workspace-leave-form workspace-leave-form-columns" data-leave-form data-remaining-leave="{{ max(0, $quota - $usedLeave) }}" novalidate>
@csrf
<div class="workspace-leave-column">
<section class="workspace-date-panel">
    <div class="workspace-leave-quota" data-quota-panel><div><span>Kuota cuti {{ $year }}</span><strong>{{ $quota }} hari</strong></div><div><span>Sudah disetujui</span><strong>{{ $usedLeave }} hari</strong></div><div><span>Sisa cuti</span><strong>{{ max(0, $quota - $usedLeave) }} hari</strong></div><div><span>Sakit tercatat</span><strong>{{ $sickDays }} hari</strong></div></div>
    <div class="workspace-leave-section-heading workspace-date-heading"><h2>Tanggal pengajuan <span class="text-danger">*</span></h2><button type="button" class="btn btn-outline-primary btn-sm" data-add-leave-date>+ Tambah tanggal</button></div>
    <div class="workspace-leave-dates" data-date-list>@foreach($dateRows as $index => $date)<div class="workspace-leave-date-row" data-date-row><span class="workspace-leave-date-index">{{ $index + 1 }}</span><input type="date" name="dates[]" class="form-control" value="{{ $date }}" aria-label="Tanggal pengajuan {{ $index + 1 }}"><button type="button" class="btn btn-outline-secondary btn-sm" data-remove-leave-date>Hapus</button></div>@endforeach</div>
    @error('dates')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    <div class="invalid-feedback d-block" data-date-error hidden></div>
    <div class="form-text">Tanggal dapat diisi mundur. Untuk cuti, jumlah tanggal tidak boleh melebihi sisa kuota.</div>
</section>
<section class="workspace-leave-section workspace-leave-reason-section"><div class="workspace-leave-section-heading"><h2>Alasan <span class="text-danger">*</span></h2></div><textarea name="reason" class="form-control" rows="4" maxlength="2000" placeholder="Tuliskan alasan pengajuan..." aria-describedby="reason-error">{{ old('reason') }}</textarea><div class="invalid-feedback d-block" id="reason-error" data-reason-error hidden></div></section>
</div>
<div class="workspace-leave-column">
<section class="workspace-leave-section workspace-leave-type-section"><div class="workspace-leave-section-heading"><h2>Jenis pengajuan <span class="text-danger">*</span></h2></div><div class="workspace-leave-type-grid"><label class="workspace-choice-card"><input type="radio" name="type" value="leave" @checked(old('type', 'leave') === 'leave') data-leave-type><span><strong>Cuti</strong><small>Menggunakan kuota cuti tahunan.</small></span></label><label class="workspace-choice-card"><input type="radio" name="type" value="sick" @checked(old('type') === 'sick') data-leave-type><span><strong>Sakit</strong><small>Tidak mengurangi kuota, wajib melampirkan surat.</small></span></label></div></section>
<section class="workspace-leave-section workspace-leave-document-section"><div class="workspace-leave-section-heading"><h2>Dokumen pendukung <span class="text-danger {{ old('type', 'leave') === 'sick' ? '' : 'd-none' }}" data-supporting-required>*</span></h2></div><input type="file" name="supporting_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" data-supporting-document aria-describedby="supporting-document-error"><div class="form-text">Wajib untuk sakit. PDF/JPG/PNG, maksimal 10 MB.</div><div class="invalid-feedback d-block" id="supporting-document-error" data-document-error hidden></div></section>
<section class="workspace-leave-section workspace-leave-employee-section"><div class="workspace-leave-section-heading"><h2>Informasi karyawan</h2></div><div class="workspace-employee-summary"><div><span>Nama</span><strong>{{ $employee->full_name }}</strong></div><div><span>NIP</span><strong>{{ $employee->employee_number }}</strong></div><div><span>Jabatan</span><strong>{{ $employee->job_title ?? 'Belum diatur' }}</strong></div><div><span>Perusahaan</span><strong>{{ $employee->company?->name ?? '-' }}</strong></div></div></section>
<section class="workspace-leave-section workspace-leave-manager-section"><div class="workspace-leave-section-heading"><h2>Informasi atasan</h2></div><div class="workspace-approval-panel">@if($employee->manager)<strong>{{ $employee->manager->full_name }}</strong><span>{{ $employee->manager->job_title ?? 'Atasan langsung' }} · {{ $employee->manager->employee_number }}</span>@else<span class="text-danger">Atasan langsung belum dikonfigurasi.</span>@endif</div></section>
<section class="workspace-leave-section workspace-leave-hr-section"><div class="workspace-leave-section-heading"><h2>Informasi HR</h2></div><div class="workspace-approval-panel {{ $hasHrApprover ? '' : 'is-missing' }}">@forelse($hrApprovers as $approver)<div class="workspace-approval-person"><span class="workspace-approval-avatar">{{ strtoupper(substr($approver->user?->name ?? '?', 0, 1)) }}</span><span><strong>{{ $approver->user?->name }}</strong><small>{{ $approver->user?->email }}</small></span></div>@empty<span class="text-danger">Approver HR belum dikonfigurasi untuk perusahaan ini.</span>@endforelse</div></section>
</div>
<div class="workspace-leave-actions d-flex justify-content-end gap-2"><a href="{{ route('workspace.hris.leave-requests') }}" class="btn btn-light btn-sm">Batal</a><button class="btn btn-primary btn-sm" type="submit" @disabled(!$hasHrApprover)>Kirim pengajuan</button></div>
</form>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-leave-form]');
    if (!form) return;

    const list = form.querySelector('[data-date-list]');
    const addButton = form.querySelector('[data-add-leave-date]');
    const typeInputs = [...form.querySelectorAll('[data-leave-type]')];
    const documentInput = form.querySelector('[data-supporting-document]');
    const reasonInput = form.querySelector('[name="reason"]');
    const dateError = form.querySelector('[data-date-error]');
    const documentError = form.querySelector('[data-document-error]');
    const reasonError = form.querySelector('[data-reason-error]');
    const remaining = Number(form.dataset.remainingLeave || 0);

    const setError = (input, message) => {
        input?.classList.toggle('is-invalid', Boolean(message));
        input?.setAttribute('aria-invalid', message ? 'true' : 'false');
    };
    const setMessage = (element, message) => {
        element.textContent = message || '';
        element.hidden = !message;
        element.classList.toggle('d-block', Boolean(message));
    };
    const selectedType = () => form.querySelector('[data-leave-type]:checked')?.value;
    const dateInputs = () => [...list.querySelectorAll('input[data-workspace-date-enhanced]')];

    const refresh = () => {
        dateInputs().forEach((input, index) => {
            const row = input.closest('[data-date-row]');
            row.querySelector('.workspace-leave-date-index').textContent = index + 1;
            row.querySelector('[data-remove-leave-date]').disabled = list.children.length === 1;
            input.setAttribute('aria-label', `Tanggal pengajuan ${index + 1}`);
        });
        const sick = selectedType() === 'sick';
        form.querySelector('[data-supporting-required]').classList.toggle('d-none', !sick);
        form.querySelector('[data-quota-panel]').classList.toggle('opacity-50', sick);
        addButton.disabled = !sick && (remaining === 0 || list.children.length >= remaining);
    };

    const validate = () => {
        let firstInvalid = null;
        const fail = (input, message, messageElement = null) => {
            setError(input, message);
            if (messageElement) setMessage(messageElement, message);
            if (!firstInvalid) firstInvalid = input;
        };
        const dates = dateInputs();
        const values = dates.map((input) => input.value).filter(Boolean);
        const type = selectedType();
        const reason = reasonInput.value.trim();

        dates.forEach((input) => setError(input, ''));
        setMessage(dateError, '');
        setError(documentInput, '');
        setMessage(documentError, '');
        setError(reasonInput, '');
        setMessage(reasonError, '');

        if (!type) fail(typeInputs[0], 'Pilih jenis pengajuan.');
        const emptyDate = dates.find((input) => !input.value);
        if (emptyDate) {
            fail(emptyDate, 'Semua baris tanggal wajib diisi.', dateError);
        } else if (values.length === 0) {
            fail(dates[0], 'Pilih minimal satu tanggal pengajuan.', dateError);
        } else if (values.length !== new Set(values).size) {
            const seen = new Set();
            const duplicate = dates.find((input) => seen.has(input.value) || !seen.add(input.value));
            fail(duplicate || dates[0], 'Tanggal pengajuan tidak boleh duplikat.', dateError);
        } else if (type === 'leave' && values.length > remaining) {
            fail(dates[remaining] || dates[0], `Jumlah tanggal tidak boleh melebihi sisa kuota (${remaining} hari).`, dateError);
        }

        if (!reason) fail(reasonInput, 'Alasan pengajuan wajib diisi.', reasonError);
        if (reason.length > 2000) fail(reasonInput, 'Alasan maksimal 2.000 karakter.', reasonError);

        const file = documentInput.files[0];
        if (type === 'sick' && !file) fail(documentInput, 'Surat pendukung wajib diunggah untuk pengajuan sakit.', documentError);
        if (file && (!/\.(pdf|jpe?g|png)$/i.test(file.name) || file.size > 10 * 1024 * 1024)) {
            fail(documentInput, 'Dokumen harus PDF/JPG/PNG dan maksimal 10 MB.', documentError);
        }
        return firstInvalid;
    };

    addButton.addEventListener('click', () => {
        if (addButton.disabled) return;
        const row = list.querySelector('[data-date-row]').cloneNode(true);
        row.querySelector('input[data-workspace-date-enhanced]').value = '';
        row.querySelector('input[data-workspace-date-enhanced]').classList.remove('is-invalid');
        row.querySelector('input[data-workspace-date-value]').value = '';
        list.append(row);
        refresh();
    });
    list.addEventListener('click', (event) => {
        if (event.target.matches('[data-remove-leave-date]') && list.children.length > 1) {
            event.target.closest('[data-date-row]').remove();
            refresh();
        }
    });
    typeInputs.forEach((input) => input.addEventListener('change', () => {
        refresh();
        validate();
    }));
    form.addEventListener('input', (event) => {
        if (event.target.matches('input[data-workspace-date-enhanced], [name="reason"], [data-supporting-document]')) validate();
    });
    form.addEventListener('submit', (event) => {
        const firstInvalid = validate();
        if (firstInvalid) {
            event.preventDefault();
            firstInvalid.focus({ preventScroll: true });
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
    refresh();
});
</script>
@endsection
