<?php

use App\Modules\HRIS\Application\Services\ClaimService;
use App\Modules\HRIS\Application\Services\HrisWorkflowService;
use App\Modules\HRIS\Application\Services\OvertimeService;
use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use App\Modules\CRM\Domain\Models\CrmAccount;
use App\Modules\CRM\Domain\Models\CrmOpportunity;
use Core\Models\Company;
use Core\Models\Role;
use Core\Models\User;
use Illuminate\Support\Facades\DB;

function hrisEmployeeForTest(string $code = 'HRIS-WORKFLOW'): array
{
    $user = User::factory()->create();
    $company = Company::create(['name' => $code, 'code' => $code, 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $manager = Employee::create(['company_id' => $company->id, 'user_id' => $user->id, 'employee_number' => 'MGR-'.fake()->unique()->numberBetween(100, 999), 'full_name' => 'Atasan Workflow', 'status' => 'active']);
    $employee = Employee::create(['company_id' => $company->id, 'manager_id' => $manager->id, 'employee_number' => 'EMP-'.fake()->unique()->numberBetween(100, 999), 'full_name' => 'Karyawan Workflow', 'status' => 'active']);
    return [$user, $company, $employee];
}

it('renders only accessible company projects on the overtime form and rejects invalid project input', function () {
    [$manager, $company, $employee] = hrisEmployeeForTest('OT-PROJECT-FORM');
    $user = User::factory()->create();
    $user->companies()->attach($company);
    foreach (['create_overtime', 'view_project', 'access_hris'] as $permission) {
        $user->givePermissionTo(\Core\Models\Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']));
    }
    $employee->update(['user_id' => $user->id]);
    $accessibleCompany = Company::create(['name' => 'OT-PROJECT-ACCESS', 'code' => 'OT-PROJECT-ACCESS', 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $user->companies()->attach($accessibleCompany);
    $project = \Core\Models\Project::create(['company_id' => $company->id, 'name' => 'Allowed Overtime Project', 'ticket_prefix' => 'OTF']);
    $project->members()->attach($user);
    $crossCompanyProject = \Core\Models\Project::create(['company_id' => $accessibleCompany->id, 'name' => 'Accessible Company Project', 'ticket_prefix' => 'OTC']);
    $crossCompanyProject->members()->attach($user);
    \Core\Models\Project::create(['company_id' => $company->id, 'name' => 'Hidden Overtime Project', 'ticket_prefix' => 'OTH']);
    $this->actingAs($user)->get(route('workspace.hris.overtime.create'))->assertOk()
        ->assertSee('Referensi pekerjaan')->assertSee('Cari nama project atau client')->assertSee('Status')->assertSee('Client')->assertSee('Allowed Overtime Project')->assertSee('Accessible Company Project')->assertDontSee('Hidden Overtime Project');
    $data = ['overtime_type' => 'external_project', 'project_id' => 'not-an-id', 'entries' => [['work_date' => now()->subDay()->format('Y-m-d'), 'start_time' => '18:00', 'end_time' => '20:00', 'work_description' => 'Project work']]];
    $this->post(route('workspace.hris.overtime.store'), $data)->assertSessionHasErrors('project_id');
    $this->post(route('workspace.hris.overtime.store'), [...$data, 'project_id' => ''])->assertSessionHasNoErrors()->assertRedirect(route('workspace.hris.overtime'));
});

it('persists an optional project on external overtime and clears it on internal revision', function () {
    [$user, $company, $employee] = hrisEmployeeForTest('HRIS-OT-PROJECT');
    $user->givePermissionTo(\Core\Models\Permission::firstOrCreate(['name' => 'view_project', 'guard_name' => 'web']));
    $project = \Core\Models\Project::create(['company_id' => $company->id, 'name' => 'Project Lembur', 'ticket_prefix' => 'OTP']);
    $project->members()->attach($user);
    $data = ['overtime_type' => 'external_project', 'project_id' => $project->id, 'work_date' => '2026-10-24', 'start_time' => '18:00', 'end_time' => '20:00', 'reason' => 'Pekerjaan project'];
    $service = app(OvertimeService::class);
    $request = $service->submit($employee, $data, $user->id);
    expect((int) $request->fresh()->project_id)->toBe($project->id);
    $request->update(['status' => 'needs_revision']);
    $revised = $service->resubmit($request, $employee, [...$data, 'overtime_type' => 'internal'], $user->id);
    expect($revised->fresh()->project_id)->toBeNull();
    $withoutProject = $service->submit($employee, [...$data, 'project_id' => null], $user->id);
    expect($withoutProject->fresh()->project_id)->toBeNull();
});

it('supports an opportunity as external overtime reference and snapshots its status', function () {
    [$user, $company, $employee] = hrisEmployeeForTest('HRIS-OT-OPPORTUNITY');
    $user->companies()->attach($company);
    $user->givePermissionTo(\Core\Models\Permission::firstOrCreate(['name' => 'create_overtime', 'guard_name' => 'web']));
    $user->givePermissionTo(\Core\Models\Permission::firstOrCreate(['name' => 'view_crm_opportunity', 'guard_name' => 'web']));
    $accessibleCompany = Company::create(['name' => 'OT-OPPORTUNITY-ACCESS', 'code' => 'OT-OPPORTUNITY-ACCESS', 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $user->companies()->attach($accessibleCompany);
    $account = CrmAccount::create(['company_id' => $accessibleCompany->id, 'name' => 'Account Lembur', 'status' => 'prospect']);
    $opportunity = CrmOpportunity::create(['company_id' => $accessibleCompany->id, 'account_id' => $account->id, 'name' => 'Implementasi Opportunity', 'stage' => 'qualification']);

    $this->actingAs($user)->get(route('workspace.hris.overtime.create'))->assertOk()
        ->assertSee('Pilih opportunity')->assertSee('Cari opportunity, status, client, atau kontak')
        ->assertSee('Implementasi Opportunity')->assertSee('Account Lembur');

    $request = app(OvertimeService::class)->submit($employee, [
        'overtime_type' => 'external_project', 'work_reference_type' => 'opportunity', 'opportunity_id' => $opportunity->id,
        'work_date' => '2026-10-24', 'start_time' => '18:00', 'end_time' => '20:00', 'reason' => 'Pekerjaan prospek',
    ], $user->id);

    expect($request->fresh()->work_reference_type)->toBe('opportunity')
        ->and($request->fresh()->opportunity_id)->toBe($opportunity->id)
        ->and($request->fresh()->reference_name_snapshot)->toBe('Implementasi Opportunity')
        ->and($request->fresh()->reference_status_snapshot)->toBe('Prospek · Stage: qualification · Closing: —');
});

it('rejects an inaccessible or nonexistent overtime project without creating a request', function (string $scope) {
    [$user, $company, $employee] = hrisEmployeeForTest('OT-DENY-'.$scope);
    $otherCompany = Company::create(['name' => 'Other company', 'code' => 'OT-OTHER-'.$scope, 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $project = \Core\Models\Project::create(['company_id' => $scope === 'other_company' ? $otherCompany->id : $company->id, 'name' => 'Restricted project', 'ticket_prefix' => 'OTR']);
    if (in_array($scope, ['other_company', 'no_membership'], true)) {
        $user->givePermissionTo(\Core\Models\Permission::firstOrCreate(['name' => 'view_project', 'guard_name' => 'web']));
    }
    if ($scope === 'other_company') {
        $project->members()->attach($user);
    }
    $count = OvertimeRequest::count();
    try {
        app(OvertimeService::class)->submit($employee, ['overtime_type' => 'external_project', 'project_id' => $scope === 'missing' ? 999999999 : $project->id, 'work_date' => '2026-10-24', 'start_time' => '18:00', 'end_time' => '20:00', 'reason' => 'Project'], $user->id);
        $this->fail('An inaccessible project must be rejected.');
    } catch (\Illuminate\Validation\ValidationException $exception) {
        expect($exception->errors())->toHaveKey('project_id');
    }
    expect(OvertimeRequest::count())->toBe($count);
})->with(['other_company', 'no_access', 'no_membership', 'missing']);

it('rounds overtime according to company parameters and allows post-event submission', function () {
    [$user, $company, $employee] = hrisEmployeeForTest('HRIS-OT');
    HrisSetting::create(['company_id' => $company->id, 'key' => 'hris.overtime_rounding', 'value' => ['mode' => 'up', 'interval_minutes' => 30], 'is_active' => true]);
    $request = app(OvertimeService::class)->submit($employee, ['work_date' => '2026-04-01', 'start_time' => '18:00', 'end_time' => '20:01', 'reason' => 'Closing'], $user->id);
    expect($request)->toBeInstanceOf(OvertimeRequest::class)->and($request->duration_minutes)->toBe(150);
});

it('stores multiple overtime realization entries and routes manager approval to HR', function () {
    [$managerUser, $company, $employee] = hrisEmployeeForTest('HRIS-OT-MULTI');
    $hrUser = User::factory()->create();
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'leave_sick', 'step' => 'hr', 'user_id' => $hrUser->id, 'is_active' => true]);
    HrisSetting::create(['company_id' => $company->id, 'key' => 'hris.overtime_rounding', 'value' => ['mode' => 'down', 'interval_minutes' => 30], 'is_active' => true]);
    $request = app(OvertimeService::class)->submit($employee, [
        'overtime_type' => 'internal', 'work_location' => 'Kantor', 'reason' => 'Closing bulanan',
        'entries' => [
            ['work_date' => '2026-10-24', 'start_time' => '18:00', 'end_time' => '20:20', 'work_description' => 'Rekonsiliasi'],
            ['work_date' => '2026-10-25', 'start_time' => '23:00', 'end_time' => '01:10', 'work_description' => 'Finalisasi laporan'],
        ],
    ], $managerUser->id);

    expect($request->status)->toBe('pending_manager')->and($request->entries()->count())->toBe(2)->and($request->duration_minutes)->toBe(240);
    $approved = app(HrisWorkflowService::class)->decide($request, 'overtime', 'manager', 'approve', $managerUser, 'Sesuai realisasi');
    expect($approved->status)->toBe('pending_hr');
});

it('routes substantive overtime revision back to manager', function () {
    [$managerUser, $company, $employee] = hrisEmployeeForTest('HRIS-OT-REVISION');
    $hrUser = User::factory()->create();
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'leave_sick', 'step' => 'hr', 'user_id' => $hrUser->id, 'is_active' => true]);
    $request = app(OvertimeService::class)->submit($employee, ['work_date' => '2026-10-24', 'start_time' => '18:00', 'end_time' => '20:00', 'reason' => 'Closing'], $managerUser->id);
    app(HrisWorkflowService::class)->decide($request, 'overtime', 'manager', 'approve', $managerUser);
    $revision = app(HrisWorkflowService::class)->decide($request->fresh(), 'overtime', 'hr', 'request_revision', $hrUser, 'Perbaiki uraian pekerjaan', 'substantive');
    expect($revision->status)->toBe('needs_revision')->and($revision->revision_requested_role)->toBe('hr');
});

it('routes a manager claim approval to finance and records the event', function () {
    [$user, $company, $employee] = hrisEmployeeForTest('HRIS-CLAIM');
    $claim = app(ClaimService::class)->submit($employee, ['type' => 'reimbursement', 'category' => 'Transport', 'purpose' => 'Meeting', 'amount' => 100000, 'currency' => 'IDR'], $user->id);
    $result = app(HrisWorkflowService::class)->decide($claim, 'claim', 'manager', 'approve', $user, 'Disetujui atasan');
    expect($result->status)->toBe('pending_finance')->and($result->manager_approved_by)->toBe($user->id)->and(DB::connection('hris')->table('approval_events')->where('request_id', $claim->id)->count())->toBe(1);
});

it('routes a claim above the company threshold to the configured final approver', function () {
    [$manager, $company, $employee] = hrisEmployeeForTest('HRIS-CLAIM-THRESHOLD');
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'claim', 'step' => 'finance', 'user_id' => $manager->id, 'is_active' => true]);
    HrisWorkflowApprover::create(['company_id' => $company->id, 'workflow_key' => 'claim', 'step' => 'final', 'user_id' => $manager->id, 'is_active' => true]);
    HrisSetting::create(['company_id' => $company->id, 'key' => 'hris.claim_final_approval_threshold', 'value' => 500000, 'is_active' => true]);
    $claim = app(ClaimService::class)->submit($employee, ['type' => 'reimbursement', 'items' => [['category' => 'Transport', 'description' => 'Meeting', 'currency' => 'IDR', 'amount' => 600000]]], $manager->id);
    expect($claim->status)->toBe('pending_manager')->and($claim->expenseItems()->count())->toBe(1);
    app(HrisWorkflowService::class)->decide($claim, 'claim', 'manager', 'approve', $manager);
    $finance = app(HrisWorkflowService::class)->decide($claim->fresh(), 'claim', 'finance', 'approve', $manager);
    expect($finance->status)->toBe('pending_final');
    $final = app(HrisWorkflowService::class)->decide($finance->fresh(), 'claim', 'final', 'approve', $manager);
    expect($final->status)->toBe('approved');
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
