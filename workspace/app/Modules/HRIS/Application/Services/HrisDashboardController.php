<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\LeaveRequest;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use App\Modules\HRIS\Domain\Models\HrisHoliday;
use App\Modules\HRIS\Domain\Models\OvertimeRequestEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HrisDashboardController extends Controller
{
    public function biodata(Request $request): View
    {
        // Biodata personal tidak mengikuti scope akses daftar karyawan/company.
        // Identitas tetap dibatasi oleh relasi employee.user_id akun login.
        $employee = Employee::query()->withoutGlobalScopes()->with(['company', 'manager'])
            ->where('user_id', $request->user()->getKey())
            ->whereNull('deleted_at')
            ->first();
        if (! $employee) return view('modules.hris-biodata-empty');
        $root = $employee;
        $visited = [];
        while ($root->manager && ! isset($visited[$root->getKey()])) {
            $visited[$root->getKey()] = true;
            $root = $root->manager;
        }

        $buildTree = function (Employee $node, int $level = 0) use (&$buildTree, $employee): array {
            $children = $node->subordinates()
                ->where('status', '!=', 'archived')
                ->orderBy('full_name')
                ->get()
                ->map(fn (Employee $child): array => $buildTree($child, $level + 1))
                ->all();
            $isCurrent = (string) $node->getKey() === (string) $employee->getKey();

            return [
                'employee' => $node->loadMissing('company'),
                'children' => $children,
                'is_current' => $isCurrent,
                'contains_current' => $isCurrent || collect($children)->contains(fn (array $child): bool => $child['contains_current']),
                'level' => $level,
            ];
        };

        return view('modules.hris-biodata-tree', [
            'employee' => $employee,
            'organizationTree' => $buildTree($root),
        ]);
    }

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->hasRole('super_admin') || $user->can('access_hris'), 403);

        $canViewEmployees = $user->hasRole('super_admin') || $user->can('view_any_employee');
        $canCreateEmployees = $user->hasRole('super_admin') || $user->can('create_employee');
        $canCreateLeave = $user->hasRole('super_admin') || $user->can('create_leave_request');
        $canViewLeave = $user->hasRole('super_admin') || $user->can('view_any_leave_request');
        $canViewApprovals = $user->hasRole('super_admin') || $user->can('view_hris_approvals');
        $approvalRows = $this->approvalRows($user);

        if (! $canViewEmployees) {
            $canCreateOvertime = $user->hasRole('super_admin') || $user->can('create_overtime');
            $canCreateClaim = $user->hasRole('super_admin') || $user->can('create_claim');
            $activeYear = (int) HrisSetting::resolve(null, 'hris.active_year', now()->year);
            $monthStart = now()->startOfMonth();
            $monthEnd = now()->endOfMonth();
            $employee = Employee::query()->where('user_id', $user->getKey())->first();

            $personalLeave = LeaveRequest::query()->where(function ($query) use ($user): void {
                $query->where('created_by', $user->getKey())
                    ->orWhere('requester_user_id', $user->getKey())
                    ->orWhereHas('employee', fn ($employee) => $employee->where('user_id', $user->getKey()));
            });
            $personalOvertime = OvertimeRequest::query()->where(function ($query) use ($user): void {
                $query->where('created_by', $user->getKey())
                    ->orWhere('requester_user_id', $user->getKey())
                    ->orWhereHas('employee', fn ($employee) => $employee->where('user_id', $user->getKey()));
            });
            $personalClaims = Claim::query()->where(function ($query) use ($user): void {
                $query->where('created_by', $user->getKey())
                    ->orWhere('requester_user_id', $user->getKey())
                    ->orWhereHas('employee', fn ($employee) => $employee->where('user_id', $user->getKey()));
            });

            $annualLeaveRequestQuery = (clone $personalLeave)->where('status', '!=', 'draft')->whereHas('dates', fn ($dates) => $dates->whereYear('leave_date', $activeYear));
            $approvedLeave = (clone $personalLeave)->where('type', 'leave')->where('status', 'approved')->whereHas('dates', fn ($dates) => $dates->whereYear('leave_date', $activeYear))->with(['dates' => fn ($dates) => $dates->whereYear('leave_date', $activeYear)])->get();
            $approvedSick = (clone $personalLeave)->where('type', 'sick')->where('status', 'approved')->whereHas('dates', fn ($dates) => $dates->whereYear('leave_date', $activeYear))->with(['dates' => fn ($dates) => $dates->whereYear('leave_date', $activeYear)])->get();
            $approvedLeaveDays = (int) $approvedLeave->sum(fn (LeaveRequest $item) => $item->dates->count());
            $approvedSickDays = (int) $approvedSick->sum(fn (LeaveRequest $item) => $item->dates->count());
            $leaveQuota = $employee
                ? max(0, (int) HrisSetting::resolveForYear((int) $employee->company_id, 'hris.annual_leave_quota', $activeYear, 12) - HrisHoliday::collectiveDaysForCompany((int) $employee->company_id, $activeYear))
                : 0;
            $remainingLeave = max(0, $leaveQuota - $approvedLeaveDays);

            $monthlyOvertime = (clone $personalOvertime)
                ->where('status', '!=', 'draft')
                ->whereHas('entries', fn ($entries) => $entries->whereBetween('work_date', [$monthStart->toDateString(), $monthEnd->toDateString()]));
            $monthlyApprovedOvertime = (clone $personalOvertime)
                ->where('status', 'approved')
                ->whereHas('entries', fn ($entries) => $entries->whereBetween('work_date', [$monthStart->toDateString(), $monthEnd->toDateString()]));
            $approvedOvertimeMinutes = (int) OvertimeRequestEntry::query()
                ->whereBetween('work_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->whereHas('overtimeRequest', function ($overtime) use ($user): void {
                    $overtime->where('status', 'approved')->where(function ($owner) use ($user): void {
                        $owner->where('created_by', $user->getKey())->orWhere('requester_user_id', $user->getKey())->orWhereHas('employee', fn ($employee) => $employee->where('user_id', $user->getKey()));
                    });
                })->sum('duration_minutes');

            $monthlyClaims = (clone $personalClaims)->where('type', 'reimbursement')->where('status', '!=', 'draft')->whereBetween('created_at', [$monthStart, $monthEnd]);
            $approvedClaimStatuses = ['approved', 'pending_payment', 'paid', 'completed'];
            $monthlyApprovedClaims = (clone $monthlyClaims)->whereIn('status', $approvedClaimStatuses);

            return view('modules.hris-dashboard-personal-cards', [
                'canCreateLeave' => $canCreateLeave,
                'canViewApprovals' => $canViewApprovals,
                'approvalRows' => $approvalRows,
                'personalStats' => [
                    ['label' => 'Cuti & sakit', 'value' => $annualLeaveRequestQuery->count(), 'period' => "Tahun {$activeYear}", 'details' => ["Cuti disetujui: {$approvedLeaveDays} hari", "Sakit disetujui: {$approvedSickDays} hari", 'Sisa kuota cuti: '.($employee ? "{$remainingLeave} hari" : 'belum tersedia')], 'view_url' => $user->hasRole('super_admin') || $user->can('access_hris') ? route('workspace.hris.leave-requests', ['year' => $activeYear]) : null, 'create_url' => $canCreateLeave ? route('workspace.hris.leave-requests.create') : null],
                    ['label' => 'Lembur', 'value' => $monthlyOvertime->count(), 'period' => now()->translatedFormat('F Y'), 'details' => ['Disetujui: '.$monthlyApprovedOvertime->count().' pengajuan', 'Durasi disetujui: '.intdiv($approvedOvertimeMinutes, 60).' jam '.($approvedOvertimeMinutes % 60).' menit'], 'view_url' => $user->hasRole('super_admin') || $user->can('view_any_overtime') ? route('workspace.hris.overtime', ['date_from' => $monthStart->toDateString(), 'date_to' => now()->toDateString()]) : null, 'create_url' => $canCreateOvertime ? route('workspace.hris.overtime.create') : null],
                    ['label' => 'Reimbursement', 'value' => $monthlyClaims->count(), 'period' => now()->translatedFormat('F Y'), 'details' => ['Disetujui: '.$monthlyApprovedClaims->count().' pengajuan', 'Total disetujui: Rp '.number_format((float) $monthlyApprovedClaims->sum('amount'), 0, ',', '.')], 'view_url' => $user->hasRole('super_admin') || $user->can('view_any_claim') ? route('workspace.hris.claims', ['type' => 'reimbursement', 'from' => $monthStart->toDateString(), 'to' => now()->toDateString()]) : null, 'create_url' => $canCreateClaim ? route('workspace.hris.claims.create', ['type' => 'reimbursement']) : null],
                ],
            ]);
        }

        $employees = $canViewEmployees ? Employee::query()->visibleTo($user) : null;
        $leave = $canViewLeave
            ? LeaveRequest::query()->when(! $user->hasRole('super_admin'), fn ($q) => $q->whereIn('company_id', $user->companies()->pluck('companies.id')))
            : null;

        return view('modules.hris-dashboard', [
            'canCreateEmployees' => $canCreateEmployees,
            'canCreateLeave' => $canCreateLeave,
            'canViewApprovals' => $canViewApprovals,
            'approvalRows' => $approvalRows,
            'canViewEmployees' => $canViewEmployees,
            'stats' => [
                ['label' => 'Karyawan aktif', 'value' => $employees ? (clone $employees)->where('status', 'active')->count() : 0],
                ['label' => 'Pengajuan menunggu', 'value' => $leave ? (clone $leave)->whereIn('status', ['pending_manager', 'pending_hr'])->count() : 0],
                ['label' => 'Cuti disetujui tahun ini', 'value' => $leave ? (clone $leave)->where('type', 'leave')->where('status', 'approved')->whereHas('dates', fn ($q) => $q->whereYear('leave_date', now()->year))->sum('total_days') : 0],
            ],
            'employees' => $employees ? $employees->with(['company', 'manager'])->latest()->limit(5)->get() : collect(),
        ]);
    }

    private function approvalRows($user): Collection
    {
        if (! $user->hasRole('super_admin') && ! $user->can('view_hris_approvals')) return collect();

        $manager = Employee::query()->where('user_id', $user->getKey())->first();
        $subordinateIds = $manager ? Employee::query()->where('manager_id', $manager->getKey())->pluck('id') : collect();
        $hrCompanyIds = HrisWorkflowApprover::query()->where('workflow_key', 'leave_sick')->where('step', 'hr')->where('user_id', $user->getKey())->where('is_active', true)->pluck('company_id');
        $financeCompanyIds = HrisWorkflowApprover::query()->where('workflow_key', 'claim')->where('step', 'finance')->where('user_id', $user->getKey())->where('is_active', true)->pluck('company_id');
        $finalCompanyIds = HrisWorkflowApprover::query()->where('workflow_key', 'claim')->where('step', 'final')->where('user_id', $user->getKey())->where('is_active', true)->pluck('company_id');

        return collect()
            ->concat(LeaveRequest::query()->with(['employee', 'dates'])->whereIn('employee_id', $subordinateIds)->where('status', 'pending_manager')->latest()->get()->map(fn ($item) => ['item' => $item, 'type' => 'leave', 'step' => 'manager', 'stage' => 'Atasan', 'label' => $item->type === 'sick' ? 'Sakit' : 'Cuti']))
            ->concat(OvertimeRequest::query()->with('employee')->whereIn('employee_id', $subordinateIds)->where('status', 'pending_manager')->latest()->get()->map(fn ($item) => ['item' => $item, 'type' => 'overtime', 'step' => 'manager', 'stage' => 'Atasan', 'label' => 'Lembur']))
            ->concat(Claim::query()->with('employee')->whereIn('employee_id', $subordinateIds)->where('status', 'pending_manager')->latest()->get()->map(fn ($item) => ['item' => $item, 'type' => 'claim', 'step' => 'manager', 'stage' => 'Atasan', 'label' => $item->type === 'cash_advance' ? 'Cash advance' : 'Reimbursement']))
            ->concat(LeaveRequest::query()->with(['employee', 'dates'])->whereIn('company_id', $hrCompanyIds)->where('status', 'pending_hr')->latest()->get()->map(fn ($item) => ['item' => $item, 'type' => 'leave', 'step' => 'hr', 'stage' => 'HR', 'label' => $item->type === 'sick' ? 'Sakit' : 'Cuti']))
            ->concat(OvertimeRequest::query()->with(['employee', 'entries'])->whereIn('company_id', $hrCompanyIds)->where('status', 'pending_hr')->latest()->get()->map(fn ($item) => ['item' => $item, 'type' => 'overtime', 'step' => 'hr', 'stage' => 'HR', 'label' => 'Lembur']))
            ->concat(Claim::query()->with('employee')->whereIn('company_id', $financeCompanyIds)->where('status', 'pending_finance')->latest()->get()->map(fn ($item) => ['item' => $item, 'type' => 'claim', 'step' => 'finance', 'stage' => 'Finance', 'label' => $item->type === 'cash_advance' ? 'Cash advance' : 'Reimbursement']))
            ->concat(Claim::query()->with('employee')->whereIn('company_id', $finalCompanyIds)->where('status', 'pending_final')->latest()->get()->map(fn ($item) => ['item' => $item, 'type' => 'claim', 'step' => 'final', 'stage' => 'Approver final', 'label' => $item->type === 'cash_advance' ? 'Cash advance' : 'Reimbursement']))
            ->sortByDesc(fn (array $row) => $row['item']->created_at?->timestamp ?? 0)
            ->values();
    }
}
