@php
    $accountAddress = collect([$account->street, $account->street2, $account->city, $account->state, $account->zip, $account->country])->filter()->implode(', ');
    $companyAddress = collect([$account->partyCompany?->street, $account->partyCompany?->street2, $account->partyCompany?->city, $account->partyCompany?->state, $account->partyCompany?->zip, $account->partyCompany?->country])->filter()->implode(', ');
@endphp
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="small text-uppercase text-secondary fw-semibold mb-2">Alamat account</div>
                <div>{{ $accountAddress ?: 'Alamat account belum tersedia.' }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="small text-uppercase text-secondary fw-semibold mb-2">Alamat client / partner</div>
                <div>{{ $companyAddress ?: 'Alamat perusahaan belum tersedia.' }}</div>
                @if($account->partyCompany)
                    <small class="text-secondary d-block mt-2">{{ $account->partyCompany->name }}</small>
                @endif
            </div>
        </div>
    </div>
</div>
