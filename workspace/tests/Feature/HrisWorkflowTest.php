<?php

use App\Modules\HRIS\Application\Services\ClaimService;
use App\Modules\HRIS\Application\Services\HrisWorkflowService;
use App\Modules\HRIS\Application\Services\OvertimeService;
use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use Core\Models\Company;
use Core\Models\Role;
use Core\Models\User;
use Illuminate\Support\Facades\DB;

function hrisEmployeeForTest(string $code = 'HRIS-WORKFLOW'): array
{
    $user = User::factory()->create();
    $company = Company::create(['name' => $code, 'code' => $code, 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $employee = Employee::create(['company_id' => $company->id, 'employee_number' => 'EMP-'.fake()->unique()->numberBetween(100, 999), 'full_name' => 'Karyawan Workflow', 'status' => 'active']);
    return [$user, $company, $employee];
}

it('rounds overtime according to company parameters and allows post-event submission', function () {
    [$user, $company, $employee] = hrisEmployeeForTest('HRIS-OT');
    HrisSetting::create(['company_id' => $company->id, 'key' => 'hris.overtime_rounding', 'value' => ['mode' => 'up', 'interval_minutes' => 30], 'is_active' => true]);
    $request = app(OvertimeService::class)->submit($employee, ['work_date' => '2026-04-01', 'start_time' => '18:00', 'end_time' => '20:01', 'reason' => 'Closing'], $user->id);
    expect($request)->toBeInstanceOf(OvertimeRequest::class)->and($request->duration_minutes)->toBe(150);
});

it('routes a manager claim approval to finance and records the event', function () {
    [$user, $company, $employee] = hrisEmployeeForTest('HRIS-CLAIM');
    $claim = app(ClaimService::class)->submit($employee, ['type' => 'reimbursement', 'category' => 'Transport', 'purpose' => 'Meeting', 'amount' => 100000, 'currency' => 'IDR'], $user->id);
    $result = app(HrisWorkflowService::class)->decide($claim, 'claim', 'manager', 'approve', $user, 'Disetujui atasan');
    expect($result->status)->toBe('pending_finance')->and($result->manager_approved_by)->toBe($user->id)->and(DB::table('hris_approval_events')->where('request_id', $claim->id)->count())->toBe(1);
});

it('requires accountability after finance approves a cash advance', function () {
    [$user, $company, $employee] = hrisEmployeeForTest('HRIS-CASH');
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'claim', 'step' => 'finance', 'user_id' => $user->id, 'is_active' => true]);
    $claim = app(ClaimService::class)->submit($employee, ['type' => 'cash_advance', 'category' => 'Hotel', 'purpose' => 'Business trip', 'amount' => 2000000, 'currency' => 'IDR'], $user->id);
    app(HrisWorkflowService::class)->decide($claim, 'claim', 'manager', 'approve', $user);
    $result = app(HrisWorkflowService::class)->decide($claim->fresh(), 'claim', 'finance', 'approve', $user);
    expect($result->status)->toBe('awaiting_accountability');
});

it('reactivates an archived workflow configuration instead of inserting a duplicate', function () {
    [$user, $company] = hrisEmployeeForTest('HRIS-WORKFLOW-RESTORE');
    $admin = User::query()->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->first();
    if (! $admin) {
        $admin = User::factory()->create(['is_system_account' => true]);
        $admin->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));
    }
    $configuration = HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'leave_sick', 'step' => 'hr', 'user_id' => $user->id, 'is_active' => true]);
    $configuration->delete();

    $this->actingAs($admin)->post(route('workspace.settings.hris.workflow.store'), [
        'company_id' => $company->id,
        'workflow_key' => 'leave_sick',
        'user_id' => $user->id,
    ])->assertRedirect();

    expect(HrisWorkflowApprover::withTrashed()->whereKey($configuration->getKey())->count())->toBe(1)
        ->and(HrisWorkflowApprover::query()->whereKey($configuration->getKey())->value('is_active'))->toBeTrue();
});
