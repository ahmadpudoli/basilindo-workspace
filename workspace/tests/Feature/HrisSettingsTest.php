<?php

use App\Modules\HRIS\Domain\Models\HrisSetting;
use Core\Models\Company;
use Core\Models\Role;
use Core\Models\User;

it('rejects a second active leave submission cutoff in the same year across scopes', function () {
    $admin = User::factory()->create(['is_system_account' => true]);
    $admin->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));
    $company = Company::create([
        'name' => 'Carano',
        'code' => 'CARANO-CUTOFF-TEST',
        'entity_type' => 'company',
        'classification' => 'internal',
        'roles' => ['operating_company'],
        'status' => 'active',
    ]);

    HrisSetting::create([
        'company_id' => null,
        'key' => 'hris.leave_submission_cutoff',
        'value' => '2026-12-25',
        'effective_from' => '2026-01-01',
        'is_active' => true,
    ]);

    $this->actingAs($admin)->post(route('workspace.settings.hris.store'), [
        'company_id' => $company->id,
        'key' => 'hris.leave_submission_cutoff',
        'value' => '2026-10-21',
        'effective_from' => '2026-07-09',
    ])->assertRedirect()->assertSessionHasErrors('key');

    expect(HrisSetting::query()->where('key', 'hris.leave_submission_cutoff')->count())->toBe(1);
});

it('renders effective dates in Indonesian numeric date format on the HRIS settings page', function () {
    $admin = User::factory()->create(['is_system_account' => true]);
    $admin->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));
    HrisSetting::create([
        'key' => 'hris.annual_leave_quota',
        'value' => 12,
        'effective_from' => '2026-01-01',
        'is_active' => true,
    ]);

    $this->actingAs($admin)->get(route('workspace.settings.hris'))
        ->assertOk()
        ->assertSee('01/01/2026');
});
