@extends('layouts.app', ['title' => 'Pengaturan HRIS'])
@section('content')
@php($parameterLabels = ['hris.annual_leave_quota' => 'Kuota cuti/tahun', 'hris.leave_submission_cutoff' => 'Cut-off pengajuan', 'hris.overtime_maximum_minutes' => 'Maksimum lembur (menit)', 'hris.claim_final_approval_threshold' => 'Ambang approval final'])
@if($showWorkflow ?? false)
<div class="card card-body mb-3 workspace-workflow-card">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <div>
            <div class="workspace-form-kicker">Workflow approval</div>
            <h2 class="h5 mb-1">Approver HR cuti & sakit</h2>
            <p class="text-secondary mb-0">Tentukan pengguna HR yang menerima pengajuan setelah disetujui atasan.</p>
        </div>
        <span class="badge text-bg-light">Tahap 2 · HR</span>
    </div>
    <form method="POST" action="{{ route('workspace.settings.hris.workflow.store') }}">
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-lg-5">
                <label class="form-label">Perusahaan operasional <span class="text-danger">*</span></label>
                <select name="company_ids[]" class="form-select" multiple size="4" required>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" @selected(in_array($company->id, old('company_ids', [])))>{{ $company->name }}</option>
                    @endforeach
                </select>
                <div class="form-text">Gunakan Ctrl/Cmd untuk memilih beberapa perusahaan.</div>
            </div>
            <div class="col-lg-5">
                <label class="form-label">Pengguna HR <span class="text-danger">*</span></label>
                <select name="user_id" class="form-select" required>
                    <option value="">Pilih approver HR</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>{{ $user->name }} · {{ $user->email }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2"><button class="btn btn-primary w-100" type="submit">Simpan workflow</button></div>
        </div>
    </form>
    @if($workflowApprovers->isNotEmpty())
        <div class="workspace-workflow-assignment mt-3">
            <span class="text-secondary small">Konfigurasi aktif:</span>
            @foreach($workflowApprovers->groupBy('user_id') as $approvers)
                <span class="badge text-bg-light">{{ $approvers->first()->user?->name }} · {{ $approvers->pluck('company.name')->filter()->implode(', ') }}</span>
            @endforeach
        </div>
    @endif
</div>
@endif
<div class="mb-4"><div class="text-uppercase text-secondary small fw-semibold mb-2">Pengaturan</div><h1 class="h3 mb-1">Parameter HRIS</h1><p class="text-secondary mb-0">Atur nilai bisnis global atau per perusahaan berdasarkan tanggal berlaku.</p></div>
@if($errors->any())
    <div class="alert alert-warning workspace-alert" role="alert">
        <div class="fw-semibold mb-1">Parameter belum disimpan.</div>
        <ul class="mb-0 ps-3">
            @foreach(array_unique($errors->all()) as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form method="POST" action="{{ $editing ? route('workspace.settings.hris.update', $editing) : route('workspace.settings.hris.store') }}" class="card card-body mb-3">@csrf @if($editing) @method('PUT') @endif
<div class="row g-3"><div class="col-md-3"><label class="form-label">Perusahaan</label><select name="company_id" class="form-select"><option value="">Global</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected((string) old('company_id', $editing?->company_id) === (string) $company->id)>{{ $company->name }}</option>@endforeach</select></div><div class="col-md-3"><label class="form-label">Parameter</label><select name="key" class="form-select"><option value="hris.annual_leave_quota" @selected(old('key', $editing?->key) === 'hris.annual_leave_quota')>Kuota cuti/tahun</option><option value="hris.leave_submission_cutoff" @selected(old('key', $editing?->key) === 'hris.leave_submission_cutoff')>Cut-off pengajuan</option><option value="hris.overtime_maximum_minutes" @selected(old('key', $editing?->key) === 'hris.overtime_maximum_minutes')>Maksimum lembur (menit)</option><option value="hris.claim_final_approval_threshold" @selected(old('key', $editing?->key) === 'hris.claim_final_approval_threshold')>Ambang approval final</option></select></div><div class="col-md-3"><label class="form-label">Nilai</label><input name="value" value="{{ old('value', is_array($editing?->value) ? json_encode($editing->value) : $editing?->value) }}" class="form-control" required></div><div class="col-md-2"><label class="form-label">Berlaku mulai</label><input type="date" name="effective_from" value="{{ old('effective_from', $editing?->effective_from?->format('Y-m-d')) }}" class="form-control"></div><div class="col-md-1 d-flex align-items-end"><button class="btn btn-primary">{{ $editing ? 'Perbarui' : 'Simpan' }}</button></div></div></form>
<form method="GET" class="card card-body border-0 shadow-sm mb-3 workspace-list-filter-form"><div class="row g-2 align-items-end"><div class="col-md-5"><label class="form-label">Cari parameter</label><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Nama parameter"></div><div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" @selected(request('status', 'active') === 'active')>Aktif</option><option value="archived" @selected(request('status') === 'archived')>Diarsipkan</option><option value="all" @selected(request('status') === 'all')>Semua</option></select></div><div class="col-md-2"><label class="form-label">Baris</label><select name="per_page" class="form-select"><option value="10" @selected($perPage === 10)>10 / halaman</option><option value="25" @selected($perPage === 25)>25 / halaman</option><option value="50" @selected($perPage === 50)>50 / halaman</option><option value="100" @selected($perPage === 100)>100 / halaman</option></select></div><div class="col-md-2"><div class="workspace-filter-actions"><a href="{{ route('workspace.settings.hris') }}" class="btn btn-sm btn-outline-secondary">Reset</a><button class="btn btn-sm btn-primary" type="submit">Terapkan</button></div></div></div></form>
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Scope</th><th>Parameter</th><th>Nilai</th><th>Berlaku mulai</th><th>Status</th><th class="text-end">Aksi</th></tr></thead><tbody>@forelse($settings as $setting)<tr><td>{{ $setting->company_id ? ($companies->firstWhere('id',$setting->company_id)?->name ?? 'Perusahaan') : 'Global' }}</td><td>{{ $setting->key }}</td><td>{{ is_array($setting->value) ? json_encode($setting->value) : $setting->value }}</td><td>{{ $setting->effective_from?->format('d/m/Y') ?? 'Selalu' }}</td><td>@if($setting->archived_at)<span class="badge text-bg-secondary">Diarsipkan</span>@else<span class="badge text-bg-success">Aktif</span>@endif</td><td class="workspace-table-actions text-end"><a href="{{ route('workspace.settings.hris.edit', $setting) }}" class="btn btn-sm btn-outline-primary">Edit</a>@if($setting->archived_at)<form method="POST" action="{{ route('workspace.settings.hris.restore', $setting) }}" class="d-inline">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-success">Pulihkan</button></form>@else<form method="POST" action="{{ route('workspace.settings.hris.archive', $setting) }}" class="d-inline" data-confirm="Parameter ini akan diarsipkan. Lanjutkan?" data-confirm-action="Arsipkan">@csrf @method('PATCH')<button type="submit" class="btn btn-sm btn-outline-secondary">Arsipkan</button></form>@endif</td></tr>@empty<tr><td colspan="6" class="text-center text-secondary py-5">Belum ada parameter.</td></tr>@endforelse</tbody></table></div><div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2"><small class="text-secondary">Menampilkan {{ $settings->firstItem() ?? 0 }}–{{ $settings->lastItem() ?? 0 }} dari {{ $settings->total() }} parameter · Halaman {{ $settings->currentPage() }} dari {{ $settings->lastPage() }}</small><div>{{ $settings->links() }}</div></div></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const labels = @json($parameterLabels);
    const table = document.querySelector('.card table');
    if (!table) return;
    table.querySelector('thead th:nth-child(2)')?.replaceChildren(document.createTextNode('Nama parameter / kode'));
    table.querySelectorAll('tbody tr').forEach((row) => {
        const cell = row.querySelector('td:nth-child(2)');
        const key = cell?.textContent.trim();
        if (!cell || !key || !labels[key]) return;
        cell.innerHTML = `<strong>${labels[key]}</strong><code class="d-block small text-secondary mt-1">${key}</code>`;
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const parameter = document.querySelector('select[name="key"]');
    const value = document.querySelector('input[name="value"]');
    if (!parameter || !value) return;

    const types = {
        'hris.annual_leave_quota': { type: 'number', step: '1', min: '0', description: 'Jumlah kuota cuti tahunan yang tersedia untuk setiap karyawan pada tahun terkait.', help: 'Isi jumlah hari kuota cuti, contoh: 12.' },
        'hris.leave_submission_cutoff': { type: 'date', step: null, min: null, description: 'Tanggal terakhir karyawan dapat mengirim pengajuan cuti untuk tahun terkait.', help: 'Pilih tanggal terakhir pengajuan cuti pada tahun terkait.' },
        'hris.overtime_maximum_minutes': { type: 'number', step: '1', min: '0', description: 'Batas maksimum durasi lembur yang dapat diajukan, dalam satuan menit. Jika tidak diatur, tidak ada batas maksimum.', help: 'Opsional. Isi maksimum lembur dalam menit, contoh: 480.' },
        'hris.claim_final_approval_threshold': { type: 'number', step: '0.01', min: '0', description: 'Nilai nominal yang menentukan kapan claim memerlukan approval final tambahan.', help: 'Isi nominal ambang approval final.' },
    };
    let description = parameter.parentElement.querySelector('[data-parameter-description]');
    if (!description) { description = document.createElement('div'); description.className = 'form-text'; description.dataset.parameterDescription = 'true'; parameter.insertAdjacentElement('afterend', description); }
    let help = value.parentElement.querySelector('[data-parameter-value-help]');
    if (!help) { help = document.createElement('div'); help.className = 'form-text'; help.dataset.parameterValueHelp = 'true'; value.insertAdjacentElement('afterend', help); }
    const refreshValueField = () => {
        const config = types[parameter.value] || { type: 'text', step: null, min: null, description: 'Parameter bisnis Workspace yang dapat dikonfigurasi sesuai kebutuhan operasional.', help: 'Isi nilai parameter sesuai kebijakan bisnis.' };
        value.type = config.type;
        value.step = config.step || '';
        if (config.min !== null) value.min = config.min; else value.removeAttribute('min');
        value.placeholder = config.type === 'date' ? 'dd/mm/yyyy' : (config.type === 'number' ? 'Masukkan angka' : 'Masukkan nilai');
        description.textContent = config.description;
        help.textContent = config.help;
    };
    parameter.addEventListener('change', refreshValueField);
    refreshValueField();
});
</script>
@endsection
