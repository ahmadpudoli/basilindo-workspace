<?php

use App\Modules\HRIS\Application\Services\LeaveRequestService;
use App\Modules\HRIS\Application\Services\HrisWorkflowService;
use App\Modules\HRIS\Application\Services\LeaveRequestForm;
use App\Modules\HRIS\Domain\Exceptions\OverlappingLeaveRequest;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\LeaveRequest;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use Core\Models\Company;
use Core\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

it('validates leave form rules on the server', function () {
    $validator = Validator::make([
        'type' => 'sick',
        'dates' => ['2026-04-01', '2026-04-01'],
        'reason' => '',
    ], (new LeaveRequestForm())->rules(), (new LeaveRequestForm())->messages());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('dates.1'))->toBeTrue()
        ->and($validator->errors()->has('reason'))->toBeTrue()
        ->and($validator->errors()->has('supporting_document'))->toBeTrue();
});

it('rejects overlapping active leave requests for the same employee', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'HRIS Test', 'code' => 'HRIS-TEST', 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $user->companies()->attach($company->id);
    $employee = Employee::create(['company_id' => $company->id, 'employee_number' => 'EMP-001', 'full_name' => 'Karyawan Test', 'status' => 'active']);
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'leave_sick', 'step' => 'hr', 'user_id' => $user->id, 'is_active' => true]);
    HrisSetting::create(['company_id' => $company->id, 'key' => 'hris.annual_leave_quota', 'value' => 12, 'effective_from' => '2026-01-01', 'is_active' => true]);
    HrisSetting::create(['company_id' => $company->id, 'key' => 'hris.leave_submission_cutoff', 'value' => '2026-12-31', 'effective_from' => '2026-01-01', 'is_active' => true]);
    app(LeaveRequestService::class)->submit($employee, ['type' => 'leave', 'dates' => ['2026-01-10', '2026-01-11', '2026-01-12'], 'reason' => 'Cuti'], $user->id);
    expect(fn () => app(LeaveRequestService::class)->submit($employee, ['type' => 'sick', 'dates' => ['2026-01-12', '2026-01-13'], 'reason' => 'Sakit'], $user->id))->toThrow(OverlappingLeaveRequest::class);
});

it('rejects a leave request whose dates cross calendar years', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'HRIS Year Test', 'code' => 'HRIS-YEAR', 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $employee = Employee::create(['company_id' => $company->id, 'employee_number' => 'EMP-YEAR', 'full_name' => 'Karyawan Tahun', 'status' => 'active']);
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'leave_sick', 'step' => 'hr', 'user_id' => $user->id, 'is_active' => true]);

    expect(fn () => app(LeaveRequestService::class)->submit($employee, ['type' => 'leave', 'dates' => ['2026-12-31', '2027-01-01'], 'reason' => 'Cuti lintas tahun'], $user->id))
        ->toThrow(ValidationException::class);
});

it('does not consume leave quota for sickness', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'HRIS Sick Test', 'code' => 'HRIS-SICK', 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $employee = Employee::create(['company_id' => $company->id, 'employee_number' => 'EMP-002', 'full_name' => 'Karyawan Sakit', 'status' => 'active']);
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'leave_sick', 'step' => 'hr', 'user_id' => $user->id, 'is_active' => true]);
    $request = app(LeaveRequestService::class)->submit($employee, ['type' => 'sick', 'dates' => ['2026-02-01', '2026-02-02', '2026-02-03'], 'reason' => 'Sakit'], $user->id);
    expect($request->total_days)->toBe(3)
        ->and($request->requester_user_id)->toBe($user->id)
        ->and($request->dates()->pluck('leave_date')->map->toDateString()->all())->toBe(['2026-02-01', '2026-02-02', '2026-02-03'])
        ->and(LeaveRequest::query()->where('employee_id', $employee->id)->where('type', 'leave')->sum('total_days'))->toBe(0);
});

it('rejects leave dates when the requested year has no quota configuration', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'HRIS Missing Year Test', 'code' => 'HRIS-MISSING-YEAR', 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $employee = Employee::create(['company_id' => $company->id, 'employee_number' => 'EMP-MISSING-YEAR', 'full_name' => 'Karyawan Tahun Belum Diatur', 'status' => 'active']);
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'leave_sick', 'step' => 'hr', 'user_id' => $user->id, 'is_active' => true]);
    HrisSetting::create(['company_id' => $company->id, 'key' => 'hris.leave_submission_cutoff', 'value' => '2030-12-31', 'effective_from' => '2030-01-01', 'is_active' => true]);

    expect(fn () => app(LeaveRequestService::class)->submit($employee, ['type' => 'leave', 'dates' => ['2030-01-10'], 'reason' => 'Cuti'], $user->id))
        ->toThrow(ValidationException::class, 'Kuota cuti untuk tahun 2030 belum dikonfigurasi');
});

