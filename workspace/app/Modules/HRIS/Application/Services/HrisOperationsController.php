<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\ClaimExpenseItem;
use App\Modules\HRIS\Domain\Models\ApprovalEvent;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\LeaveRequest;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use App\Modules\HRIS\Domain\Models\HrisExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
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
            'claim' => Claim::query()->with(['employee.company', 'employee.manager', 'expenseItems', 'project'])->findOrFail($id),
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
        $hasSupportingDocument = $type !== 'claim' && filled($model->supporting_document_id) && $supportingDisk->exists((string) $model->supporting_document_id);
        $supportingMimeType = $hasSupportingDocument ? ($supportingDisk->mimeType((string) $model->supporting_document_id) ?: 'application/octet-stream') : null;
        $canPreviewSupportingDocument = $supportingMimeType === 'application/pdf' || str_starts_with((string) $supportingMimeType, 'image/');
        $approvalTimeline = collect();
        if ($type === 'claim') {
            $model->load('approvalEvents.actor');
            $approvalTimeline->push([
                'action' => 'submitted', 'label' => 'Pengajuan dibuat', 'step' => 'Pemohon',
                'actor' => $model->employee?->full_name ?? 'Pemohon',
                'at' => $model->submitted_at ?: $model->created_at, 'comment' => null, 'state' => 'is-done',
            ]);
            foreach ($model->approvalEvents as $event) {
                $stepLabel = match ($event->step) {
                    'manager' => 'Atasan', 'finance' => 'Finance', 'final' => 'Approver final',
                    'requester' => 'Pemohon', 'payment' => 'Pembayaran', default => ucfirst((string) $event->step),
                };
                [$actionLabel, $state] = match ($event->action) {
                    'approve' => ['Menyetujui pengajuan', 'is-done'],
                    'reject' => ['Menolak pengajuan', 'is-danger'],
                    'request_revision' => ['Meminta revisi', 'is-warning'],
                    'resubmitted' => ['Pengajuan dikirim ulang', 'is-warning'],
                    default => [ucfirst(str_replace('_', ' ', (string) $event->action)), 'is-done'],
                };
                $approvalTimeline->push([
                    'action' => $event->action, 'label' => $actionLabel, 'step' => $stepLabel,
                    'actor' => $event->actor?->name ?? 'Pengguna', 'at' => $event->created_at,
                    'comment' => $event->comment, 'state' => $state,
                ]);
            }
            $approvalTimeline = $approvalTimeline->sortBy('at')->values();
        }
        $typeLabel = match ($type) {
            'leave' => $model->type === 'sick' ? 'Sakit' : 'Cuti',
            'overtime' => 'Lembur',
            default => $model->type === 'cash_advance' ? 'Cash advance' : 'Reimbursement',
        };
        $stage = match ($step) { 'manager' => 'Atasan', 'hr' => 'HR', 'finance' => 'Finance', 'final' => 'Approver final', default => ucfirst($step) };

        return view('modules.hris-approval-detail', compact('model', 'type', 'step', 'stage', 'typeLabel', 'hasSupportingDocument', 'supportingMimeType', 'canPreviewSupportingDocument', 'approvalTimeline'));
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
        $statusOptions = [
            'draft' => 'Draft', 'submitted' => 'Diajukan', 'pending_manager' => 'Menunggu persetujuan atasan',
            'pending_finance' => 'Menunggu persetujuan Finance', 'pending_final' => 'Menunggu persetujuan final',
            'pending_payment' => 'Menunggu pembayaran', 'awaiting_accountability' => 'Menunggu pertanggungjawaban',
            'needs_revision' => 'Perlu revisi', 'approved' => 'Disetujui', 'paid' => 'Dibayar',
            'completed' => 'Selesai', 'rejected' => 'Ditolak',
        ];
        $selectedStatuses = is_array($request->query('statuses'))
            ? array_values(array_unique(array_filter($request->query('statuses'), fn ($value) => is_string($value) && array_key_exists($value, $statusOptions))))
            : [];
        $typeOptions = ['reimbursement' => 'Reimbursement', 'cash_advance' => 'Cash Advance'];
        $selectedTypes = is_array($request->query('types'))
            ? array_values(array_unique(array_filter($request->query('types'), fn ($value) => is_string($value) && array_key_exists($value, $typeOptions))))
            : [];
        if ($summaryView) {
            $selectedTypes = [$type];
        } elseif ($selectedTypes === [] && $type !== null) {
            $selectedTypes = [$type];
        }
        $activeTabType = ! $summaryView && count($selectedTypes) === 1 ? $selectedTypes[0] : ($summaryView ? $type : null);
        $isValidDate = static function ($value): bool {
            if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) return false;
            [$year, $month, $day] = array_map('intval', explode('-', $value));
            return checkdate($month, $day, $year);
        };
        $periodFrom = $isValidDate($request->query('from')) ? $request->query('from') : now()->startOfMonth()->toDateString();
        $periodTo = $isValidDate($request->query('to')) ? $request->query('to') : now()->toDateString();
        if ($periodFrom > $periodTo) [$periodFrom, $periodTo] = [$periodTo, $periodFrom];
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $sort = in_array($request->string('sort')->toString(), ['type', 'amount', 'status', 'created_at'], true) ? $request->string('sort')->toString() : 'created_at';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $visibleClaims = Claim::query()
            ->when($summaryView && ! $request->user()->hasRole('super_admin'), fn ($query) => $query->whereIn('company_id', $request->user()->companies()->pluck('companies.id')))
            ->when($summaryView, fn ($query) => $query->where('status', '!=', 'draft'))
            ->when(! $summaryView, fn ($query) => $query->visibleTo($request->user()))
            ->when(! $summaryView, fn ($query) => $query->where(fn ($draftQuery) => $draftQuery->where('status', '!=', 'draft')->orWhere('created_by', $request->user()->getKey())));
        $categoryOptions = ClaimExpenseItem::query()
            ->whereHas('claim', fn ($query) => $query->whereIn('id', (clone $visibleClaims)->select('id')))
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();
        $selectedCategories = is_array($request->query('categories'))
            ? array_values(array_unique(array_filter($request->query('categories'), fn ($value) => is_string($value) && in_array($value, $categoryOptions, true))))
            : [];
        $items = (clone $visibleClaims)
            ->with('employee')
            ->with('expenseItems')
            ->whereDate('created_at', '>=', $periodFrom)
            ->whereDate('created_at', '<=', $periodTo)
            ->when($selectedTypes !== [], fn ($q) => $q->whereIn('type', $selectedTypes))
            ->when($selectedStatuses !== [], fn ($q) => $q->whereIn('status', $selectedStatuses))
            ->when($selectedCategories !== [], fn ($q) => $q->whereHas('expenseItems', fn ($detail) => $detail->whereIn('category', $selectedCategories)))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
        return view('modules.hris-claims-enhanced', compact('items', 'activeTabType', 'selectedStatuses', 'statusOptions', 'selectedTypes', 'typeOptions', 'selectedCategories', 'categoryOptions', 'periodFrom', 'periodTo', 'perPage', 'sort', 'direction', 'summaryView'));
    }
    public function claimCreate(Request $request): View
    {
        Gate::authorize('create', Claim::class);
        $employee = Employee::query()->visibleTo($request->user())->where('user_id', $request->user()->getKey())->where('status', 'active')->with(['company', 'manager', 'organizationUnit'])->firstOrFail();
        $requestNumber = 'Nomor dibuat saat dikirim';
        $financeApprovers = HrisWorkflowApprover::query()->with('user')->forWorkflow((int) $employee->company_id, 'claim', 'finance')->get();
        $finalApprovers = HrisWorkflowApprover::query()->with('user')->forWorkflow((int) $employee->company_id, 'claim', 'final')->get();
        $expenseCategories = HrisExpenseCategory::query()->active()->orderBy('sort_order')->orderBy('name')->get();
        $projects = app(OvertimeProjectService::class)->available($employee, $request->user())->with('clients')->orderBy('name')->get();
        $opportunities = app(OvertimeWorkReferenceService::class)->availableOpportunities($employee, $request->user())->with(['account', 'contact'])->latest()->get();
        $referenceDetails = $this->claimReferenceDetails($projects, $opportunities);
        return view('modules.hris-claim-form-layout', compact('employee', 'financeApprovers', 'finalApprovers', 'projects', 'opportunities', 'referenceDetails', 'expenseCategories') + ['employees' => collect([$employee]), 'selectedType' => 'reimbursement', 'requestNumber' => $requestNumber, 'isDraft' => false, 'claimData' => [], 'draftItems' => [], 'claimAction' => route('workspace.hris.claims.store')]);
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
        $expenseCategories = HrisExpenseCategory::query()->where(fn ($query) => $query->active()->orWhereIn('id', $claim->expenseItems->pluck('expense_category_id')->filter()))->orderBy('sort_order')->orderBy('name')->get();
        $projects = app(OvertimeProjectService::class)->available($employee, $request->user())->with('clients')->orderBy('name')->get();
        $opportunities = app(OvertimeWorkReferenceService::class)->availableOpportunities($employee, $request->user())->with(['account', 'contact'])->latest()->get();
        $referenceDetails = $this->claimReferenceDetails($projects, $opportunities);
        $claimData = ['expense_scope' => $claim->expense_scope, 'work_reference_type' => $claim->work_reference_type, 'opportunity_id' => $claim->opportunity_id, 'department' => $claim->department, 'project_id' => $claim->project_id, 'po_number' => $claim->po_number, 'purpose' => $claim->purpose, 'type' => $claim->type];
        $draftItems = $claim->expenseItems->map(fn ($item): array => ['id' => $item->getKey(), 'receipt_date' => $item->receipt_date?->format('Y-m-d'), 'activity_date' => $item->activity_date?->format('Y-m-d'), 'expense_category_id' => $item->expense_category_id, 'category' => $item->category, 'description' => $item->description, 'currency' => $item->currency, 'amount' => $item->amount, 'attachment_reference' => $item->attachment_reference, 'attachment_object_key' => $item->attachment_object_key])->values();
        $isRevision = $claim->status === 'needs_revision';
        return view('modules.hris-claim-form-layout', compact('claim', 'employee', 'financeApprovers', 'finalApprovers', 'projects', 'opportunities', 'referenceDetails', 'expenseCategories', 'isRevision') + ['employees' => collect([$employee]), 'selectedType' => $claim->type, 'requestNumber' => $claim->request_number, 'isDraft' => true, 'claimData' => $claimData, 'draftItems' => $draftItems, 'claimAction' => route('workspace.hris.claims.update', $claim)]);
    }

    public function claimShow(Request $request, Claim $claim): View
    {
        Gate::authorize('view', $claim);
        $claim->load(['employee.company', 'project.clients', 'opportunity.account', 'opportunity.contact', 'expenseItems']);
        $canViewAuditTrail = Gate::allows('viewAuditTrail', $claim);
        $statusTimeline = collect();
        if ($claim->status !== 'draft') {
            $statusTimeline->push(['label' => 'Pengajuan dikirim', 'state' => 'done', 'at' => $claim->submitted_at ?: $claim->created_at]);
            $managerState = $claim->status === 'needs_revision' && $claim->revision_requested_role === 'manager'
                ? 'revision'
                : ($claim->manager_approved_at || in_array($claim->status, ['pending_finance', 'pending_final', 'approved', 'pending_payment', 'paid', 'completed'], true) ? 'done' : ($claim->status === 'pending_manager' ? 'current' : 'upcoming'));
            $statusTimeline->push(['label' => 'Pemeriksaan Atasan', 'state' => $managerState, 'at' => $claim->manager_approved_at ?: ($managerState === 'revision' ? $claim->revision_requested_at : null)]);
            $financeState = $claim->status === 'needs_revision' && $claim->revision_requested_role === 'finance'
                ? 'revision'
                : ($claim->finance_approved_at || in_array($claim->status, ['pending_final', 'approved', 'pending_payment', 'paid', 'completed'], true) ? 'done' : ($claim->status === 'pending_finance' ? 'current' : 'upcoming'));
            $statusTimeline->push(['label' => 'Pemeriksaan Finance', 'state' => $financeState, 'at' => $claim->finance_approved_at ?: ($financeState === 'revision' ? $claim->revision_requested_at : null)]);
            if ($claim->status === 'pending_final' || $claim->final_approved_at) {
                $statusTimeline->push(['label' => 'Persetujuan akhir', 'state' => $claim->final_approved_at ? 'done' : 'current', 'at' => $claim->final_approved_at]);
            }
            if ($claim->status === 'rejected') {
                $statusTimeline->push(['label' => 'Pengajuan ditolak', 'state' => 'rejected', 'at' => $claim->updated_at]);
            }
        }
        $timelineEntries = collect();
        if ($claim->status !== 'draft' && filled($claim->request_number)) {
            $timelineEntries->push([
                'kind' => 'event', 'action' => 'submitted', 'step' => 'requester',
                'actor' => $claim->employee?->full_name ?? 'Pemohon',
                'at' => $claim->submitted_at ?: $claim->created_at, 'comment' => null,
            ]);
        }
        $claim->load($canViewAuditTrail ? 'approvalEvents.actor' : 'approvalEvents');
        foreach ($claim->approvalEvents as $event) {
            $timelineEntries->push([
                'kind' => 'event', 'action' => $event->action, 'step' => $event->step,
                'actor' => $canViewAuditTrail ? ($event->actor?->name ?? 'Pengguna') : null,
                'at' => $event->created_at, 'comment' => $canViewAuditTrail ? $event->comment : null,
            ]);
        }
        if ($canViewAuditTrail) {
            $snapshots = DB::connection('hris')->table('claim_revision_snapshots')->where('claim_id', $claim->getKey())->get();
            foreach ($snapshots as $snapshot) {
                $timelineEntries->push([
                    'kind' => 'snapshot', 'revision_number' => $snapshot->revision_number,
                    'revision_type' => $snapshot->revision_type, 'requested_role' => $snapshot->requested_role,
                    'reason' => $snapshot->revision_reason, 'item_count' => count(json_decode($snapshot->expense_items_snapshot, true) ?: []),
                    'at' => \Illuminate\Support\Carbon::parse($snapshot->created_at),
                ]);
            }
            $timelineEntries = $timelineEntries->sortBy('at')->values();
        }

        return view('modules.hris-claim-detail', compact('claim', 'statusTimeline', 'timelineEntries', 'canViewAuditTrail'));
    }

    public function claimPrint(Request $request, Claim $claim): View
    {
        Gate::authorize('view', $claim);
        abort_unless(
            $claim->type === 'reimbursement'
                && $claim->finance_approved_at !== null
                && in_array($claim->status, ['pending_final', 'approved', 'pending_payment', 'paid', 'completed'], true),
            404,
        );

        $claim->load(['employee.company', 'project.clients', 'opportunity.account', 'opportunity.contact', 'expenseItems', 'managerApprover', 'financeApprover', 'finalApprover']);
        $attachmentDisk = Storage::disk((string) config('hris.claim_attachment_disk', 's3'));
        $printAttachments = $claim->expenseItems
            ->filter(fn (ClaimExpenseItem $item): bool => filled($item->attachment_object_key))
            ->map(function (ClaimExpenseItem $item) use ($claim, $attachmentDisk): array {
                try {
                    $mimeType = $attachmentDisk->mimeType((string) $item->attachment_object_key) ?: 'application/octet-stream';
                } catch (\Throwable) {
                    $mimeType = 'application/octet-stream';
                }

                return [
                    'item' => $item,
                    'mime_type' => $mimeType,
                    'url' => route('workspace.hris.claims.expense-items.attachment', ['claim' => $claim, 'item' => $item, 'preview' => 1]),
                ];
            })
            ->values();

        $attachmentPages = [];
        foreach ($printAttachments as $attachment) {
            $pageType = str_starts_with($attachment['mime_type'], 'image/') ? 'images' : ($attachment['mime_type'] === 'application/pdf' ? 'pdf' : 'unsupported');
            $lastPageIndex = array_key_last($attachmentPages);

            if ($pageType === 'images' && $lastPageIndex !== null && $attachmentPages[$lastPageIndex]['type'] === 'images') {
                $attachmentPages[$lastPageIndex]['attachments'][] = $attachment;
                continue;
            }

            $attachmentPages[] = ['type' => $pageType, 'attachments' => [$attachment]];
        }

        return view('modules.hris-claim-print', compact('claim', 'printAttachments', 'attachmentPages'));
    }

    public function claimUpdate(ClaimRequestForm $request, Claim $claim, ClaimService $service): RedirectResponse
    {
        Gate::authorize('update', $claim);
        $employee = $claim->employee()->where('user_id', $request->user()->getKey())->where('status', 'active')->firstOrFail();
        $data = $request->validated();
        if ($claim->status === 'needs_revision') {
            if ($request->input('action') === 'draft') {
                $service->saveRevisionDraft($claim, $employee, $data, (int) $request->user()->getKey());
                return redirect()->route('workspace.hris.claims.edit', $claim)->with('status', 'Perubahan revisi berhasil disimpan. Anda dapat melanjutkan dan mengirimnya nanti.');
            }
            abort_unless($request->input('action') === 'submit', 422, 'Aksi revisi tidak valid.');
            $service->resubmit($claim, $employee, $data, (int) $request->user()->getKey());
            return redirect()->route('workspace.hris.claims')->with('status', 'Revisi pengajuan claim berhasil dikirim untuk pemeriksaan ulang.');
        }
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

    public function claimExpenseAttachment(Request $request, Claim $claim, ClaimExpenseItem $item): StreamedResponse
    {
        abort_unless((string) $item->claim_id === (string) $claim->getKey(), 404);
        $user = $request->user();
        $isOwner = (int) $claim->created_by === (int) $user->getKey() || (int) $claim->employee?->user_id === (int) $user->getKey();
        $isCompanyViewer = $user->can('view_any_claim') && $user->companies()->whereKey($claim->company_id)->exists();
        $isManager = $claim->status === 'pending_manager' && (int) $claim->employee?->manager?->user_id === (int) $user->getKey();
        $isFinanceApprover = $claim->status === 'pending_finance' && in_array((int) $user->getKey(), HrisWorkflowApprover::approverIds((int) $claim->company_id, 'claim', 'finance'), true);
        $isFinalApprover = $claim->status === 'pending_final' && in_array((int) $user->getKey(), HrisWorkflowApprover::approverIds((int) $claim->company_id, 'claim', 'final'), true);
        abort_unless($user->hasRole('super_admin') || $isOwner || $isCompanyViewer || $isManager || $isFinanceApprover || $isFinalApprover, 403);

        $objectKey = (string) $item->attachment_object_key;
        $disk = Storage::disk((string) config('hris.claim_attachment_disk', 's3'));
        abort_unless($objectKey !== '' && $disk->exists($objectKey), 404);
        $stream = $disk->readStream($objectKey);
        abort_unless(is_resource($stream), 404);

        $mimeType = $disk->mimeType($objectKey) ?: 'application/octet-stream';
        $disposition = $request->boolean('preview') ? 'inline' : 'attachment';

        return response()->stream(function () use ($stream): void {
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition.'; filename="bukti-nota-'.$item->line_number.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
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
        ApprovalEvent::create(['request_type' => 'claim', 'request_id' => $claim->getKey(), 'step' => 'payment', 'action' => 'pending_payment', 'actor_id' => $request->user()->getKey(), 'comment' => null, 'created_at' => now('UTC')]);
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

    private function claimReferenceDetails($projects, $opportunities): array
    {
        $details = [];
        foreach ($projects as $project) {
            $details[(string) $project->id] = [
                'company' => $project->company?->name ?: '—',
                'client' => $project->clients->first()?->name ?: '—',
                'status' => 'Aktif',
            ];
        }
        foreach ($opportunities as $opportunity) {
            $status = app(OvertimeWorkReferenceService::class)->statusParts($opportunity);
            $details[(string) $opportunity->id] = [
                'company' => $opportunity->company?->name ?: '—',
                'client' => $opportunity->account?->name ?: '—',
                'contact' => $opportunity->contact?->name ?: ($opportunity->email ?: '—'),
                'kind' => 'opportunity',
                'classification' => $status['classification'],
                'stage' => $status['stage'],
                'closing' => $status['closing'],
                'status' => $status['classification'],
            ];
        }
        return $details;
    }
}
