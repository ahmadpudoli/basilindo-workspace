<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\LeaveRequest;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
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
            $personalLeave = LeaveRequest::query()->where(function ($query) use ($user): void {
                $query->where('created_by', $user->getKey())
                    ->orWhereHas('employee', fn ($employee) => $employee->where('user_id', $user->getKey()));
            });
            $personalOvertime = OvertimeRequest::query()->where(function ($query) use ($user): void {
                $query->where('created_by', $user->getKey())
                    ->orWhereHas('employee', fn ($employee) => $employee->where('user_id', $user->getKey()));
            });
            $personalClaims = Claim::query()->where(function ($query) use ($user): void {
                $query->where('created_by', $user->getKey())
                    ->orWhereHas('employee', fn ($employee) => $employee->where('user_id', $user->getKey()));
            });

            return view('modules.hris-dashboard-personal', [
                'canCreateLeave' => $canCreateLeave,
                'canViewApprovals' => $canViewApprovals,
                'approvalRows' => $approvalRows,
                'personalStats' => [
                    ['label' => 'Cuti & sakit saya', 'value' => (clone $personalLeave)->count()],
                    ['label' => 'Lembur saya', 'value' => (clone $personalOvertime)->count()],
                    ['label' => 'Reimbursement & cash advance saya', 'value' => (clone $personalClaims)->count()],
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

        return collect()
            ->concat(LeaveRequest::query()->with(['employee', 'dates'])->whereIn('employee_id', $subordinateIds)->where('status', 'pending_manager')->latest()->get()->map(fn ($item) => ['item' => $item, 'type' => 'leave', 'step' => 'manager', 'stage' => 'Atasan', 'label' => $item->type === 'sick' ? 'Sakit' : 'Cuti']))
            ->concat(OvertimeRequest::query()->with('employee')->whereIn('employee_id', $subordinateIds)->where('status', 'pending_manager')->latest()->get()->map(fn ($item) => ['item' => $item, 'type' => 'overtime', 'step' => 'manager', 'stage' => 'Atasan', 'label' => 'Lembur']))
            ->concat(Claim::query()->with('employee')->whereIn('employee_id', $subordinateIds)->where('status', 'pending_manager')->latest()->get()->map(fn ($item) => ['item' => $item, 'type' => 'claim', 'step' => 'manager', 'stage' => 'Atasan', 'label' => $item->type === 'cash_advance' ? 'Cash advance' : 'Reimbursement']))
            ->concat(LeaveRequest::query()->with(['employee', 'dates'])->whereIn('company_id', $hrCompanyIds)->where('status', 'pending_hr')->latest()->get()->map(fn ($item) => ['item' => $item, 'type' => 'leave', 'step' => 'hr', 'stage' => 'HR', 'label' => $item->type === 'sick' ? 'Sakit' : 'Cuti']))
            ->sortByDesc(fn (array $row) => $row['item']->created_at?->timestamp ?? 0)
            ->values();
    }
}
