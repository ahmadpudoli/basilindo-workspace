<?php

use App\Modules\HRIS\Domain\Models\Employee;
use Core\Models\Company;
use Core\Models\User;

it('shows personal biodata without requiring employee list access or company scope', function () {
    $user = User::factory()->create();
    $company = Company::create([
        'name' => 'HRIS Biodata Test',
        'code' => 'HRIS-BIODATA',
        'entity_type' => 'company',
        'classification' => 'internal',
        'roles' => ['operating_company'],
        'status' => 'active',
    ]);
    Employee::create([
        'company_id' => $company->id,
        'user_id' => $user->id,
        'employee_number' => 'BIO-001',
        'full_name' => 'Karyawan Biodata',
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->get(route('workspace.hris.biodata'))
        ->assertOk()
        ->assertSee('Karyawan Biodata');
});
