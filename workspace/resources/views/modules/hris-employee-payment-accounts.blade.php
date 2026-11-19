@extends('layouts.app', ['title' => 'Data Pembayaran Karyawan'])

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="text-muted small">HRIS / Pegawai</div>
            <h1 class="h3 mb-1">Data pembayaran</h1>
            <div class="text-muted">{{ $employee->full_name }} · {{ $employee->employee_number }}</div>
        </div>
        <a href="{{ route('workspace.hris.employees') }}" class="btn btn-outline-secondary">Kembali ke karyawan</a>
    </div>

    <div class="row g-3 align-items-start">
        @foreach(['payroll' => ['Gaji', 'Rekening untuk pembayaran gaji.', 'view_employee_payroll_account', 'update_employee_payroll_account'], 'reimbursement' => ['Reimbursement', 'Rekening untuk pembayaran reimbursement.', 'view_employee_reimbursement_account', 'update_employee_reimbursement_account']] as $type => [$label, $description, $viewPermission, $updatePermission])
            @continue(!isset($accounts[$type]))
            @php($canView = auth()->user()->hasRole('super_admin') || auth()->user()->can($viewPermission))
            @php($canUpdate = auth()->user()->hasRole('super_admin') || auth()->user()->can($updatePermission))
            @php($current = $accounts[$type]->firstWhere('is_current', true))
            <div class="col-12 col-xl-6">
                <section class="card workspace-table-card">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-1">Rekening {{ $label }}</h2>
                        <div class="text-muted small">{{ $description }}</div>
                    </div>
                    <div class="card-body">
                        @if($canView)
                            @if($current)
                                <div class="alert alert-light border mb-3">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                        <div class="fw-semibold">{{ $current->bank?->name ?: $current->bank_name }}@if($current->bank?->short_name) <span class="text-muted">({{ $current->bank->short_name }})</span>@endif</div>
                                        <span class="badge rounded-pill text-bg-success flex-shrink-0"><svg class="me-1" width="12" height="12" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m3 8 3.1 3.1L13 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>Rekening aktif</span>
                                    </div>
                                    <div>{{ $current->account_holder }}</div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mt-1"><span class="font-monospace">{{ $current->maskedNumber() }}</span><button class="btn btn-sm btn-outline-secondary py-0" type="button" data-reveal-account data-reveal-url="{{ route('workspace.hris.employees.payment-accounts.reveal', [$employee, $current]) }}"><svg class="me-1" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg>Lihat nomor</button></div>
                                </div>
                            @else
                                <div class="alert alert-secondary">Belum ada rekening aktif.</div>
                            @endif
                        @endif

                        @if($canUpdate)
                            <form method="POST" action="{{ route('workspace.hris.employees.payment-accounts.update', [$employee, $type]) }}" class="vstack gap-3" data-payment-account-form data-client-validation="payment-account" data-confirm="{{ $current ? 'Nomor rekening aktif untuk '.strtolower($label).' akan diganti dengan data yang baru. Versi sebelumnya tetap tersimpan di riwayat. Lanjutkan?' : 'Rekening '.strtolower($label).' akan disimpan sebagai rekening aktif. Lanjutkan?' }}" data-confirm-action="{{ $current ? 'Ganti rekening' : 'Simpan rekening' }}" @if($current) data-confirm-password="true" @endif novalidate>
                                @csrf
                                @method('PUT')
                                @error('password')<div class="alert alert-danger py-2 mb-0" role="alert">{{ $message }}</div>@enderror
                                @if($type === 'reimbursement' && $canEnrichReimbursement && isset($accounts['payroll']))
                                    @php($payrollSource = $accounts['payroll']->firstWhere('is_current', true))
                                    @if($payrollSource && $banks->contains('id', $payrollSource->bank_id))
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary" type="button" data-enrich-payroll-account data-enrich-url="{{ route('workspace.hris.employees.payment-accounts.enrich-payroll', $employee) }}">
                                                <svg class="me-1" width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 7h11a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7"/><path d="M16 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M10 12h7m-7 3h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                                Isi otomatis dari rekening gaji
                                            </button>
                                        </div>
                                    @endif
                                @endif
                                <div class="workspace-floating-field">
                                    @php($selectedBankId = old('bank_id', $canView ? $current?->bank_id : null))
                                    <div class="workspace-dropdown-multiselect dropdown" data-searchable-dropdown data-single-select="true" data-dropdown-empty-label="Pilih nama bank">
                                        <button type="button" id="bank-{{ $type }}" class="form-select text-start workspace-dropdown-trigger @error('bank_id') is-invalid @enderror" data-account-bank-trigger data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false" aria-describedby="bank-error-{{ $type }}"><span data-dropdown-label>@php($selectedBank = $banks->firstWhere('id', $selectedBankId))@if($selectedBank){{ $selectedBank->name }} ({{ $selectedBank->short_name }})@else{{ $current && !$current->bank_id ? 'Pilih bank (rekening lama: '.$current->bank_name.')' : 'Pilih nama bank' }}@endif</span></button>
                                        <div class="dropdown-menu workspace-dropdown-menu p-2 w-100">
                                            <input type="search" class="form-control form-control-sm mb-2" placeholder="Cari nama atau singkatan bank..." data-dropdown-search autocomplete="off">
                                            <div class="workspace-dropdown-options">
                                                @foreach($banks as $bank)
                                                    <label class="workspace-dropdown-option" data-dropdown-option>
                                                        <input type="radio" name="bank_id" value="{{ $bank->id }}" data-dropdown-item data-dropdown-value="{{ $bank->name }} ({{ $bank->short_name }})" @checked((string) $selectedBankId === (string) $bank->id)>
                                                        <span>{{ $bank->name }} <strong>({{ $bank->short_name }})</strong></span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <label class="form-label" for="bank-{{ $type }}">Nama bank</label>
                                    <div class="invalid-feedback @error('bank_id') d-block @enderror" id="bank-error-{{ $type }}" data-field-error="bank_id">@error('bank_id'){{ $message }}@enderror</div>
                                </div>
                                <div class="workspace-floating-field">
                                    <input class="form-control @error('account_holder') is-invalid @enderror" id="holder-{{ $type }}" name="account_holder" data-account-field="account_holder" maxlength="160" value="{{ old('account_holder', $canView ? $current?->account_holder : null) }}" placeholder=" " required aria-describedby="holder-error-{{ $type }}">
                                    <label class="form-label" for="holder-{{ $type }}">Nama pemilik rekening</label>
                                    <div class="invalid-feedback @error('account_holder') d-block @enderror" id="holder-error-{{ $type }}" data-field-error="account_holder">@error('account_holder'){{ $message }}@enderror</div>
                                </div>
                                <div class="workspace-floating-field">
                                    <input class="form-control font-monospace @error('account_number') is-invalid @enderror" id="number-{{ $type }}" name="account_number" data-account-field="account_number" maxlength="80" inputmode="numeric" pattern="[0-9 .-]+" value="{{ old('account_number') }}" placeholder=" " required aria-describedby="number-error-{{ $type }}">
                                    <label class="form-label" for="number-{{ $type }}">Nomor rekening</label>
                                    <div class="invalid-feedback @error('account_number') d-block @enderror" id="number-error-{{ $type }}" data-field-error="account_number">@error('account_number'){{ $message }}@enderror</div>
                                </div>
                                <div class="text-muted small">Menyimpan perubahan akan mengganti rekening aktif dan mempertahankan versi sebelumnya.</div>
                                <div><button class="btn btn-primary" type="submit">Simpan rekening {{ strtolower($label) }}</button></div>
                            </form>
                        @endif
                    </div>
                    @if($canView && $accounts[$type]->count() > 0)
                        <div class="table-responsive">
                            <table class="table workspace-data-table workspace-payment-history-table mb-0">
                                <thead><tr><th>TANGGAL</th><th>BANK</th><th>PEMILIK</th><th>NOMOR</th><th>STATUS REKENING</th></tr></thead>
                                <tbody>
                                    @foreach($accounts[$type] as $account)
                                        <tr>
                                            <td>{{ $account->created_at->format('d M Y H:i') }}</td>
                                            <td><div class="workspace-table-cell-wrap">{{ $account->bank?->name ?: $account->bank_name }}@if($account->bank?->short_name)<small class="d-block text-muted">({{ $account->bank->short_name }})</small>@endif</div></td>
                                            <td><div class="workspace-table-cell-wrap">{{ $account->account_holder }}</div></td>
                                            <td><div class="d-flex align-items-center gap-2 flex-wrap"><span class="font-monospace text-nowrap">{{ $account->maskedNumber() }}</span><button class="btn btn-sm btn-outline-secondary py-0" type="button" data-reveal-account data-reveal-url="{{ route('workspace.hris.employees.payment-accounts.reveal', [$employee, $account]) }}" aria-label="Lihat nomor rekening {{ $account->is_current ? 'aktif' : 'riwayat' }}"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg></button></div></td>
                                            <td>@if($account->is_current)<span class="badge rounded-pill text-bg-success">Aktif</span>@else<span class="badge rounded-pill text-bg-light border text-secondary">Diganti</span>@endif</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            </div>
        @endforeach
    </div>
