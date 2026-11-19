<div class="card border-0 shadow-sm overflow-hidden claim-summary-table-container workspace-table-card" aria-label="Daftar pengajuan">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 workspace-data-table workspace-data-table--claims claim-summary-table">
            <colgroup><col style="width: 15%"><col style="width: 14%"><col style="width: 19%"><col style="width: 14%"><col style="width: 12%"><col style="width: 10%"><col style="width: 9%"><col style="width: 7%"></colgroup>
            <thead class="table-light"><tr>
                <th scope="col">Karyawan / Perusahaan</th>
                <th scope="col">Pengajuan</th>
                <th scope="col">Tujuan / Referensi</th>
                <th scope="col">Kategori</th>
                <th scope="col"><a class="workspace-sort-link" href="{{ request()->fullUrlWithQuery(['sort' => 'amount', 'direction' => $sort === 'amount' && $direction === 'asc' ? 'desc' : 'asc']) }}">Nominal {!! $sort === 'amount' ? ($direction === 'asc' ? '↑' : '↓') : '' !!}</a></th>
                <th scope="col">Status pengajuan</th>
                <th scope="col">Status pembayaran</th>
                <th scope="col" class="text-end">Aksi</th>
            </tr></thead>
            <tbody>
            @forelse($items as $item)
                @php($claimCategories = $item->expenseItems->pluck('category')->filter()->unique()->values())
                <tr data-claim-id="{{ $item->getKey() }}">
                    <td><div class="fw-semibold">{{ $item->employee?->full_name ?? 'Karyawan' }}</div><div class="small text-secondary">{{ $item->employee?->company?->name ?? 'Perusahaan tidak tersedia' }}</div></td>
                    <td><div class="fw-medium claim-summary-request-number">{{ $item->request_number ?: 'Nomor belum tersedia' }}</div><div class="small text-secondary">{{ $item->created_at?->timezone(config('app.timezone'))->translatedFormat('d M Y') }}</div></td>
                    <td><div>{{ $item->purpose ?: '—' }}</div>@if($item->reference_name_snapshot || $item->project?->name)<div class="small text-secondary">{{ $item->reference_name_snapshot ?: $item->project?->name }}</div>@endif</td>
                    <td><div class="d-flex flex-wrap gap-1">@forelse($claimCategories->take(3) as $claimCategory)<span class="badge rounded-pill text-bg-light border">{{ $claimCategory }}</span>@empty<span class="text-secondary">{{ $item->category ?: '—' }}</span>@endforelse @if($claimCategories->count() > 3)<span class="badge rounded-pill text-bg-light border">+{{ $claimCategories->count() - 3 }}</span>@endif</div></td>
                    <td class="text-nowrap fw-semibold">{{ $item->currency }} {{ number_format((float) $item->amount, 2, ',', '.') }}</td>
                    <td><span class="badge rounded-pill text-bg-{{ in_array($item->status, ['approved','completed'], true) ? 'success' : (in_array($item->status, ['rejected','cancelled'], true) ? 'danger' : ($item->status === 'needs_revision' ? 'info' : 'warning')) }}">{{ $item->status_label }}</span></td>
                    <td>@if($item->type === 'reimbursement')<span class="badge rounded-pill text-bg-{{ $item->payment_status === 'paid' ? 'success' : ($item->payment_status === 'pending_payment' ? 'warning' : 'secondary') }}">{{ $item->payment_status_label ?? 'Belum ada' }}</span>@else<span class="text-secondary">—</span>@endif</td>
                    <td class="text-end">
                        @can($summaryView ? 'viewFromSummary' : 'view', $item)
                            <div class="dropdown">
                                <button class="btn claim-summary-action-toggle" type="button" data-claim-action-toggle data-menu-id="claim-action-menu-{{ $item->getKey() }}" aria-controls="claim-action-menu-{{ $item->getKey() }}" aria-haspopup="true" aria-expanded="false" aria-label="Aksi pengajuan {{ $item->request_number ?: $item->employee?->full_name }}">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><circle cx="12" cy="5" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="19" r="1.8"/></svg>
                                </button>
                                <div id="claim-action-menu-{{ $item->getKey() }}" class="dropdown-menu dropdown-menu-end claim-summary-action-menu" role="menu">
                                    <div class="claim-summary-menu-heading">AKSI PENGAJUAN</div>
                                    <a class="dropdown-item" href="{{ $summaryView ? route('workspace.hris.claims.summary.show', array_merge(['claim' => $item], request()->query())) : route('workspace.hris.claims.show', $item) }}"><span class="claim-summary-menu-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.3-6 9.5-6 9.5 6 9.5 6-3.3 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/></svg></span><span><strong>Lihat detail</strong><small>Buka informasi pengajuan</small></span></a>
                                    @if($summaryView && $item->type === 'reimbursement' && $item->status === 'approved' && $item->payment_status === 'pending_payment' && (auth()->user()->hasRole('super_admin') || auth()->user()->can('process_hris_payment')))
                                        @php($activeReimbursementAccount = $item->employee?->paymentAccounts?->first())
                                        <button class="dropdown-item" type="button" data-claim-payment-open data-claim-payment-id="{{ $item->getKey() }}" data-payment-url="{{ route('workspace.hris.claims.payment.store', $item->id) }}" data-payment-number="{{ $item->request_number }}" data-payment-employee="{{ $item->employee?->full_name }}" data-payment-company="{{ $item->employee?->company?->name }}" data-payment-currency="{{ $item->currency }}" data-payment-amount="{{ number_format((float) $item->amount, 2, '.', '') }}" data-payment-account-available="{{ $activeReimbursementAccount ? '1' : '0' }}" data-payment-account-holder="{{ $activeReimbursementAccount?->account_holder }}" data-payment-bank-name="{{ $activeReimbursementAccount?->bank_name }}" data-payment-account-number="{{ $activeReimbursementAccount?->account_number }}"><span class="claim-summary-menu-icon print"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7.5h16v11H4zM4 9l8 5 8-5M8 4h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span><strong>Catat pembayaran</strong><small>Isi pembayaran tanpa meninggalkan ringkasan</small></span></button>
                                    @endif
                                    @if($item->type === 'reimbursement' && $item->finance_approved_at && $item->status === 'approved')
                                        @can('print', $item)<a class="dropdown-item" href="{{ route('workspace.hris.claims.print', $item) }}" target="_blank" rel="noopener"><span class="claim-summary-menu-icon print"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 8V3.5h10V8M7 17H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2M7 14h10v6.5H7V14Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M17.5 11.5h.01" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg></span><span><strong>Cetak dokumen</strong><small>Buka pratinjau cetak</small></span></a>@endcan
                                    @endif
                                </div>
                            </div>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-5"><div class="fw-semibold">Belum ada pengajuan</div><div class="small text-secondary mt-1">Tidak ada pengajuan {{ $activeTabType === 'reimbursement' ? 'reimbursement' : 'cash advance' }} pada periode dan filter yang dipilih. Draft tidak ditampilkan.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white workspace-list-pagination"><div class="workspace-pagination-summary">Menampilkan <strong>{{ $items->firstItem() ?? 0 }}</strong>–<strong>{{ $items->lastItem() ?? 0 }}</strong> dari <strong>{{ $items->total() }}</strong> pengajuan</div>{{ $items->onEachSide(1)->links() }}</div>
</div>
