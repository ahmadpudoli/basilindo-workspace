@extends('layouts.app', ['title' => 'Master Penomoran'])
@section('content')
@php($sortLink=function($column) use($sort,$direction){return request()->fullUrlWithQuery(['sort'=>$column,'direction'=>$sort===$column&&$direction==='asc'?'desc':'asc','page'=>null]);})
<div class="mb-4"><div class="text-uppercase text-secondary small fw-semibold mb-2">Core · Master data</div><h1 class="h3 mb-1">Master Penomoran</h1><p class="text-secondary mb-0">Atur format nomor dan counter per perusahaan.</p></div>
@php($counterScopes = ['yearly' => 'Tahunan', 'monthly' => 'Bulanan', 'daily' => 'Harian', 'global' => 'Global'])
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h2 class="h6 mb-4">Tambah master penomoran</h2>
        <form method="POST" action="{{ route('workspace.core.numbering-rules.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="workspace-floating-field">
                        <select id="numbering-company" name="company_id" class="form-select" required>
                            @foreach($companies as $company)<option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->name }}</option>@endforeach
                        </select>
                        <label for="numbering-company" class="form-label">Perusahaan</label>
                    </div>
                    @error('company_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <div class="workspace-floating-field">
                        <select id="numbering-form" name="form_key" class="form-select" required>
                            @foreach($formCatalog as $key => $name)<option value="{{ $key }}" @selected(old('form_key') === $key)>{{ $name }}</option>@endforeach
                        </select>
                        <label for="numbering-form" class="form-label">Jenis form</label>
                    </div>
                    @error('form_key')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <div class="workspace-floating-field">
                        <input id="numbering-format" name="format" class="form-control font-monospace" maxlength="255" value="{{ old('format', 'OT/[tahun]/[bulan_romawi]/[no_urut]') }}" aria-describedby="numbering-format-help" required>
                        <label for="numbering-format" class="form-label">Format nomor</label>
                    </div>
                    @error('format')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <div class="workspace-floating-field">
                        <select id="numbering-counter" name="counter_scope" class="form-select" required>
                            @foreach($counterScopes as $key => $label)<option value="{{ $key }}" @selected(old('counter_scope', 'yearly') === $key)>{{ $label }}</option>@endforeach
                        </select>
                        <label for="numbering-counter" class="form-label">Counter</label>
                    </div>
                    @error('counter_scope')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <div class="form-text">Nomor urut dimulai kembali setiap periode. Global tidak direset.</div>
                </div>
                <div class="col-12">
                    <details class="small text-secondary mt-2" id="numbering-format-help"><summary class="text-primary">Bantuan pola penomoran</summary><div class="border rounded p-3 mt-2"><p class="mb-2">Gabungkan teks, pemisah, dan token berikut:</p><ul class="mb-2 ps-3"><li><code>[C]</code>: nomor urut tanpa tambahan nol.</li><li><code>[CCCCC]</code>: nomor urut minimal 5 digit, misalnya 00001. Sesuaikan jumlah C untuk mengatur digit.</li><li><code>[no_urut]</code>: nomor urut minimal 4 digit, misalnya 0001.</li><li><code>[tahun]</code>: tahun 4 digit.</li><li><code>[bulan]</code>: bulan 2 digit; <code>[bulan_romawi]</code>: bulan Romawi.</li><li><code>[hari]</code>: hari 2 digit.</li></ul><p class="mb-0">Contoh: <code class="text-break">OT/[tahun]/[bulan_romawi]/[CCCCC]</code><br>Hasil untuk 3 November 2026, urutan 1: <code>OT/2026/XI/00001</code>.</p></div></details>
                </div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Simpan master penomoran</button></div>
            </div>
        </form>
    </div>
</div>
<form method="GET" class="workspace-list-filter-form card card-body border-0 shadow-sm mb-3">
    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="direction" value="{{ $direction }}">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <div class="workspace-floating-field">
                <input id="numbering-search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nama, kode, atau format">
                <label for="numbering-search" class="form-label">Pencarian</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="workspace-floating-field">
                <div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-empty-label="Semua perusahaan">
                    <button type="button" class="form-select text-start workspace-dropdown-trigger workspace-compact-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <span data-dropdown-label>{{ $companyIds->count() ? $companyIds->count().' perusahaan dipilih' : 'Semua perusahaan' }}</span>
                    </button>
                    <div class="dropdown-menu workspace-dropdown-menu p-2">
                        <input type="search" class="form-control form-control-sm mb-2" placeholder="Cari perusahaan..." data-dropdown-search>
                        <div class="workspace-dropdown-options">
                            @forelse($companies as $company)
                                <label class="workspace-dropdown-option" data-dropdown-option>
                                    <input type="checkbox" name="company_ids[]" value="{{ $company->id }}" data-dropdown-item data-dropdown-value="{{ $company->name }}" @checked($companyIds->contains((int) $company->id))>
                                    <span>{{ $company->name }}</span>
                                </label>
                            @empty
                                <div class="text-secondary small p-2">Belum ada perusahaan dalam scope Anda.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <label class="form-label">Perusahaan</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="workspace-floating-field">
                <div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-dropdown-empty-label="Semua jenis form">
                    <button type="button" class="form-select text-start workspace-dropdown-trigger workspace-compact-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <span data-dropdown-label>{{ $formKeys->count() ? $formKeys->count().' jenis form dipilih' : 'Semua jenis form' }}</span>
                    </button>
                    <div class="dropdown-menu workspace-dropdown-menu p-2">
                        <input type="search" class="form-control form-control-sm mb-2" placeholder="Cari jenis form..." data-dropdown-search>
                        <div class="workspace-dropdown-options">
                            @foreach($formCatalog as $key => $label)
                                <label class="workspace-dropdown-option" data-dropdown-option>
                                    <input type="checkbox" name="form_keys[]" value="{{ $key }}" data-dropdown-item data-dropdown-value="{{ $label }}" @checked($formKeys->contains($key))>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <label class="form-label">Jenis form</label>
            </div>
        </div>
        <div class="col-md-1">
            <div class="workspace-floating-field">
                <select id="numbering-limit" name="limit" class="form-select">
                    @foreach([10, 25, 50, 100] as $limit)<option value="{{ $limit }}" @selected($perPage === $limit)>{{ $limit }}</option>@endforeach
                </select>
                <label for="numbering-limit" class="form-label">Baris</label>
            </div>
        </div>
        <div class="col-md-2"><div class="workspace-filter-actions"><a href="{{ route('workspace.core.numbering-rules') }}" class="btn btn-outline-secondary">Reset</a><button type="submit" class="btn workspace-filter-apply">Terapkan</button></div></div>
    </div>
</form>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th><a href="{{ $sortLink('company_id') }}" class="text-decoration-none text-reset">Perusahaan @if($sort==='company_id'){{ $direction==='asc'?'↑':'↓' }}@endif</a></th><th><a href="{{ $sortLink('form_key') }}" class="text-decoration-none text-reset">Form @if($sort==='form_key'){{ $direction==='asc'?'↑':'↓' }}@endif</a></th><th><a href="{{ $sortLink('format') }}" class="text-decoration-none text-reset">Format @if($sort==='format'){{ $direction==='asc'?'↑':'↓' }}@endif</a></th><th><a href="{{ $sortLink('counter_scope') }}" class="text-decoration-none text-reset">Counter @if($sort==='counter_scope'){{ $direction==='asc'?'↑':'↓' }}@endif</a></th><th><a href="{{ $sortLink('is_active') }}" class="text-decoration-none text-reset">Status @if($sort==='is_active'){{ $direction==='asc'?'↑':'↓' }}@endif</a></th><th>Counter terakhir</th><th class="text-end">Aksi</th></tr></thead><tbody>@forelse($rules as $rule)<tr><td>{{ $rule->company?->name }}</td><td><strong>{{ $rule->name }}</strong><br><code>{{ $rule->form_key }}</code></td><td><code>{{ $rule->format }}</code></td><td>{{ $counterScopes[$rule->counter_scope] ?? $rule->counter_scope }}</td><td>{{ $rule->is_active?'Aktif':'Nonaktif' }}</td><td>{{ max(0, (int) $rule->next_number - 1) }}</td><td class="text-end text-nowrap"><a href="{{ request()->fullUrlWithQuery(['edit' => $rule->id]) }}" class="btn btn-sm btn-outline-primary">Edit</a> <form method="POST" action="{{ route('workspace.core.numbering-rules.update',$rule) }}" class="d-inline">@csrf @method('PUT')<input type="hidden" name="name" value="{{ $rule->name }}"><input type="hidden" name="format" value="{{ $rule->format }}"><input type="hidden" name="counter_scope" value="{{ $rule->counter_scope }}"><input type="hidden" name="is_active" value="{{ $rule->is_active?0:1 }}"><button class="btn btn-sm btn-outline-secondary">{{ $rule->is_active?'Nonaktifkan':'Aktifkan' }}</button></form></td></tr>@empty<tr><td colspan="7" class="text-center text-secondary py-4">Belum ada master penomoran.</td></tr>@endforelse</tbody></table></div><div class="card-footer bg-white d-flex justify-content-between align-items-center"><span class="small text-secondary">Menampilkan {{ $rules->firstItem()??0 }}–{{ $rules->lastItem()??0 }} dari {{ $rules->total() }} data</span>{{ $rules->onEachSide(1)->links() }}</div></div>
@if($editRule)
<div class="modal show d-block" id="editRuleModal" tabindex="-1" style="background:rgba(0,0,0,.45)" aria-labelledby="editRuleTitle" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('workspace.core.numbering-rules.update', $editRule) }}">
                @csrf @method('PUT')
                <div class="modal-header">
                    <div><h5 class="modal-title" id="editRuleTitle">Edit {{ $editRule->name }}</h5><div class="small text-secondary">{{ $editRule->company?->name }}</div></div>
                    <a href="{{ request()->fullUrlWithQuery(['edit' => null]) }}" class="btn-close" aria-label="Tutup"></a>
                </div>
                <div class="modal-body">
                    <div class="row g-3 pt-2">
                        <div class="col-12">
                            <div class="workspace-floating-field">
                                <input id="edit-numbering-format" name="format" class="form-control font-monospace @error('format') is-invalid @enderror" maxlength="255" value="{{ old('format', $editRule->format) }}" aria-describedby="edit-numbering-format-help" required>
                                <label for="edit-numbering-format" class="form-label">Format nomor</label>
                            </div>
                            @error('format')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <div class="workspace-floating-field">
                                <select id="edit-numbering-counter" name="counter_scope" class="form-select @error('counter_scope') is-invalid @enderror" required>
                                    @foreach($counterScopes as $key => $label)<option value="{{ $key }}" @selected(old('counter_scope', $editRule->counter_scope) === $key)>{{ $label }}</option>@endforeach
                                </select>
                                <label for="edit-numbering-counter" class="form-label">Counter</label>
                            </div>
                            @error('counter_scope')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div class="form-text">Nomor urut dimulai kembali setiap periode. Global tidak direset.</div>
                        </div>
                    </div>
                    <details class="small text-secondary mt-2" id="edit-numbering-format-help"><summary class="text-primary">Bantuan pola penomoran</summary><div class="border rounded p-3 mt-2"><p class="mb-2">Gabungkan teks, pemisah, dan token berikut:</p><ul class="mb-2 ps-3"><li><code>[C]</code>: nomor urut tanpa tambahan nol.</li><li><code>[CCCCC]</code>: nomor urut minimal 5 digit, misalnya 00001. Sesuaikan jumlah C untuk mengatur digit.</li><li><code>[no_urut]</code>: nomor urut minimal 4 digit, misalnya 0001.</li><li><code>[tahun]</code>: tahun 4 digit.</li><li><code>[bulan]</code>: bulan 2 digit; <code>[bulan_romawi]</code>: bulan Romawi.</li><li><code>[hari]</code>: hari 2 digit.</li></ul><p class="mb-0">Contoh: <code class="text-break">OT/[tahun]/[bulan_romawi]/[CCCCC]</code><br>Hasil untuk 3 November 2026, urutan 1: <code>OT/2026/XI/00001</code>.</p></div></details>
                    <input type="hidden" name="name" value="{{ $editRule->name }}">
                    <input type="hidden" name="is_active" value="{{ $editRule->is_active ? 1 : 0 }}">
                </div>
                <div class="modal-footer"><a href="{{ request()->fullUrlWithQuery(['edit' => null]) }}" class="btn btn-outline-secondary">Batal</a><button type="submit" class="btn btn-primary">Simpan perubahan</button></div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