</div>
<div class="modal fade" id="payment-account-security-modal" tabindex="-1" aria-labelledby="payment-account-security-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <form id="payment-account-reveal-form" novalidate>
                <div class="modal-header">
                    <div><div class="text-muted small">VERIFIKASI KEAMANAN</div><h2 class="modal-title fs-5" id="payment-account-security-title" data-reveal-title>Lihat nomor rekening</h2></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted" data-reveal-message>Masukkan password akun Anda untuk membuka nomor rekening ini.</p>
                    <label class="form-label" for="payment-account-reveal-password">Password akun</label>
                    <input class="form-control" type="password" id="payment-account-reveal-password" name="password" autocomplete="current-password" required>
                    <div class="invalid-feedback" data-reveal-password-error></div>
                    <div class="alert alert-light border mt-3 mb-0 d-none" data-revealed-account-wrap>
                        <div class="small text-muted mb-1">Nomor rekening</div>
                        <code class="fs-6 user-select-all" data-revealed-account></code>
                        <div class="small text-muted mt-2">Nomor disembunyikan kembali otomatis setelah 20 detik.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" data-reveal-submit>Verifikasi &amp; lihat</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const messages = {
        bank_id: 'Pilih nama bank dari daftar master.',
        account_holder: 'Nama pemilik rekening wajib diisi (maksimal 160 karakter).',
        account_number: 'Masukkan nomor rekening yang valid dengan 6 sampai 34 digit.'
    };

    document.querySelectorAll('[data-payment-account-form]').forEach((form) => {
        const fields = [...form.querySelectorAll('[data-account-field]')];
        const bankOptions = [...form.querySelectorAll('input[name="bank_id"]')];
        const bankTrigger = form.querySelector('[data-bs-toggle="dropdown"]');
        const bankFeedback = form.querySelector('[data-field-error="bank_id"]');
        const showBankError = (message) => {
            bankTrigger?.classList.toggle('is-invalid', Boolean(message));
            bankTrigger?.setAttribute('aria-invalid', message ? 'true' : 'false');
            if (bankFeedback) {
                bankFeedback.textContent = message;
                bankFeedback.classList.toggle('d-block', Boolean(message));
            }
        };
        const validate = (field) => {
            const name = field.dataset.accountField;
            const value = field.value.trim();
            let message = '';

            if (!value) message = name === 'bank_id' ? messages.bank_id : `${name === 'account_holder' ? 'Nama pemilik rekening' : 'Nomor rekening'} wajib diisi.`;
            else if (field.maxLength >= 0 && value.length > Number(field.maxLength)) message = messages[name];
            else if (name === 'account_number') {
                const digits = value.replace(/\D/g, '');
                if (!/^[0-9 .-]+$/.test(value) || digits.length < 6 || digits.length > 34) message = messages.account_number;
            }

            field.setCustomValidity(message);
            field.classList.toggle('is-invalid', Boolean(message));
            field.setAttribute('aria-invalid', message ? 'true' : 'false');
            const feedback = form.querySelector(`[data-field-error="${name}"]`);
            if (feedback) {
                feedback.textContent = message;
                feedback.classList.toggle('d-block', Boolean(message));
            }
            return !message;
        };

        fields.forEach((field) => {
            field.addEventListener('input', () => validate(field));
            field.addEventListener('blur', () => validate(field));
        });

        bankOptions.forEach((option) => option.addEventListener('change', () => showBankError('')));

        form.addEventListener('submit', (event) => {
            const invalid = fields.filter((field) => !validate(field));
            if (!bankOptions.some((option) => option.checked)) {
                showBankError(messages.bank_id);
                invalid.unshift(bankTrigger);
            }
            if (invalid.length) {
                event.preventDefault();
                invalid[0].focus();
            }
        });
    });

    const revealModalElement = document.getElementById('payment-account-security-modal');
    const revealForm = document.getElementById('payment-account-reveal-form');
    if (revealModalElement && revealForm && window.bootstrap) {
        const revealModal = bootstrap.Modal.getOrCreateInstance(revealModalElement);
        const passwordInput = document.getElementById('payment-account-reveal-password');
        const passwordError = revealForm.querySelector('[data-reveal-password-error]');
        const revealedWrap = revealForm.querySelector('[data-revealed-account-wrap]');
        const revealedNumber = revealForm.querySelector('[data-revealed-account]');
        const submitButton = revealForm.querySelector('[data-reveal-submit]');
        const message = revealForm.querySelector('[data-reveal-message]');
        const title = revealForm.querySelector('[data-reveal-title]');
        let revealUrl = '';
        let revealMode = 'reveal';
        let enrichmentForm = null;
        let replacementForm = null;
        let confirmedSubmit = null;
        let hideNumberTimer;

        document.querySelectorAll('[data-reveal-account]').forEach((button) => button.addEventListener('click', () => {
            revealUrl = button.dataset.revealUrl;
            revealMode = 'reveal';
            enrichmentForm = null;
            replacementForm = null;
            confirmedSubmit = null;
            title.textContent = 'Lihat nomor rekening';
            submitButton.textContent = 'Verifikasi & lihat';
            message.textContent = 'Masukkan password akun Anda untuk membuka nomor rekening ini.';
            passwordInput.value = '';
            passwordInput.classList.remove('is-invalid');
            passwordError.textContent = '';
            revealedNumber.textContent = '';
            revealedWrap.classList.add('d-none');
            submitButton.disabled = false;
            submitButton.classList.remove('d-none');
            revealModal.show();
        }));

        document.querySelectorAll('[data-enrich-payroll-account]').forEach((button) => button.addEventListener('click', () => {
            revealUrl = button.dataset.enrichUrl;
            revealMode = 'enrich';
            enrichmentForm = button.closest('form');
            replacementForm = null;
            confirmedSubmit = null;
            title.textContent = 'Isi otomatis dari rekening gaji';
            message.textContent = 'Masukkan password akun Anda untuk mengisi data reimbursement dari rekening gaji.';
            passwordInput.value = '';
            passwordInput.classList.remove('is-invalid');
            passwordError.textContent = '';
            revealedNumber.textContent = '';
            revealedWrap.classList.add('d-none');
            submitButton.textContent = 'Verifikasi & isi otomatis';
            submitButton.disabled = false;
            submitButton.classList.remove('d-none');
            revealModal.show();
        }));

        document.querySelectorAll('[data-payment-account-form][data-confirm-password="true"]').forEach((form) => form.addEventListener('workspace:before-confirmed-submit', (event) => {
            event.preventDefault();
            revealMode = 'replace';
            replacementForm = form;
            enrichmentForm = null;
            confirmedSubmit = event.detail?.submit;
            revealUrl = '';
            title.textContent = 'Konfirmasi keamanan';
            message.textContent = 'Nomor rekening aktif akan diganti. Masukkan password akun Anda untuk melanjutkan.';
            passwordInput.value = '';
            passwordInput.classList.remove('is-invalid');
            passwordError.textContent = '';
            revealedNumber.textContent = '';
            revealedWrap.classList.add('d-none');
            submitButton.textContent = 'Simpan penggantian';
            submitButton.disabled = false;
            submitButton.classList.remove('d-none');
            revealModal.show();
        }));

        revealForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            passwordInput.classList.remove('is-invalid');
            passwordError.textContent = '';
            revealedWrap.classList.add('d-none');
            if (!passwordInput.value) {
                passwordInput.classList.add('is-invalid');
                passwordError.textContent = 'Masukkan password akun Anda.';
                passwordInput.focus();
                return;
            }

            if (revealMode === 'replace') {
                if (!replacementForm) return;
                let passwordField = replacementForm.querySelector('input[name="password"][data-confirm-password-value]');
                if (!passwordField) {
                    passwordField = document.createElement('input');
                    passwordField.type = 'hidden';
                    passwordField.name = 'password';
                    passwordField.dataset.confirmPasswordValue = 'true';
                    replacementForm.append(passwordField);
                }
                passwordField.value = passwordInput.value;
                passwordInput.value = '';
                revealModal.hide();
                window.dispatchEvent(new CustomEvent('workspace:show-loading', { detail: 'Menyimpan rekening...' }));
                if (confirmedSubmit) confirmedSubmit();
                else replacementForm.submit();
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Memverifikasi...';
            try {
                const response = await fetch(revealUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': revealForm.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ password: passwordInput.value })
                });
                const payload = await response.json();
                if (!response.ok) {
                    passwordInput.classList.add('is-invalid');
                    passwordError.textContent = payload.errors?.password?.[0] || (response.status === 429 ? 'Terlalu banyak percobaan. Coba lagi sebentar.' : 'Verifikasi gagal. Silakan coba lagi.');
                    passwordInput.focus();
                    return;
                }

                passwordInput.value = '';
                passwordInput.classList.remove('is-invalid');
                if (revealMode === 'enrich') {
                    const bankOption = enrichmentForm?.querySelector(`input[name="bank_id"][value="${CSS.escape(payload.bank_id || '')}"]`);
                    if (bankOption) {
                        bankOption.checked = true;
                        bankOption.dispatchEvent(new Event('change', { bubbles: true }));
                        enrichmentForm.querySelector('[data-dropdown-label]').textContent = bankOption.dataset.dropdownValue;
                    }
                    ['account_holder', 'account_number'].forEach((name) => {
                        const field = enrichmentForm?.querySelector(`[data-account-field="${name}"]`);
                        if (!field) return;
                        field.value = payload[name] || '';
                        field.dispatchEvent(new Event('input', { bubbles: true }));
                    });
                    revealModal.hide();
                    return;
                }

                revealedNumber.textContent = payload.account_number || '';
                revealedWrap.classList.remove('d-none');
                submitButton.classList.add('d-none');
                clearTimeout(hideNumberTimer);
                hideNumberTimer = setTimeout(() => {
                    revealedNumber.textContent = '';
                    revealedWrap.classList.add('d-none');
                    submitButton.classList.remove('d-none');
                    submitButton.textContent = 'Verifikasi & lihat';
                    submitButton.disabled = false;
                }, 20000);
            } catch {
                passwordError.textContent = 'Nomor rekening tidak dapat dimuat. Periksa koneksi lalu coba lagi.';
            } finally {
                if (!submitButton.classList.contains('d-none')) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Verifikasi & lihat';
                }
            }
        });

        revealModalElement.addEventListener('hidden.bs.modal', () => {
            clearTimeout(hideNumberTimer);
            passwordInput.value = '';
            passwordInput.classList.remove('is-invalid');
            passwordError.textContent = '';
            revealedNumber.textContent = '';
            revealedWrap.classList.add('d-none');
            submitButton.classList.remove('d-none');
            submitButton.disabled = false;
            submitButton.textContent = 'Verifikasi & lihat';
            revealUrl = '';
            enrichmentForm = null;
            replacementForm = null;
            confirmedSubmit = null;
            revealMode = 'reveal';
            title.textContent = 'Lihat nomor rekening';
            submitButton.textContent = 'Verifikasi & lihat';
            message.textContent = 'Masukkan password akun Anda untuk membuka nomor rekening ini.';
        });
    }
});
</script>
@endsection
