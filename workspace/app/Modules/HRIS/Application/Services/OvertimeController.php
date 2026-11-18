<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OvertimeController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', OvertimeRequest::class);
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $sort = in_array($request->string('sort')->toString(), ['request_number', 'work_date', 'duration_minutes', 'status', 'created_at'], true) ? $request->string('sort')->toString() : 'created_at';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $allowedStatuses = ['draft', 'pending_manager', 'pending_hr', 'needs_revision', 'approved', 'rejected'];
        $status = collect($request->input('status', []))->when(is_string($request->input('status')), fn ($values) => collect([$request->input('status')]))->filter(fn ($value) => in_array($value, $allowedStatuses, true))->values()->all();
        $dateFrom = $request->input('date_from') ?: now()->startOfMonth()->toDateString();
        $dateTo = $request->input('date_to') ?: now()->toDateString();
        $userId = $request->user()->getKey();
        $itemsQuery = OvertimeRequest::query()
            ->where(fn ($owner) => $owner->where('requester_user_id', $userId)->orWhereHas('employee', fn ($employee) => $employee->where('user_id', $userId)))
            ->where(fn ($query) => $query->where('status', '!=', 'draft')->orWhere('created_by', $userId))
            ->with(['employee.organizationUnit', 'entries'])
            ->when($request->filled('q'), fn ($query) => $query->where(fn ($nested) => $nested->where('request_number', 'ilike', '%'.trim($request->q).'%')->orWhereHas('employee', fn ($employee) => $employee->where('full_name', 'ilike', '%'.trim($request->q).'%'))))
            ->when($status !== [], fn ($query) => $query->whereIn('status', $status))
            ->whereHas('entries', fn ($entries) => $entries->whereDate('work_date', '>=', $dateFrom)->whereDate('work_date', '<=', $dateTo));
        $summaryByStatus = (clone $itemsQuery)
            ->selectRaw('status, count(*) as request_count, coalesce(sum(duration_minutes), 0) as total_minutes')
            ->groupBy('status')
            ->get()
            ->keyBy('status');
        $summarizeStatuses = static function (array $statuses) use ($summaryByStatus): array {
            $rows = collect($statuses)->map(fn (string $status) => $summaryByStatus->get($status))->filter();
            return ['count' => (int) $rows->sum('request_count'), 'minutes' => (int) $rows->sum('total_minutes')];
        };
        $summary = [
            'total' => ['count' => (clone $itemsQuery)->count(), 'minutes' => (int) (clone $itemsQuery)->sum('duration_minutes')],
            'approved' => $summarizeStatuses(['approved']),
            'pending' => $summarizeStatuses(['pending_manager', 'pending_hr', 'needs_revision']),
            'rejected' => $summarizeStatuses(['rejected']),
            'draft' => $summarizeStatuses(['draft']),
        ];
        $items = (clone $itemsQuery)->orderBy($sort, $direction)->paginate($perPage)->withQueryString();
        return view('modules.hris-overtime-index', compact('items', 'perPage', 'sort', 'direction', 'status', 'dateFrom', 'dateTo', 'summary'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', OvertimeRequest::class);
        $employee = Employee::query()->visibleTo($request->user())->where('user_id', $request->user()->getKey())->where('status', 'active')->with(['company', 'manager', 'organizationUnit'])->firstOrFail();
        $requestNumber = 'Nomor dibuat saat dikirim';
        return $this->form($request, new OvertimeRequest, $requestNumber, $employee);
    }

    public function store(OvertimeRequestForm $request, OvertimeService $service): RedirectResponse
    {
        Gate::authorize('create', OvertimeRequest::class);
        $employee = Employee::query()->visibleTo($request->user())->where('user_id', $request->user()->getKey())->where('status', 'active')->firstOrFail();
        $data = $request->validated();
        if ($request->input('action') === 'draft') {
            $service->saveDraft($employee, $data, (int) $request->user()->getKey());
            return redirect()->route('workspace.hris.overtime')->with('status', 'Draft pengajuan lembur berhasil disimpan.');
        }
        $data['request_number'] = $request->session()->pull('hris.request_number.overtime', null);
        $service->submit($employee, $data, (int) $request->user()->getKey());
        return redirect()->route('workspace.hris.overtime')->with('status', 'Pengajuan lembur berhasil dikirim ke Atasan.');
    }

    public function show(Request $request, OvertimeRequest $overtimeRequest): View
    {
        $isSummaryDetail = $request->routeIs('workspace.hris.overtime-summary.show');
        Gate::authorize($isSummaryDetail ? 'view' : 'viewOwner', $overtimeRequest);
        $backUrl = route($isSummaryDetail ? 'workspace.hris.overtime-summary' : 'workspace.hris.overtime');
        if ($isSummaryDetail) {
            $returnUrl = $request->query('return');
            $returnPath = is_string($returnUrl) ? parse_url($returnUrl, PHP_URL_PATH) : null;
            $summaryPath = parse_url(route('workspace.hris.overtime-summary'), PHP_URL_PATH);
            $returnHost = is_string($returnUrl) ? parse_url($returnUrl, PHP_URL_HOST) : null;
            $appHost = parse_url($request->root(), PHP_URL_HOST);
            if (is_string($returnUrl) && $returnPath === $summaryPath && ($returnHost === null || hash_equals((string) $appHost, $returnHost))) {
                $backUrl = $returnUrl;
            }
        }
        $overtimeRequest->load([
            'employee.company',
            'employee.manager',
            'employee.organizationUnit',
            'entries',
            'approvalEvents.actor',
            'project.company',
            'project.clients',
            'opportunity.company',
            'opportunity.account',
        ]);
        $referenceType = $overtimeRequest->work_reference_type
            ?: ($overtimeRequest->opportunity_id ? 'opportunity' : ($overtimeRequest->project_id ? 'project' : 'none'));
        $referenceProject = $referenceType === 'project' ? $overtimeRequest->project : null;
        $referenceOpportunity = $referenceType === 'opportunity' ? $overtimeRequest->opportunity : null;
        $referenceClientNames = $referenceProject?->clients?->pluck('name')->filter()->unique()->values();
        $referenceClient = $referenceType === 'project'
            ? ($referenceClientNames?->isNotEmpty() ? $referenceClientNames->join(', ') : 'Belum ditentukan')
            : ($referenceOpportunity?->account?->name ?? 'Belum ditentukan');
        $referenceOwner = $referenceType === 'project'
            ? ($referenceProject?->company?->name ?? 'Informasi perusahaan tidak tersedia')
            : ($referenceOpportunity?->company?->name ?? 'Informasi perusahaan tidak tersedia');

        return view('modules.hris-overtime-show', [
            'item' => $overtimeRequest,
            'referenceType' => $referenceType,
            'referenceProject' => $referenceProject,
            'referenceOpportunity' => $referenceOpportunity,
            'referenceClient' => $referenceClient,
            'referenceOwner' => $referenceOwner,
            'backUrl' => $backUrl,
        ]);
    }

    public function edit(Request $request, OvertimeRequest $overtimeRequest): View
    {
        Gate::authorize('update', $overtimeRequest);
        $overtimeRequest->load('entries');
        return $this->form($request, $overtimeRequest, $overtimeRequest->request_number ?: 'Nomor dibuat saat dikirim');
    }

    public function resubmit(OvertimeRequestForm $request, OvertimeRequest $overtimeRequest, OvertimeService $service): RedirectResponse
    {
        Gate::authorize('update', $overtimeRequest);
        $employee = $overtimeRequest->employee()->where('status', 'active')->firstOrFail();
        $data = $request->validated();
        $wasDraft = $overtimeRequest->status === 'draft';
        $action = $request->input('action');
        if ($overtimeRequest->status === 'draft') {
            if ($action === 'draft') $service->updateDraft($overtimeRequest, $employee, $data, (int) $request->user()->getKey());
            else $service->submitDraft($overtimeRequest, $employee, $data, (int) $request->user()->getKey());
        } elseif ($action === 'draft') {
            $service->saveRevisionDraft($overtimeRequest, $employee, $data, (int) $request->user()->getKey());
        } else {
            $service->resubmit($overtimeRequest, $employee, $data, (int) $request->user()->getKey());
        }
        if ($wasDraft) {
            return redirect()->route('workspace.hris.overtime')->with('status', $action === 'draft' ? 'Draft pengajuan lembur berhasil diperbarui.' : 'Pengajuan lembur berhasil dikirim ke Atasan.');
        }
        if ($action === 'draft') return redirect()->route('workspace.hris.overtime.show', $overtimeRequest)->with('status', 'Perubahan revisi lembur berhasil disimpan sebagai draft.');
        return redirect()->route('workspace.hris.overtime.show', $overtimeRequest)->with('status', 'Revisi lembur berhasil dikirim ulang.');
    }

    public function destroy(Request $request, OvertimeRequest $overtimeRequest): RedirectResponse
    {
        Gate::authorize('update', $overtimeRequest);
        abort_unless($overtimeRequest->status === 'draft', 404);
        $overtimeRequest->delete();
        return redirect()->route('workspace.hris.overtime')->with('status', 'Draft pengajuan lembur berhasil dihapus.');
    }

    private function form(Request $request, OvertimeRequest $overtimeRequest, string $requestNumber, ?Employee $employee = null): View
    {
        $employee ??= $overtimeRequest->employee()->with(['company', 'manager', 'organizationUnit'])->firstOrFail();
        $projects = app(OvertimeProjectService::class)->available($employee, $request->user(), ignoreCompanyScope: true)->with('clients')->orderBy('name')->get(['id', 'company_id', 'name', 'end_date']);
        $opportunities = app(OvertimeWorkReferenceService::class)->availableOpportunities($employee, $request->user(), ignoreCompanyScope: true)->with(['account', 'contact'])->orderBy('name')->get();
        $hrApprovers = HrisWorkflowApprover::query()->with('user')->forWorkflow((int) $employee->company_id, 'leave_sick', 'hr')->get();
        return view('modules.hris-overtime-form', compact('employee', 'requestNumber', 'overtimeRequest', 'projects', 'opportunities', 'hrApprovers'));
    }
}
