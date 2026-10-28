@extends('layouts.app', ['title' => 'Ajukan Cuti atau Sakit'])

@section('content')
@php($dateRows = old('dates', ['']))
<div class="mb-4">
    <div class="text-uppercase text-secondary small fw-semibold mb-2">Kehadiran</div>
    <h1 class="h3 mb-1">Ajukan cuti atau sakit</h1>
    <p class="text-secondary mb-0">Pilih tanggal satu per satu. Tanggal yang beririsan dengan pengajuan aktif akan ditolak.</p>
</div>

@if($errors->any())
    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('workspace.hris.leave-requests.store') }}" enctype="multipart/form-data" class="card card-body workspace-leave-form" data-leave-form data-remaining-leave="{{ max(0, $quota - $usedLeave) }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Karyawan</label>
            <div class="workspace-readonly-field">
                <strong>{{ $employee->full_name }}</strong>
                <small>{{ $employee->employee_number }} · {{ $employee->company?->name ?? '-' }}</small>
            </div>
            <div class="form-text">Pengajuan hanya dapat dibuat untuk karyawan yang terhubung dengan akun login.</div>
        </div>
        <div class="col-md-6">
            <label for="leave-type" class="form-label">Jenis <span class="text-danger">*</span></label>
            <select id="leave-type" name="type" class="form-select" required data-leave-type>
                <option value="leave" @selected(old('type', 'leave') === 'leave')>Cuti</option>
                <option value="sick" @selected(old('type') === 'sick')>Sakit</option>
            </select>
        </div>
        <div class="col-12">
            <div class="workspace-leave-quota" data-quota-panel>
                <div><span class="text-secondary">Kuota cuti {{ $year }}</span><strong>{{ $quota }} hari</strong></div>
                <div><span class="text-secondary">Sudah disetujui</span><strong>{{ $usedLeave }} hari</strong></div>
                <div><span class="text-secondary">Sisa cuti</span><strong data-remaining-label>{{ max(0, $quota - $usedLeave) }} hari</strong></div>
                <div><span class="text-secondary">Sakit tercatat</span><strong>{{ $sickDays }} hari</strong></div>
            </div>
        </div>
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-label mb-0">Tanggal pengajuan <span class="text-danger">*</span></label>
                <button type="button" class="btn btn-outline-primary btn-sm" data-add-leave-date>+ Tambah tanggal</button>
            </div>
            <div class="workspace-leave-dates" data-date-list>
                @foreach($dateRows as $index => $date)
                    <div class="workspace-leave-date-row" data-date-row>
                        <span class="workspace-leave-date-index">{{ $index + 1 }}</span>
                        <input type="date" name="dates[]" class="form-control" value="{{ $date }}" required>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-remove-leave-date aria-label="Hapus tanggal">Hapus</button>
                    </div>
                @endforeach
            </div>
            <div class="form-text">Tanggal dapat diisi mundur (back date). Untuk cuti, jumlah tanggal tidak boleh melebihi sisa kuota.</div>
        </div>
        <div class="col-md-7">
            <label class="form-label">Atasan/manager approver</label>
            <div class="workspace-readonly-field">
                @if($employee->manager)
                    <strong>{{ $employee->manager->full_name }}</strong>
                    <small>{{ $employee->manager->job_title ?? 'Atasan langsung' }} · {{ $employee->manager->employee_number }}</small>
                @else
                    <span class="text-danger">Belum ada atasan yang ditentukan</span>
                @endif
            </div>
            <div class="form-text">Setelah atasan menyetujui, pengajuan diteruskan ke tahap approval HR sesuai workflow.</div>
        </div>
        <div class="col-md-5">
            <label for="supporting-document" class="form-label">Dokumen pendukung <span class="text-danger" data-sick-required>*</span></label>
            <input id="supporting-document" type="file" name="supporting_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" data-supporting-document>
            <div class="form-text" data-supporting-help>Wajib untuk sakit. Format PDF/JPG/PNG, maksimal 10 MB.</div>
        </div>
        <div class="col-12">
            <label class="form-label">Approver HR</label>
            <div class="workspace-readonly-field">
                @forelse($hrApprovers as $approver)
                    <div class="d-flex align-items-center gap-2">
                        <span class="workspace-approval-avatar">{{ strtoupper(substr($approver->user?->name ?? '?', 0, 1)) }}</span>
                        <span><strong>{{ $approver->user?->name ?? 'Pengguna tidak ditemukan' }}</strong><small class="d-block">{{ $approver->user?->email }}</small></span>
                    </div>
                @empty
                    <span class="text-danger">Approver HR belum dikonfigurasi untuk perusahaan ini.</span>
                @endforelse
            </div>
            <div class="form-text">Approver HR ditentukan dari Pengaturan HRIS dan akan memproses tahap kedua setelah approval atasan.</div>
        </div>
        <div class="col-12">
            <label for="reason" class="form-label">Alasan</label>
            <textarea id="reason" name="reason" class="form-control" rows="4" maxlength="2000">{{ old('reason') }}</textarea>
        </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('workspace.hris.leave-requests') }}" class="btn btn-light btn-sm">Batal</a>
        <button class="btn btn-primary btn-sm" type="submit">Kirim pengajuan</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('[data-leave-form]');
    if (!form) return;
    const list = form.querySelector('[data-date-list]');
    const type = form.querySelector('[data-leave-type]');
    const documentInput = form.querySelector('[data-supporting-document]');
    const quotaPanel = form.querySelector('[data-quota-panel]');
    const remaining = Number(form.dataset.remainingLeave || 0);

    const refresh = () => {
        [...list.querySelectorAll('[data-date-row]')].forEach((row, index) => {
            row.querySelector('.workspace-leave-date-index').textContent = index + 1;
            row.querySelector('[data-remove-leave-date]').disabled = list.children.length === 1;
        });
        const isSick = type.value === 'sick';
        documentInput.required = isSick;
        form.querySelectorAll('[data-sick-required]').forEach((label) => label.classList.toggle('d-none', !isSick));
        quotaPanel.classList.toggle('opacity-50', isSick);
    };

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
    type.addEventListener('change', refresh);
    form.addEventListener('submit', (event) => {
        const values = [...list.querySelectorAll('input[name="dates[]"]')].map((input) => input.value).filter(Boolean);
        const unique = new Set(values);
        if (values.length !== unique.size) {
            event.preventDefault();
            alert('Tanggal pengajuan tidak boleh duplikat.');
        } else if (type.value === 'leave' && values.length > remaining) {
            event.preventDefault();
            alert('Jumlah tanggal melebihi sisa kuota cuti.');
        }
    });
    refresh();
});
</script>
@endsection
