@extends('layouts.app', ['title' => 'Ajukan Cuti atau Sakit'])

@section('content')
@php($dateRows = old('dates', ['']))
@php($hasHrApprover = $hrApprovers->isNotEmpty())
<div class="mb-4">
    <div class="text-uppercase text-secondary small fw-semibold mb-2">Kehadiran</div>
    <h1 class="h3 mb-1">Ajukan cuti atau sakit</h1>
    <p class="text-secondary mb-0">Lengkapi data pengajuan secara berurutan. Tanggal yang beririsan dengan pengajuan aktif akan ditolak.</p>
</div>

@if($errors->any())
    <div class="alert alert-danger workspace-alert mb-3">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('workspace.hris.leave-requests.store') }}" enctype="multipart/form-data" class="card card-body workspace-leave-form" data-leave-form data-remaining-leave="{{ max(0, $quota - $usedLeave) }}" data-has-hr="{{ $hasHrApprover ? '1' : '0' }}">
    @csrf
    <section class="workspace-leave-section workspace-date-panel">
        <div class="workspace-leave-section-heading">
            <div><span class="workspace-step-number">1</span><div><h2>Tanggal pengajuan <span class="text-danger">*</span></h2><p>Pilih satu atau beberapa tanggal yang akan diajukan.</p></div></div>
            <button type="button" class="btn btn-outline-primary btn-sm" data-add-leave-date>+ Tambah tanggal</button>
        </div>
        <div class="workspace-leave-quota" data-quota-panel>
            <div><span>Kuota cuti {{ $year }}</span><strong>{{ $quota }} hari</strong></div>
            <div><span>Sudah disetujui</span><strong>{{ $usedLeave }} hari</strong></div>
            <div><span>Sisa cuti</span><strong data-remaining-label>{{ max(0, $quota - $usedLeave) }} hari</strong></div>
            <div><span>Sakit tercatat</span><strong>{{ $sickDays }} hari</strong></div>
        </div>
        <div class="workspace-leave-dates" data-date-list>
            @foreach($dateRows as $index => $date)
                <div class="workspace-leave-date-row" data-date-row>
                    <span class="workspace-leave-date-index">{{ $index + 1 }}</span>
                    <input type="date" name="dates[]" class="form-control" value="{{ $date }}" required>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-remove-leave-date>Hapus</button>
                </div>
            @endforeach
        </div>
        <div class="form-text">Tanggal dapat diisi mundur (back date). Untuk cuti, jumlah tanggal tidak boleh melebihi sisa kuota.</div>
    </section>

    <section class="workspace-leave-section workspace-leave-type-section">
        <div class="workspace-leave-section-heading"><div><span class="workspace-step-number">2</span><div><h2>Jenis pengajuan</h2><p>Tentukan apakah pengajuan ini cuti atau sakit.</p></div></div></div>
        <div class="workspace-leave-type-grid">
            <label class="workspace-choice-card"><input type="radio" name="type" value="leave" @checked(old('type', 'leave') === 'leave') data-leave-type><span><strong>Cuti</strong><small>Menggunakan kuota cuti tahunan.</small></span></label>
            <label class="workspace-choice-card"><input type="radio" name="type" value="sick" @checked(old('type') === 'sick') data-leave-type><span><strong>Sakit</strong><small>Tidak mengurangi kuota, tetapi wajib melampirkan surat.</small></span></label>
        </div>
    </section>

    <section class="workspace-leave-section workspace-leave-document-section">
        <div class="workspace-leave-section-heading"><div><span class="workspace-step-number">3</span><div><h2>Dokumen pendukung</h2><p>Wajib diunggah jika jenis pengajuan adalah sakit.</p></div></div></div>
        <input id="supporting-document" type="file" name="supporting_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" data-supporting-document>
        <div class="form-text">Format PDF/JPG/PNG, maksimal 10 MB.</div>
    </section>

    <section class="workspace-leave-section workspace-leave-reason-section">
        <div class="workspace-leave-section-heading"><div><span class="workspace-step-number">4</span><div><h2>Alasan</h2><p>Tambahkan keterangan jika diperlukan.</p></div></div></div>
        <textarea name="reason" class="form-control" rows="4" maxlength="2000" placeholder="Tuliskan alasan pengajuan...">{{ old('reason') }}</textarea>
    </section>

    <section class="workspace-leave-section workspace-leave-manager-section">
        <div class="workspace-leave-section-heading"><div><span class="workspace-step-number">5</span><div><h2>Informasi atasan</h2><p>Pengajuan akan diverifikasi terlebih dahulu oleh atasan langsung.</p></div></div></div>
        <div class="workspace-approval-panel">
            @if($employee->manager)
                <strong>{{ $employee->manager->full_name }}</strong><span>{{ $employee->manager->job_title ?? 'Atasan langsung' }} · {{ $employee->manager->employee_number }}</span>
            @else
                <span class="text-danger">Atasan langsung belum dikonfigurasi.</span>
            @endif
        </div>
    </section>

    <section class="workspace-leave-section workspace-leave-hr-section">
        <div class="workspace-leave-section-heading"><div><span class="workspace-step-number">6</span><div><h2>Informasi HR</h2><p>Approver HR memproses pengajuan setelah disetujui atasan.</p></div></div></div>
        <div class="workspace-approval-panel {{ $hasHrApprover ? '' : 'is-missing' }}">
            @forelse($hrApprovers as $approver)
                <div class="workspace-approval-person"><span class="workspace-approval-avatar">{{ strtoupper(substr($approver->user?->name ?? '?', 0, 1)) }}</span><span><strong>{{ $approver->user?->name ?? 'Pengguna tidak ditemukan' }}</strong><small>{{ $approver->user?->email }}</small></span></div>
            @empty
                <span class="text-danger">Approver HR belum dikonfigurasi untuk perusahaan ini.</span>
            @endforelse
        </div>
    </section>

    <div class="workspace-leave-actions d-flex justify-content-end gap-2 pt-2">
        <a href="{{ route('workspace.hris.leave-requests') }}" class="btn btn-light btn-sm">Batal</a>
        <button class="btn btn-primary btn-sm" type="submit" @disabled(!$hasHrApprover)>Kirim pengajuan</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-leave-form]');
    if (!form) return;
    const list = form.querySelector('[data-date-list]');
    const remaining = Number(form.dataset.remainingLeave || 0);
    const refresh = () => [...list.querySelectorAll('[data-date-row]')].forEach((row, index) => {
        row.querySelector('.workspace-leave-date-index').textContent = index + 1;
        row.querySelector('[data-remove-leave-date]').disabled = list.children.length === 1;
    });
    form.querySelector('[data-add-leave-date]').addEventListener('click', () => {
        const row = list.querySelector('[data-date-row]').cloneNode(true);
        row.querySelector('input').value = '';
        list.appendChild(row);
        refresh();
    });
    list.addEventListener('click', (event) => {
        if (event.target.matches('[data-remove-leave-date]') && list.children.length > 1) {
            event.target.closest('[data-date-row]').remove();
            refresh();
        }
    });
    form.querySelectorAll('[data-leave-type]').forEach((input) => input.addEventListener('change', () => {
        form.querySelector('[data-supporting-document]').required = input.value === 'sick' && input.checked;
        form.querySelector('[data-quota-panel]').classList.toggle('opacity-50', input.value === 'sick' && input.checked);
    }));
    form.addEventListener('submit', (event) => {
        const values = [...list.querySelectorAll('input[name="dates[]"]')].map((input) => input.value).filter(Boolean);
        const type = form.querySelector('[data-leave-type]:checked')?.value;
        if (new Set(values).size !== values.length || (type === 'leave' && values.length > remaining)) {
            event.preventDefault();
            alert(new Set(values).size !== values.length ? 'Tanggal pengajuan tidak boleh duplikat.' : 'Jumlah tanggal melebihi sisa kuota cuti.');
        }
    });
    refresh();
});
</script>
@endsection