it('rejects leave dates when the requested year has no submission cutoff', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'HRIS Missing Cutoff Test', 'code' => 'HRIS-MISSING-CUTOFF', 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $employee = Employee::create(['company_id' => $company->id, 'employee_number' => 'EMP-MISSING-CUTOFF', 'full_name' => 'Karyawan Cutoff Belum Diatur', 'status' => 'active']);
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'leave_sick', 'step' => 'hr', 'user_id' => $user->id, 'is_active' => true]);
    HrisSetting::create(['company_id' => $company->id, 'key' => 'hris.annual_leave_quota', 'value' => 12, 'effective_from' => '2031-01-01', 'is_active' => true]);

    expect(fn () => app(LeaveRequestService::class)->submit($employee, ['type' => 'leave', 'dates' => ['2031-01-10'], 'reason' => 'Cuti'], $user->id))
        ->toThrow(ValidationException::class, 'Batas akhir pengajuan cuti untuk tahun 2031 belum dikonfigurasi');
});

it('uses the configured annual leave quota for the requested year', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'HRIS Quota Test', 'code' => 'HRIS-QUOTA', 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $employee = Employee::create(['company_id' => $company->id, 'employee_number' => 'EMP-003', 'full_name' => 'Karyawan Kuota', 'status' => 'active']);
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'leave_sick', 'step' => 'hr', 'user_id' => $user->id, 'is_active' => true]);
    HrisSetting::create(['company_id' => $company->id, 'key' => 'hris.annual_leave_quota', 'value' => 12, 'effective_from' => '2026-01-01', 'is_active' => true]);
    HrisSetting::create(['company_id' => $company->id, 'key' => 'hris.leave_submission_cutoff', 'value' => '2026-12-31', 'effective_from' => '2026-01-01', 'is_active' => true]);
    $approved = app(LeaveRequestService::class)->submit($employee, ['type' => 'leave', 'dates' => collect(range(1, 12))->map(fn ($day) => sprintf('2026-03-%02d', $day))->all(), 'reason' => 'Cuti'], $user->id);
    $approved->update(['status' => 'approved']);
    expect(fn () => app(LeaveRequestService::class)->submit($employee, ['type' => 'leave', 'dates' => ['2026-03-20'], 'reason' => 'Cuti'], $user->id))->toThrow(ValidationException::class);
});

it('routes substantive revision requested by HR back through the manager', function () {
    $requester = User::factory()->create();
    $managerUser = User::factory()->create();
    $company = Company::create(['name' => 'HRIS Revision Test', 'code' => 'HRIS-REVISION', 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $manager = Employee::create(['company_id' => $company->id, 'user_id' => $managerUser->id, 'employee_number' => 'MGR-001', 'full_name' => 'Manager', 'status' => 'active']);
    $employee = Employee::create(['company_id' => $company->id, 'user_id' => $requester->id, 'employee_number' => 'EMP-REV', 'full_name' => 'Requester', 'manager_id' => $manager->id, 'status' => 'active']);
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'leave_sick', 'step' => 'hr', 'user_id' => $managerUser->id, 'is_active' => true]);
    HrisSetting::create(['company_id' => $company->id, 'key' => 'hris.annual_leave_quota', 'value' => 12, 'effective_from' => '2026-01-01', 'is_active' => true]);
    HrisSetting::create(['company_id' => $company->id, 'key' => 'hris.leave_submission_cutoff', 'value' => '2026-12-31', 'effective_from' => '2026-01-01', 'is_active' => true]);
    $leave = app(LeaveRequestService::class)->submit($employee, ['type' => 'leave', 'dates' => ['2026-05-01'], 'reason' => 'Cuti'], $requester->id);
    $leave->update(['status' => 'pending_hr']);

    app(HrisWorkflowService::class)->decide($leave->fresh(), 'leave', 'hr', 'request_revision', $managerUser, 'Tanggal perlu diperbaiki', 'substantive');
    expect($leave->fresh()->status)->toBe('needs_revision');

    $resubmitted = app(LeaveRequestService::class)->resubmit($leave->fresh(), $employee, ['type' => 'leave', 'dates' => ['2026-05-02'], 'reason' => 'Cuti diperbaiki'], $requester->id);
    expect($resubmitted->status)->toBe('pending_manager')
        ->and($resubmitted->dates()->pluck('leave_date')->map->toDateString()->all())->toBe(['2026-05-02']);
});

it('routes administrative HR revision directly back to HR after resubmission', function () {
    $requester = User::factory()->create();
    $company = Company::create(['name' => 'HRIS Admin Revision Test', 'code' => 'HRIS-ADMIN-REV', 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $employee = Employee::create(['company_id' => $company->id, 'user_id' => $requester->id, 'employee_number' => 'EMP-ADMIN-REV', 'full_name' => 'Requester Admin', 'status' => 'active']);
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'leave_sick', 'step' => 'hr', 'user_id' => $requester->id, 'is_active' => true]);
    $leave = app(LeaveRequestService::class)->submit($employee, ['type' => 'sick', 'dates' => ['2026-06-01'], 'reason' => 'Sakit'], $requester->id);
    $leave->update(['status' => 'pending_hr']);
    app(HrisWorkflowService::class)->decide($leave->fresh(), 'leave', 'hr', 'request_revision', $requester, 'Lampiran perlu dilengkapi', 'administrative');
    $resubmitted = app(LeaveRequestService::class)->resubmit($leave->fresh(), $employee, ['type' => 'sick', 'dates' => ['2026-06-01'], 'reason' => 'Sakit dengan lampiran'], $requester->id);
    expect($resubmitted->status)->toBe('pending_hr');
});
