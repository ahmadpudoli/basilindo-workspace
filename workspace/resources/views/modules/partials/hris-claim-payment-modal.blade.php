<div class="modal fade" id="claimPaymentModal" tabindex="-1" aria-labelledby="claimPaymentModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <div>
                    <div class="small text-uppercase text-secondary fw-semibold">Reimbursement</div>
                    <h2 class="modal-title fs-5" id="claimPaymentModalTitle">Catat pembayaran</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="claimPaymentForm" method="post" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="modal-body">
                    <div id="claimPaymentMessage" class="alert alert-danger py-2 d-none" role="status" aria-live="polite"></div>
                    <div class="rounded-3 border bg-light p-3 mb-3">
                        <div id="claimPaymentRequestNumber" class="fw-semibold">—</div>
                        <div id="claimPaymentEmployee" class="small text-secondary"></div>
                        <div class="mt-2 small text-secondary">Nominal pengajuan</div>
                        <div id="claimPaymentExpectedAmount" class="fw-bold"></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="claimPaymentMethod" class="form-label">Metode pembayaran</label>
                            <select id="claimPaymentMethod" name="payment_method" class="form-select" required>
                                <option value="transfer">Transfer bank</option>
                                <option value="cash">Tunai</option>
                                <option value="petty_cash">Kas kecil</option>
                            </select>
                            <div class="invalid-feedback d-block" data-payment-error="payment_method"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="claimPaymentPaidTo" class="form-label">Dibayarkan kepada</label>
                            <input id="claimPaymentPaidTo" name="paid_to" class="form-control" maxlength="150" required readonly>
                            <div class="invalid-feedback d-block" data-payment-error="paid_to"></div>
                        </div>
                        <div class="col-md-6" data-payment-bank-fields>
                            <label for="claimPaymentBankName" class="form-label">Nama bank <span class="text-secondary">(untuk transfer)</span></label>
                            <input id="claimPaymentBankName" name="bank_name" class="form-control" maxlength="100" readonly>
                            <div class="invalid-feedback d-block" data-payment-error="bank_name"></div>
                        </div>
                        <div class="col-md-6" data-payment-bank-fields>
                            <label for="claimPaymentBankAccount" class="form-label">Nomor rekening <span class="text-secondary">(untuk transfer)</span></label>
                            <input id="claimPaymentBankAccount" name="bank_account" class="form-control" maxlength="100" readonly>
                            <div class="invalid-feedback d-block" data-payment-error="bank_account"></div>
                        </div>
                        <div class="col-12 d-none" data-payment-transfer-notice><div class="alert alert-info py-2 mb-0 small">Rekening reimbursement aktif karyawan akan digunakan otomatis. Nomor rekening disimpan dari data karyawan dan tidak dapat diubah pada form pembayaran.</div></div>
                        <div class="col-12 d-none" data-payment-transfer-unavailable><div class="alert alert-warning py-2 mb-0 small">Karyawan belum memiliki rekening reimbursement aktif. Pembayaran transfer tidak dapat dilanjutkan sampai rekening disimpan pada data karyawan.</div></div>
                        <div class="col-md-6 d-none" data-payment-petty-cash-source-field>
                            <label for="claimPaymentPettyCashSource" class="form-label">Sumber kas kecil <span class="text-danger">*</span></label>
                            <input id="claimPaymentPettyCashSource" name="petty_cash_source" class="form-control" maxlength="150" placeholder="Contoh: Kas kecil Kantor Jakarta">
                            <div class="invalid-feedback d-block" data-payment-error="petty_cash_source"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="claimPaymentAmount" class="form-label">Nominal dibayar</label>
                            <input id="claimPaymentAmount" name="paid_amount_display" type="text" inputmode="decimal" autocomplete="off" class="form-control" required aria-describedby="claimPaymentAmountNotice claimPaymentAmountError">
                            <input id="claimPaymentAmountValue" name="paid_amount" type="hidden">
                            <div id="claimPaymentAmountNotice" class="form-text">Masukkan nominal dalam format Indonesia. Nominal berbeda dari pengajuan akan meminta konfirmasi.</div>
                            <div id="claimPaymentAmountDifference" class="alert alert-warning py-2 px-3 mt-2 mb-0 small d-none" role="status" aria-live="polite"></div>
                            <div id="claimPaymentAmountError" class="invalid-feedback d-block" data-payment-error="paid_amount"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="claimPaymentDate" class="form-label">Tanggal pembayaran <span class="text-danger">*</span></label>
                            <input id="claimPaymentDate" name="paid_at" type="date" value="{{ now(config('app.timezone'))->toDateString() }}" class="form-control" required aria-required="true">
                            <div class="invalid-feedback d-block" data-payment-error="paid_at"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="claimPaymentReference" class="form-label">Nomor referensi <span class="text-secondary">(opsional)</span></label>
                            <input id="claimPaymentReference" name="payment_reference" class="form-control" maxlength="150">
                            <div class="invalid-feedback d-block" data-payment-error="payment_reference"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="claimPaymentProof" class="form-label">ID bukti pembayaran <span class="text-secondary">(opsional)</span></label>
                            <input id="claimPaymentProof" name="payment_proof_id" class="form-control" maxlength="255">
                            <div class="invalid-feedback d-block" data-payment-error="payment_proof_id"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Bukti pembayaran <span class="text-danger">*</span></label>
                            <input id="claimPaymentFile" name="payment_proof_file" type="file" accept="image/jpeg,image/png,image/webp,application/pdf" class="visually-hidden" aria-label="Pilih bukti pembayaran" required aria-required="true">
                            <div class="claim-payment-upload-zone rounded-3 border border-2 border-dashed p-3 text-center" data-payment-upload-zone>
                                <div class="text-secondary small mb-2">Unggah gambar atau PDF. Gambar akan dikompresi sebelum dikirim.</div>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-payment-file-pick>Pilih file</button>
                                <div class="small text-secondary mt-2">Maksimal 5 MB per file. Gambar dikompresi sebelum dikirim.</div>
                            </div>
                            <div class="claim-payment-file-preview d-none align-items-center gap-3 rounded-3 border p-2 mt-2" data-payment-file-preview>
                                <img class="claim-payment-file-thumbnail d-none rounded-2" data-payment-file-thumbnail alt="Pratinjau bukti pembayaran">
                                <div class="claim-payment-file-pdf d-none rounded-2 bg-light text-danger fw-bold" data-payment-file-pdf aria-hidden="true">PDF</div>
                                <div class="min-w-0 flex-grow-1"><div class="fw-semibold text-truncate" data-payment-file-name></div><div class="small text-secondary" data-payment-file-meta></div></div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-payment-file-pick>Ganti</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-payment-file-remove aria-label="Hapus file bukti">Hapus</button>
                            </div>
                            <div class="form-text" data-payment-file-status>Format yang didukung: JPEG, PNG, WebP, atau PDF.</div>
                            <div class="invalid-feedback d-block" data-payment-error="payment_proof_file"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-primary d-none" data-payment-next>Catat pembayaran berikutnya</button>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan pembayaran</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
