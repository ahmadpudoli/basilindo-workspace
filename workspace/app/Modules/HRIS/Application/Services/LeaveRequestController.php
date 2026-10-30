<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Application\Services\LeaveRequestService;
use App\Modules\HRIS\Domain\Exceptions\OverlappingLeaveRequest;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\LeaveRequest as LeaveRequestModel;
use App\Modules\HRIS\Domain\Models\LeaveRequestDate;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', LeaveRequestModel::class);
        $user = $request->user();
        $summaryView = $request->string('view')->toString() === 'summary';
        $canViewAll = $user->hasRole('super_admin') || ($summaryView && $user->can('view_hris_leave_summary'));
        $type = in_array($request->string('type')->toString(), ['leave', 'sick'], true) ? $request->string('type')->toString() : null;
        $status = in_array($request->string('status')->toString(), ['pending_manager', 'pending_hr', 'needs_revision', 'approved', 'rejected'], true) ? $request->string('status')->toString() : null;
        $yearInput = $request->string('year')->toString();
        $year = $yearInput === 'all' ? null : ($request->integer('year') ?: now()->year);
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $sort = in_array($request->string('sort')->toString(), ['leave_date', 'start_date', 'status', 'created_at'], true) ? $request->string('sort')->toString() : 'created_at';
        if ($sort === 'start_date') $sort = 'leave_date';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $query = LeaveRequestModel::query()->with(['employee', 'dates']);
        if ($canViewAll) {
            $query->when(! $user->hasRole('super_admin'), fn ($q) => $q->whereIn('company_id', $user->companies()->pluck('companies.id')));
        } else {
            $query->where(function (Builder $q) use ($user): void {
                $q->where('created_by', $user->getKey())->orWhereHas('employee', fn (Builder $employee) => $employee->where('user_id', $user->getKey()));
            });
        }
        $query->when($type, fn ($q) => $q->where('type', $type))->when($status, fn ($q) => $q->where('status', $status))->when($year !== null, fn ($q) => $q->whereHas('dates', fn ($dateQuery) => $dateQuery->whereYear('leave_date', $year)));
        if ($sort === 'leave_date') {
            $query->orderBy(LeaveRequestDate::query()->select('leave_date')->whereColumn('leave_request_id', 'leave_requests.id')->orderBy('leave_date')->limit(1), $direction);
        } else {
            $query->orderBy($sort, $direction);
        }
        $requests = $query->paginate($perPage)->withQueryString();
        $employee = Employee::query()->where('user_id', $user->getKey())->first();
        $metricYear = $year ?? now()->year;
        $quota = $employee ? (int) HrisSetting::resolve($employee->company_id, 'hris.annual_leave_quota', 12) : 0;
        $usedLeave = $employee ? (int) LeaveRequestDate::query()->whereYear('leave_date', $metricYear)->whereHas('leaveRequest', fn ($q) => $q->where('employee_id', $employee->getKey())->where('type', 'leave')->where('status', 'approved'))->count() : 0;
        $sickDays = $employee ? (int) LeaveRequestDate::query()->whereYear('leave_date', $metricYear)->whereHas('leaveRequest', fn ($q) => $q->where('employee_id', $employee->getKey())->where('type', 'sick')->where('status', 'approved'))->count() : 0;
        $canCreate = $user->hasRole('super_admin') || $user->can('create_leave_request');
        return view('modules.hris-leave-requests-enhanced', compact('requests', 'type', 'status', 'year', 'metricYear', 'perPage', 'sort', 'direction', 'quota', 'usedLeave', 'sickDays', 'canViewAll', 'canCreate', 'summaryView'));
    }

    public function show(Request $request, LeaveRequestModel $leaveRequest): View
    {
        Gate::authorize('view', $leaveRequest);
        $leaveRequest->load(['employee.company', 'approvalEvents', 'dates']);
        $detailContext = in_array($request->string('from')->toString(), ['summary', 'leave-requests', 'notification'], true)
            ? $request->string('from')->toString()
            : 'default';
        $showRequesterActions = $detailContext !== 'summary';
        $disk = Storage::disk(config('filesystems.default', 's3'));
        $hasSupportingDocument = filled($leaveRequest->supporting_document_id) && $disk->exists((string) $leaveRequest->supporting_document_id);
        $supportingMimeType = $hasSupportingDocument ? ($disk->mimeType((string) $leaveRequest->supporting_document_id) ?: 'application/octet-stream') : null;
        $canPreviewSupportingDocument = $supportingMimeType === 'application/pdf' || str_starts_with((string) $supportingMimeType, 'image/');
        return view('modules.hris-leave-show-detail', compact('leaveRequest', 'hasSupportingDocument', 'supportingMimeType', 'canPreviewSupportingDocument', 'detailContext', 'showRequesterActions'));
    }

    public function supportingDocument(Request $request, LeaveRequestModel $leaveRequest): StreamedResponse
    {
        Gate::authorize('view', $leaveRequest);
        abort_unless(filled($leaveRequest->supporting_document_id), 404);

        $disk = Storage::disk(config('filesystems.default', 's3'));
        $objectKey = (string) $leaveRequest->supporting_document_id;
        abort_unless($disk->exists($objectKey), 404);
        $stream = $disk->readStream($objectKey);
        abort_unless(is_resource($stream), 404);

        $extension = pathinfo($objectKey, PATHINFO_EXTENSION) ?: 'bin';
        $headers = [
            'Content-Type' => $disk->mimeType($objectKey) ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ];
        if (! $request->boolean('preview')) $headers['Content-Disposition'] = 'attachment; filename="'.($leaveRequest->request_number ?: $leaveRequest->getKey()).'.'.$extension.'"';

        return response()->stream(function () use ($stream): void {
            fpassthru($stream);
            fclose($stream);
        }, 200, $headers);
    }

    public function summary(Request $request): View
    {
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('view_hris_leave_summary'), 403);

        $type = in_array($request->string('type')->toString(), ['leave', 'sick'], true) ? $request->string('type')->toString() : null;
        $status = in_array($request->string('status')->toString(), ['pending_manager', 'pending_hr', 'needs_revision', 'approved', 'rejected'], true) ? $request->string('status')->toString() : null;
        $yearInput = $request->string('year')->toString();
        $year = $yearInput === 'all' ? null : ($request->integer('year') ?: now()->year);
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $sort = in_array($request->string('sort')->toString(), ['start_date', 'status', 'created_at'], true) ? $request->string('sort')->toString() : 'created_at';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $companyScope = $request->user()->hasRole('super_admin') ? null : $request->user()->companies()->pluck('companies.id');

        $query = LeaveRequestModel::query()->with(['employee.company', 'dates'])
            ->when($companyScope !== null, fn ($q) => $q->whereIn('company_id', $companyScope))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($year !== null, fn ($q) => $q->whereHas('dates', fn ($dateQuery) => $dateQuery->whereYear('leave_date', $year)));
        if ($sort === 'start_date') {
            $query->orderBy(LeaveRequestDate::query()->select('leave_date')->whereColumn('leave_request_id', 'leave_requests.id')->orderBy('leave_date')->limit(1), $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $requests = $query->paginate($perPage)->withQueryString();
        $metricsQuery = LeaveRequestModel::query()->when($companyScope !== null, fn ($q) => $q->whereIn('company_id', $companyScope))->when($year !== null, fn ($q) => $q->whereHas('dates', fn ($dateQuery) => $dateQuery->whereYear('leave_date', $year)));
        $metrics = [
            'total' => (clone $metricsQuery)->count(),
            'pending' => (clone $metricsQuery)->whereIn('status', ['pending_manager', 'pending_hr'])->count(),
            'approved_days' => (int) LeaveRequestDate::query()->whereHas('leaveRequest', fn ($q) => $q->when($companyScope !== null, fn ($nested) => $nested->whereIn('company_id', $companyScope))->where('status', 'approved')->when($type, fn ($nested) => $nested->where('type', $type)))->when($year !== null, fn ($q) => $q->whereYear('leave_date', $year))->count(),
        ];

        return view('modules.hris-leave-summary', compact('requests', 'type', 'status', 'year', 'perPage', 'sort', 'direction', 'metrics'));
    }

    public function edit(Request $request, LeaveRequestModel $leaveRequest): View
    {
        Gate::authorize('update', $leaveRequest);
        $leaveRequest->load(['employee.company', 'employee.manager', 'dates']);
        $employee = $leaveRequest->employee;
        $year = $leaveRequest->dates->first()?->leave_date?->year ?? now()->year;
        $quota = (int) HrisSetting::resolve($employee->company_id, 'hris.annual_leave_quota', 12);
        $usedLeave = (int) LeaveRequestDate::query()->whereYear('leave_date', $year)->whereHas('leaveRequest', fn ($q) => $q->where('employee_id', $employee->getKey())->where('type', 'leave')->where('status', 'approved'))->count();
        $sickDays = (int) LeaveRequestDate::query()->whereYear('leave_date', $year)->whereHas('leaveRequest', fn ($q) => $q->where('employee_id', $employee->getKey())->where('type', 'sick')->where('status', 'approved'))->count();
        $hrApprovers = HrisWorkflowApprover::query()->with('user')->forWorkflow((int) $employee->company_id, 'leave_sick', 'hr')->get();
        $disk = Storage::disk(config('filesystems.default', 's3'));
        $hasSupportingDocument = filled($leaveRequest->supporting_document_id) && $disk->exists((string) $leaveRequest->supporting_document_id);
        $supportingMimeType = $hasSupportingDocument ? ($disk->mimeType((string) $leaveRequest->supporting_document_id) ?: 'application/octet-stream') : null;
        $canPreviewSupportingDocument = $supportingMimeType === 'application/pdf' || str_starts_with((string) $supportingMimeType, 'image/');
        return view('modules.hris-leave-revision-modern', compact('employee', 'year', 'quota', 'usedLeave', 'sickDays', 'hrApprovers', 'leaveRequest', 'hasSupportingDocument', 'supportingMimeType', 'canPreviewSupportingDocument'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', LeaveRequestModel::class);
        $employee = Employee::query()->with(['company', 'manager'])->where('user_id', $request->user()->getKey())->where('status', 'active')->firstOrFail();
        $year = now()->year;
        $quota = (int) HrisSetting::resolve($employee->company_id, 'hris.annual_leave_quota', 12);
        $usedLeave = (int) LeaveRequestDate::query()->whereYear('leave_date', $year)->whereHas('leaveRequest', fn ($q) => $q->where('employee_id', $employee->getKey())->where('type', 'leave')->where('status', 'approved'))->count();
        $sickDays = (int) LeaveRequestDate::query()->whereYear('leave_date', $year)->whereHas('leaveRequest', fn ($q) => $q->where('employee_id', $employee->getKey())->where('type', 'sick')->where('status', 'approved'))->count();
        $hrApprovers = HrisWorkflowApprover::query()->with('user')->forWorkflow((int) $employee->company_id, 'leave_sick', 'hr')->get();
        $requestNumber = LeaveRequestModel::generateRequestNumber();
        return view('modules.hris-leave-form-layout', compact('employee', 'year', 'quota', 'usedLeave', 'sickDays', 'hrApprovers', 'requestNumber'));
    }

    public function store(LeaveRequestForm $request, LeaveRequestService $service): RedirectResponse
    {
        Gate::authorize('create', LeaveRequestModel::class);
        $employee = Employee::query()->where('user_id', $request->user()->getKey())->where('status', 'active')->firstOrFail();
        $data = $request->validated();
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $key = 'hris/leave-supporting/'.$employee->getKey().'/'.Str::ulid().'.'.$file->extension();
            Storage::disk(config('filesystems.default', 's3'))->putFileAs(dirname($key), $file, basename($key), ['visibility' => 'private']);
            $data['supporting_document_id'] = $key;
        }
        unset($data['supporting_document']);
        try { $service->submit($employee, $data, $request->user()->getKey()); } catch (OverlappingLeaveRequest $exception) { return back()->withInput()->withErrors(['dates' => $exception->getMessage()]); }
        return redirect()->route('workspace.hris.leave-requests')->with('status', 'Pengajuan berhasil dikirim.');
    }

    public function resubmit(LeaveRevisionForm $request, LeaveRequestModel $leaveRequest, LeaveRequestService $service): RedirectResponse
    {
        Gate::authorize('update', $leaveRequest);
        $employee = $leaveRequest->employee()->where('user_id', $request->user()->getKey())->where('status', 'active')->firstOrFail();
        $data = $request->validated();
        $disk = Storage::disk(config('filesystems.default', 's3'));
        $hasExistingSupportingDocument = filled($leaveRequest->supporting_document_id) && $disk->exists((string) $leaveRequest->supporting_document_id);
        if ($leaveRequest->type === 'sick' && ! $request->hasFile('supporting_document') && ! $hasExistingSupportingDocument) {
            return back()->withInput()->withErrors(['supporting_document' => 'Surat pendukung wajib tersedia untuk pengajuan sakit.']);
        }
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $key = 'hris/leave-supporting/'.$employee->getKey().'/'.Str::ulid().'.'.$file->extension();
            Storage::disk(config('filesystems.default', 's3'))->putFileAs(dirname($key), $file, basename($key), ['visibility' => 'private']);
            $data['supporting_document_id'] = $key;
        }
        unset($data['supporting_document']);
        try {
            $service->resubmit($leaveRequest, $employee, $data, $request->user()->getKey());
        } catch (OverlappingLeaveRequest $exception) {
            return back()->withInput()->withErrors(['dates' => $exception->getMessage()]);
        }
        return redirect()->route('workspace.hris.leave-requests.show', $leaveRequest)->with('status', 'Perubahan pengajuan berhasil dikirim ulang.');
    }
}
