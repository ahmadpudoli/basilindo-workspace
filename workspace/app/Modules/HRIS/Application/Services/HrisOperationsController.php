<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\ApprovalEvent;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\LeaveRequest;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HrisOperationsController extends Controller
{
    public function approvals(Request $request): View
    {
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('view_hris_approvals'), 403);
        $search = trim($request->string('q')->toString());
        $typeFilter = in_array($request->string('type')->toString(), ['leave', 'overtime', 'claim'], true) ? $request->string('type')->toString() : '';
        $stepFilter = in_array($request->string('step')->toString(), ['manager', 'hr', 'finance', 'final'], true) ? $request->string('step')->toString() : '';
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $sort = in_array($request->string('sort')->toString(), ['type', 'employee', 'created_at'], true) ? $request->string('sort')->toString() : 'created_at';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $filterEmployee = static function ($query) use ($search): void {
            if ($search !== '') {
                $query->whereHas('employee', fn ($employee) => $employee->where('full_name', 'ilike', '%'.$search.'%')->orWhere('employee_number', 'ilike', '%'.$search.'%'));
            }
        };
        $manager = Employee::query()->where('user_id', $request->user()->getKey())->first();
        $subordinateIds = $manager ? Employee::query()->where('manager_id', $manager->getKey())->pluck('id') : collect();
        $leaveApprovals = LeaveRequest::query()->with(['employee', 'dates'])->whereIn('employee_id', $subordinateIds)->where('status', 'pending_manager')->when($search !== '', $filterEmployee)->when($typeFilter === '' || $typeFilter === 'leave', fn ($query) => $query)->when($typeFilter === 'overtime' || $typeFilter === 'claim' || $stepFilter === 'hr', fn ($query) => $query->whereRaw('1 = 0'))->latest()->get();
        $overtimeApprovals = OvertimeRequest::query()->with(['employee', 'entries'])->whereIn('employee_id', $subordinateIds)->where('status', 'pending_manager')->when($search !== '', $filterEmployee)->when($typeFilter === '' || $typeFilter === 'overtime', fn ($query) => $query)->when($typeFilter === 'leave' || $typeFilter === 'claim' || $stepFilter === 'hr', fn ($query) => $query->whereRaw('1 = 0'))->latest()->get();
        $claimApprovals = Claim::query()->with('employee')->whereIn('employee_id', $subordinateIds)->where('status', 'pending_manager')->when($search !== '', $filterEmployee)->when($typeFilter === '' || $typeFilter === 'claim', fn ($query) => $query)->when($typeFilter === 'leave' || $typeFilter === 'overtime' || $stepFilter === 'hr', fn ($query) => $query->whereRaw('1 = 0'))->latest()->get();
        $hrCompanyIds = HrisWorkflowApprover::query()->where('workflow_key', 'leave_sick')->where('step', 'hr')->where('user_id', $request->user()->getKey())->where('is_active', true)->pluck('company_id');
        $overtimeHrCompanyIds = $hrCompanyIds;
        $financeCompanyIds = HrisWorkflowApprover::query()->where('workflow_key', 'claim')->where('step', 'finance')->where('user_id', $request->user()->getKey())->where('is_active', true)->pluck('company_id');
        $finalCompanyIds = HrisWorkflowApprover::query()->where('workflow_key', 'claim')->where('step', 'final')->where('user_id', $request->user()->getKey())->where('is_active', true)->pluck('company_id');
        $leaveHrApprovals = LeaveRequest::query()->with(['employee', 'dates'])->whereIn('company_id', $hrCompanyIds)->where('status', 'pending_hr')->when($search !== '', $filterEmployee)->when($typeFilter === '' || $typeFilter === 'leave', fn ($query) => $query)->when($typeFilter === 'overtime' || $typeFilter === 'claim' || $stepFilter === 'manager', fn ($query) => $query->whereRaw('1 = 0'))->latest()->get();
        $overtimeHrApprovals = OvertimeRequest::query()->with(['employee', 'entries'])->whereIn('company_id', $overtimeHrCompanyIds)->where('status', 'pending_hr')->when($search !== '', $filterEmployee)->when($typeFilter === '' || $typeFilter === 'overtime', fn ($query) => $query)->when($typeFilter === 'leave' || $typeFilter === 'claim' || $stepFilter === 'manager', fn ($query) => $query->whereRaw('1 = 0'))->latest()->get();
        $financeApprovals = Claim::query()->with('employee')->whereIn('company_id', $financeCompanyIds)->where('status', 'pending_finance')->when($search !== '', $filterEmployee)->when($typeFilter === '' || $typeFilter === 'claim', fn ($query) => $query)->when($typeFilter === 'leave' || $typeFilter === 'overtime' || $stepFilter === 'manager', fn ($query) => $query->whereRaw('1 = 0'))->latest()->get();
        $finalApprovals = Claim::query()->with('employee')->whereIn('company_id', $finalCompanyIds)->where('status', 'pending_final')->when($search !== '', $filterEmployee)->when($typeFilter === '' || $typeFilter === 'claim', fn ($query) => $query)->when($typeFilter === 'leave' || $typeFilter === 'overtime' || $stepFilter === 'manager', fn ($query) => $query->whereRaw('1 = 0'))->latest()->get();
        $approvalRows = collect()
            ->concat($leaveApprovals->map(fn ($item) => ['item' => $item, 'type' => 'leave', 'step' => 'manager', 'stage' => 'Atasan']))
            ->concat($overtimeApprovals->map(fn ($item) => ['item' => $item, 'type' => 'overtime', 'step' => 'manager', 'stage' => 'Atasan']))
            ->concat($claimApprovals->map(fn ($item) => ['item' => $item, 'type' => 'claim', 'step' => 'manager', 'stage' => 'Atasan']))
            ->concat($leaveHrApprovals->map(fn ($item) => ['item' => $item, 'type' => 'leave', 'step' => 'hr', 'stage' => 'HR']))
            ->concat($overtimeHrApprovals->map(fn ($item) => ['item' => $item, 'type' => 'overtime', 'step' => 'hr', 'stage' => 'HR']))
            ->concat($financeApprovals->map(fn ($item) => ['item' => $item, 'type' => 'claim', 'step' => 'finance', 'stage' => 'Finance']))
            ->concat($finalApprovals->map(fn ($item) => ['item' => $item, 'type' => 'claim', 'step' => 'final', 'stage' => 'Approver final']))
            ->values();
        if ($stepFilter !== '') $approvalRows = $approvalRows->filter(fn (array $row): bool => $row['step'] === $stepFilter)->values();
        $supportingDisk = Storage::disk(config('filesystems.default', 's3'));
        $approvalRows = $approvalRows->map(function (array $row) use ($supportingDisk): array {
            $objectKey = $row['item']->supporting_document_id;
            $row['has_supporting_document'] = filled($objectKey) && $supportingDisk->exists((string) $objectKey);
            $row['supporting_mime_type'] = $row['has_supporting_document'] ? ($supportingDisk->mimeType((string) $objectKey) ?: 'application/octet-stream') : null;
            $row['can_preview_supporting_document'] = $row['supporting_mime_type'] === 'application/pdf' || str_starts_with((string) $row['supporting_mime_type'], 'image/');

            return $row;
        });

        $approvalRows = $approvalRows->sortBy(fn (array $row) => match ($sort) {
            'type' => $row['type'],
            'employee' => mb_strtolower((string) ($row['item']->employee?->full_name ?? '')),
            default => $row['item']->created_at?->timestamp ?? 0,
        }, SORT_NATURAL, $direction !== 'asc')->values();
        $page = LengthAwarePaginator::resolveCurrentPage('page');
        $approvalRows = new LengthAwarePaginator($approvalRows->forPage($page, $perPage)->values(), $approvalRows->count(), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

        return view('modules.hris-approvals-table', compact('approvalRows', 'search', 'typeFilter', 'stepFilter', 'perPage', 'sort', 'direction'));
    }

    public function approvalDetail(Request $request, string $type, string $id, string $step): View
    {
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('view_hris_approvals'), 403);

        $model = match ($type) {
            'leave' => LeaveRequest::query()->with(['employee.company', 'employee.manager', 'dates', 'approvalEvents'])->findOrFail($id),
            'overtime' => OvertimeRequest::query()->with(['employee.company', 'employee.manager', 'entries', 'approvalEvents'])->findOrFail($id),
            'claim' => Claim::query()->with(['employee.company', 'employee.manager', 'expenseItems'])->findOrFail($id),
            default => abort(404),
        };

        abort_unless($request->user()->hasRole('super_admin') || $request->user()->companies()->whereKey($model->company_id)->exists(), 403);
        if ($step === 'manager') {
            abort_unless($request->user()->hasRole('super_admin') || (int) $model->employee?->manager?->user_id === (int) $request->user()->getKey(), 403);
        } else {
            $workflowKey = in_array($type, ['leave', 'overtime'], true) ? 'leave_sick' : $type;
            abort_unless($request->user()->hasRole('super_admin') || in_array((int) $request->user()->getKey(), HrisWorkflowApprover::approverIds((int) $model->company_id, $workflowKey, $step), true), 403);
        }

        $expectedStatus = match ($step) { 'manager' => 'pending_manager', 'hr' => 'pending_hr', 'finance' => 'pending_finance', 'final' => 'pending_final', default => null };
        abort_unless($model->status === $expectedStatus, 404);
        $supportingDisk = Storage::disk(config('filesystems.default', 's3'));
        $hasSupportingDocument = filled($model->supporting_document_id) && $supportingDisk->exists((string) $model->supporting_document_id);
        $supportingMimeType = $hasSupportingDocument ? ($supportingDisk->mimeType((string) $model->supporting_document_id) ?: 'application/octet-stream') : null;
        $canPreviewSupportingDocument = $supportingMimeType === 'application/pdf' || str_starts_with((string) $supportingMimeType, 'image/');
        $typeLabel = match ($type) {
            'leave' => $model->type === 'sick' ? 'Sakit' : 'Cuti',
            'overtime' => 'Lembur',
            default => $model->type === 'cash_advance' ? 'Cash advance' : 'Reimbursement',
        };
        $stage = match ($step) { 'manager' => 'Atasan', 'hr' => 'HR', 'finance' => 'Finance', 'final' => 'Approver final', default => ucfirst($step) };

        return view('modules.hris-approval-detail', compact('model', 'type', 'step', 'stage', 'typeLabel', 'hasSupportingDocument', 'supportingMimeType', 'canPreviewSupportingDocument'));
    }

    public function overtime(Request $request): View
    {
        $summaryView = $request->string('view')->toString() === 'summary';
        if ($summaryView) {
            abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('view_hris_overtime_summary'), 403);
        } else {
            Gate::authorize('viewAny', OvertimeRequest::class);
        }

        $items = OvertimeRequest::query()
            ->when($summaryView && ! $request->user()->hasRole('super_admin'), fn ($query) => $query->whereIn('company_id', $request->user()->companies()->pluck('companies.id')))
            ->when(! $summaryView, fn ($query) => $query->visibleTo($request->user()))
            ->with('employee')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('modules.hris-overtime', compact('items', 'summaryView'));
    }

    public function overtimeSummary(Request $request): View
    {
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('view_hris_overtime_summary'), 403);
        $companyScope = $request->user()->hasRole('super_admin') ? null : $request->user()->companies()->pluck('companies.id');
        $items = OvertimeRequest::query()
            ->when($companyScope !== null, fn ($query) => $query->whereIn('company_id', $companyScope))
            ->with(['employee.company'])
            ->latest()
            ->paginate(10)
            ->withQueryString();
        $metricsQuery = OvertimeRequest::query()->when($companyScope !== null, fn ($query) => $query->whereIn('company_id', $companyScope));
        $metrics = [
            'total' => (clone $metricsQuery)->count(),
            'pending' => (clone $metricsQuery)->whereIn('status', ['pending_manager', 'pending_hr'])->count(),
            'approved_minutes' => (int) (clone $metricsQuery)->where('status', 'approved')->sum('duration_minutes'),
        ];

        return view('modules.hris-overtime-summary', compact('items', 'metrics'));
    }
    public function overtimeCreate(Request $request): View { Gate::authorize('create', OvertimeRequest::class); $requestNumber = OvertimeRequest::generateRequestNumber(); $request->session()->put('hris.request_number.overtime', $requestNumber); return view('modules.hris-overtime-form', ['employees' => Employee::query()->visibleTo($request->user())->where('status', 'active')->orderBy('full_name')->get(), 'requestNumber' => $requestNumber]); }
    public function overtimeStore(OvertimeRequestForm $request, OvertimeService $service): RedirectResponse { Gate::authorize('create', OvertimeRequest::class); $employee = Employee::query()->visibleTo($request->user())->findOrFail($request->validated('employee_id')); $data = $request->validated(); $data['request_number'] = $request->session()->pull('hris.request_number.overtime', null); $service->submit($employee, $data, $request->user()->id); return redirect()->route('workspace.hris.overtime')->with('status', 'Pengajuan lembur berhasil dikirim.'); }

    public function claims(Request $request): View
    {
        $type = in_array($request->string('type')->toString(), ['reimbursement', 'cash_advance'], true) ? $request->string('type')->toString() : null;
        $summaryView = $request->string('view')->toString() === 'summary';
        if ($summaryView) {
            abort_unless($type !== null, 404);
            $permission = $type === 'reimbursement' ? 'view_hris_reimbursement_summary' : 'view_hris_cash_advance_summary';
            abort_unless($request->user()->hasRole('super_admin') || $request->user()->can($permission), 403);
        } else {
            Gate::authorize('viewAny', Claim::class);
        }
        $status = in_array($request->string('status')->toString(), ['draft', 'submitted', 'pending_manager', 'pending_finance', 'pending_final', 'pending_payment', 'paid', 'completed', 'awaiting_accountability', 'approved', 'rejected'], true) ? $request->string('status')->toString() : null;
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $sort = in_array($request->string('sort')->toString(), ['type', 'amount', 'status', 'created_at'], true) ? $request->string('sort')->toString() : 'created_at';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $items = Claim::query()
            ->when($summaryView && ! $request->user()->hasRole('super_admin'), fn ($query) => $query->whereIn('company_id', $request->user()->companies()->pluck('companies.id')))
            ->when(! $summaryView, fn ($query) => $query->visibleTo($request->user()))
            ->when(! $summaryView, fn ($query) => $query->where(fn ($draftQuery) => $draftQuery->where('status', '!=', 'draft')->orWhere('created_by', $request->user()->getKey())))
            ->with('employee')
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
        return view('modules.hris-claims-enhanced', compact('items', 'type', 'status', 'perPage', 'sort', 'direction', 'summaryView'));
    }
    public function claimCreate(Request $request): View
    {
        Gate::authorize('create', Claim::class);
        $employee = Employee::query()->visibleTo($request->user())->where('user_id', $request->user()->getKey())->where('status', 'active')->with(['company', 'manager', 'organizationUnit'])->firstOrFail();
        $requestNumber = 'Nomor dibuat saat dikirim';
        $financeApprovers = HrisWorkflowApprover::query()->with('user')->forWorkflow((int) $employee->company_id, 'claim', 'finance')->get();
        $finalApprovers = HrisWorkflowApprover::query()->with('user')->forWorkflow((int) $employee->company_id, 'claim', 'final')->get();
        return view('modules.hris-claim-form', ['employee' => $employee, 'employees' => collect([$employee]), 'financeApprovers' => $financeApprovers, 'finalApprovers' => $finalApprovers, 'selectedType' => $request->string('type')->toString(), 'requestNumber' => $requestNumber, 'isDraft' => false, 'claimData' => [], 'draftItems' => [], 'claimAction' => route('workspace.hris.claims.store')]);
    }

    public function claimStore(ClaimRequestForm $request, ClaimService $service): RedirectResponse
    {
        Gate::authorize('create', Claim::class);
        $employee = Employee::query()->visibleTo($request->user())->where('user_id', $request->user()->getKey())->where('status', 'active')->firstOrFail();
        $data = $request->validated();
        if ($request->input('action') === 'draft') {
            $service->saveDraft($employee, $data, (int) $request->user()->getKey());
            return redirect()->route('workspace.hris.claims')->with('status', 'Draft pengajuan reimbursement berhasil disimpan.');
        }
        $data['request_number'] = $request->session()->pull('hris.request_number.claim', null);
        $service->submit($employee, $data, $request->user()->id);
        return redirect()->route('workspace.hris.claims')->with('status', 'Pengajuan claim berhasil dikirim.');
    }

    public function claimEdit(Request $request, Claim $claim): View
    {
        Gate::authorize('update', $claim);
        $claim->load(['employee.company', 'employee.manager', 'employee.organizationUnit', 'expenseItems']);
        $employee = $claim->employee;
        $financeApprovers = HrisWorkflowApprover::query()->with('user')->forWorkflow((int) $employee->company_id, 'claim', 'finance')->get();
        $finalApprovers = HrisWorkflowApprover::query()->with('user')->forWorkflow((int) $employee->company_id, 'claim', 'final')->get();
        $claimData = ['department' => $claim->department, 'project_id' => $claim->project_id, 'po_number' => $claim->po_number, 'purpose' => $claim->purpose, 'type' => $claim->type];
        $draftItems = $claim->expenseItems->map(fn ($item): array => ['receipt_date' => $item->receipt_date?->format('Y-m-d'), 'activity_date' => $item->activity_date?->format('Y-m-d'), 'category' => $item->category, 'description' => $item->description, 'currency' => $item->currency, 'amount' => $item->amount, 'attachment_reference' => $item->attachment_reference])->values();
        return view('modules.hris-claim-form', ['claim' => $claim, 'employee' => $employee, 'employees' => collect([$employee]), 'financeApprovers' => $financeApprovers, 'finalApprovers' => $finalApprovers, 'selectedType' => $claim->type, 'requestNumber' => 'Nomor dibuat saat dikirim', 'isDraft' => true, 'claimData' => $claimData, 'draftItems' => $draftItems, 'claimAction' => route('workspace.hris.claims.update', $claim)]);
    }

    public function claimUpdate(ClaimRequestForm $request, Claim $claim, ClaimService $service): RedirectResponse
    {
        Gate::authorize('update', $claim);
        $employee = $claim->employee()->where('user_id', $request->user()->getKey())->where('status', 'active')->firstOrFail();
        $data = $request->validated();
        if ($request->input('action') === 'draft') $service->updateDraft($claim, $employee, $data, (int) $request->user()->getKey());
        else $service->submitDraft($claim, $employee, $data, (int) $request->user()->getKey());
        return redirect()->route('workspace.hris.claims')->with('status', $request->input('action') === 'draft' ? 'Draft berhasil diperbarui.' : 'Pengajuan claim berhasil dikirim.');
    }

    public function claimDestroy(Request $request, Claim $claim): RedirectResponse
    {
        Gate::authorize('update', $claim);
        abort_unless($claim->status === 'draft', 404);
        $claim->delete();
        return redirect()->route('workspace.hris.claims')->with('status', 'Draft pengajuan reimbursement berhasil dihapus.');
    }

    public function claimPayment(Request $request, string $id): View
    {
        $this->authorizePayment($request);
        $claim = Claim::query()->with(['employee.company', 'expenseItems'])->findOrFail($id);
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->companies()->whereKey($claim->company_id)->exists(), 403);
        abort_unless($claim->status === 'pending_payment', 404);
        return view('modules.hris-claim-payment', compact('claim'));
    }

    public function claimPaymentStart(Request $request, string $id): RedirectResponse
    {
        $this->authorizePayment($request);
        $claim = Claim::query()->findOrFail($id);
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->companies()->whereKey($claim->company_id)->exists(), 403);
        abort_unless($claim->status === 'approved', 422, 'Pengajuan belum disetujui sepenuhnya.');
        $claim->update(['status' => 'pending_payment']);
        ApprovalEvent::create(['request_type' => 'claim', 'request_id' => $claim->getKey(), 'step' => 'payment', 'action' => 'pending_payment', 'actor_id' => $request->user()->getKey(), 'comment' => null]);
        return redirect()->route('workspace.hris.claims.payment', $claim->id);
    }

    public function claimPaymentStore(Request $request, string $id): RedirectResponse
    {
        $this->authorizePayment($request);
        $claim = Claim::query()->findOrFail($id);
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->companies()->whereKey($claim->company_id)->exists(), 403);
        abort_unless($claim->status === 'pending_payment', 422, 'Pengajuan belum berada pada tahap pembayaran.');
        $data = $request->validate([
            'payment_method' => ['required', 'in:transfer,cash,petty_cash'],
            'paid_to' => ['required', 'string', 'max:150'],
            'bank_name' => ['nullable', 'string', 'max:100', 'required_if:payment_method,transfer'],
            'bank_account' => ['nullable', 'string', 'max:100', 'required_if:payment_method,transfer'],
            'paid_amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'payment_reference' => ['nullable', 'string', 'max:150'],
            'payment_proof_id' => ['nullable', 'string', 'max:255'],
        ]);
        abort_unless((float) $data['paid_amount'] === (float) $claim->amount, 422, 'Nominal pembayaran harus sama dengan total pengajuan.');
        $claim->update($data + ['paid_by' => $request->user()->getKey(), 'status' => 'paid']);
        return redirect()->route('workspace.hris.claims')->with('status', 'Pembayaran reimbursement berhasil dicatat.');
    }

    public function claimComplete(Request $request, string $id): RedirectResponse
    {
        $this->authorizePayment($request);
        $claim = Claim::query()->findOrFail($id);
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->companies()->whereKey($claim->company_id)->exists(), 403);
        abort_unless($claim->status === 'paid', 422, 'Pengajuan belum berstatus dibayar.');
        $claim->update(['status' => 'completed', 'completed_at' => now()]);
        return back()->with('status', 'Pengajuan reimbursement ditandai selesai.');
    }

    private function authorizePayment(Request $request): void
    {
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('process_hris_payment'), 403);
    }

    public function approve(Request $request, string $type, string $id, string $step): RedirectResponse
    {
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('approve_hris_request'), 403);
        $model = match ($type) { 'leave' => LeaveRequest::findOrFail($id), 'overtime' => OvertimeRequest::findOrFail($id), 'claim' => Claim::findOrFail($id), default => abort(404) };
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->companies()->whereKey($model->company_id)->exists(), 403);
        $action = $request->input('action', 'approve');
        if ($step === 'manager') {
            $managerUserId = $model->employee?->manager?->user_id;
            abort_unless($request->user()->hasRole('super_admin') || (int) $managerUserId === (int) $request->user()->getKey(), 403);
        }
        $request->validate([
            'action' => ['required', 'in:approve,reject,request_revision'],
            'comment' => ['nullable', 'string', 'max:2000', 'required_if:action,request_revision,reject'],
            'revision_type' => ['nullable', 'in:substantive,administrative', 'required_if:action,request_revision'],
        ], ['comment.required_if' => 'Alasan penolakan atau revisi wajib diisi.', 'revision_type.required_if' => 'Jenis revisi wajib dipilih.']);
        app(HrisWorkflowService::class)->decide($model, $type, $step, $action, $request->user(), $request->input('comment'), $request->input('revision_type'));
        return redirect()->route('workspace.hris.approvals')->with('status', 'Keputusan workflow berhasil disimpan.');
    }

    public function supportingDocument(Request $request, string $type, string $id, string $step): StreamedResponse
    {
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('view_hris_approvals'), 403);

        $model = match ($type) {
            'leave' => LeaveRequest::query()->with('employee')->findOrFail($id),
            'overtime' => OvertimeRequest::query()->with('employee')->findOrFail($id),
            'claim' => Claim::query()->with('employee')->findOrFail($id),
            default => abort(404),
        };
        abort_unless(filled($model->supporting_document_id), 404);
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->companies()->whereKey($model->company_id)->exists(), 403);

        if ($step === 'manager') {
            $managerUserId = $model->employee?->manager?->user_id;
            abort_unless($request->user()->hasRole('super_admin') || (int) $managerUserId === (int) $request->user()->getKey(), 403);
        } else {
            $workflowKey = in_array($type, ['leave', 'overtime'], true) ? 'leave_sick' : $type;
            abort_unless($request->user()->hasRole('super_admin') || in_array((int) $request->user()->getKey(), HrisWorkflowApprover::approverIds((int) $model->company_id, $workflowKey, $step), true), 403);
        }

        $disk = Storage::disk(config('filesystems.default', 's3'));
        $objectKey = (string) $model->supporting_document_id;
        abort_unless($disk->exists($objectKey), 404);
        $stream = $disk->readStream($objectKey);
        abort_unless(is_resource($stream), 404);

        $mimeType = $disk->mimeType($objectKey) ?: 'application/octet-stream';
        $filename = basename($objectKey);
        $headers = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $request->boolean('preview') ? 'inline; filename="'.addslashes($filename).'"' : 'attachment; filename="'.addslashes($filename).'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ];

        return response()->stream(function () use ($stream): void {
            fpassthru($stream);
            fclose($stream);
        }, 200, $headers);
    }

    public function accountability(Request $request, string $id): RedirectResponse
    {
        $claim = Claim::query()->findOrFail($id); abort_unless($claim->type === 'cash_advance', 404);
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->companies()->whereKey($claim->company_id)->exists(), 403);
        $request->validate(['accountability_document_id' => ['required', 'string', 'max:100']]);
        $claim->update(['accountability_document_id' => $request->string('accountability_document_id')->toString(), 'status' => 'pending_finance_accountability']);
        return back()->with('status', 'Laporan pertanggungjawaban berhasil dikirim.');
    }
}
