@extends('layouts.app', ['title' => 'Pipeline Sales', 'wide' => true])
@section('content')
<div class="crm-pipeline-toolbar">
    <div class="d-flex gap-2"><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#crmOpportunityCreateModal">Tambah opportunity</button></div>
    <form method="GET" class="input-group input-group-sm"><span class="input-group-text">⌕</span><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari prospek atau opportunity..."><button class="btn btn-outline-secondary">Cari</button></form>
    <div class="small text-secondary align-self-center">Prospek dan opportunity dikelola dalam satu pipeline.</div>
    <form method="GET" class="form-check form-switch small text-secondary align-self-center"><input type="hidden" name="q" value="{{ request('q') }}"><input class="form-check-input" type="checkbox" role="switch" id="show-archived" name="show_archived" value="1" @checked($showArchived) onchange="this.form.submit()"><label class="form-check-label" for="show-archived">Tampilkan yang dihapus di Odoo</label></form>
</div>
<div class="crm-pipeline-board" data-crm-pipeline data-move-url="{{ route('workspace.crm.pipeline.move', '__ID__') }}">
@foreach($stages as $stage)
    <section class="crm-pipeline-column" data-stage-id="{{ $stage->id }}">
        <div class="d-flex justify-content-between align-items-center mb-2"><h2 class="h6 mb-0">{{ $stage->name }}</h2><span class="badge text-bg-light">{{ $cards->get($stage->id, collect())->count() }}</span></div>
        <div class="crm-pipeline-dropzone" data-dropzone>
        @foreach($cards->get($stage->id, collect()) as $opportunity)
            @php($sourceDeleted = $opportunity->source_record_status === 'deleted' || $opportunity->trashed())
            <article class="card shadow-sm mb-2 crm-pipeline-card {{ $sourceDeleted ? 'opacity-75' : '' }}" draggable="{{ $sourceDeleted ? 'false' : 'true' }}" data-opportunity-id="{{ $opportunity->id }}">
                <div class="card-body p-3"><div class="d-flex justify-content-between gap-2"><div class="fw-semibold">{{ $opportunity->name }}</div><span class="badge {{ $sourceDeleted ? 'text-bg-danger' : 'text-bg-info' }}">{{ $sourceDeleted ? 'Dihapus di Odoo' : ($opportunity->record_type === 'lead' ? 'Lead' : 'Opportunity') }}</span></div><div class="small text-secondary">{{ $opportunity->account?->company?->name ?: $opportunity->company?->name ?: $opportunity->account?->name ?: 'Client belum diatur' }}</div><div class="small mt-2">{{ $opportunity->amount ? 'Rp '.number_format((float) $opportunity->amount, 0, ',', '.') : 'Nilai belum diatur' }}</div><div class="d-flex justify-content-between align-items-center mt-3"><span class="badge {{ $opportunity->closingStatus?->is_closed ? 'text-bg-secondary' : 'text-bg-light' }}">{{ $opportunity->closingStatus?->name ?: 'Terbuka' }}</span><a href="{{ route('workspace.crm.edit', ['opportunities', $opportunity->id]) }}" class="small">Detail</a></div></div>
            </article>
        @endforeach
        </div>
    </section>
@endforeach
</div>
<div class="modal fade" id="crmOpportunityCreateModal" tabindex="-1" aria-labelledby="crmOpportunityCreateTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h2 class="modal-title h5" id="crmOpportunityCreateTitle">Opportunity baru</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
        <form method="POST" action="{{ route('workspace.crm.store', 'opportunities') }}"><div class="modal-body"><input type="hidden" name="return_to" value="pipeline">@csrf
            <div class="row g-3"><div class="col-md-8"><label class="form-label">Nama opportunity <span class="text-danger">*</span></label><input name="name" class="form-control" required maxlength="255"></div><div class="col-md-4"><label class="form-label">Client/account <span class="text-danger">*</span></label><select name="account_id" class="form-select" required><option value="">Pilih client</option>@foreach($accounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select></div><div class="col-md-4"><label class="form-label">Stage</label><select name="stage_id" class="form-select">@foreach($stages as $stage)<option value="{{ $stage->id }}" @selected($stage->code === 'new')>{{ $stage->name }}</option>@endforeach</select></div><div class="col-md-4"><label class="form-label">Team Sales</label><select name="team_id" class="form-select"><option value="">Tanpa team</option>@foreach($teams as $team)<option value="{{ $team->id }}">{{ $team->name }}</option>@endforeach</select></div><div class="col-md-4"><label class="form-label">Nilai</label><input name="amount" type="number" min="0" step="0.01" class="form-control"></div><div class="col-md-4"><label class="form-label">Estimasi closing</label><input name="expected_close_date" type="date" class="form-control"></div><div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="3"></textarea></div></div>
        </div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan opportunity</button></div></form>
    </div></div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const board = document.querySelector('[data-crm-pipeline]');
    if (!board) return;
    let dragged = null;
    board.querySelectorAll('.crm-pipeline-card').forEach((card) => card.addEventListener('dragstart', () => { dragged = card; card.classList.add('opacity-50'); }));
    board.addEventListener('dragend', () => { if (dragged) dragged.classList.remove('opacity-50'); dragged = null; });
    board.querySelectorAll('[data-dropzone]').forEach((zone) => {
        zone.addEventListener('dragover', (event) => event.preventDefault());
        zone.addEventListener('drop', async (event) => {
            event.preventDefault();
            if (!dragged) return;
            const column = zone.closest('[data-stage-id]');
            const url = board.dataset.moveUrl.replace('__ID__', dragged.dataset.opportunityId);
            const response = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ stage_id: column.dataset.stageId }) });
            if (response.ok) zone.appendChild(dragged); else alert('Tahap opportunity tidak dapat diperbarui.');
        });
    });
});
</script>
@endpush
@endsection
