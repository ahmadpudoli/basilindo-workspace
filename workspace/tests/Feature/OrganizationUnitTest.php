<?php

use Core\Models\Company;
use Core\Models\OrganizationUnit;
use Core\Models\User;

it('stores a hierarchy for an internal operating company', function () {
    $company = Company::create(['name' => 'Operating Org Test', 'code' => 'ORG-TEST', 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    $division = OrganizationUnit::create(['company_id' => $company->id, 'code' => 'FIN', 'name' => 'Finance', 'type' => 'division', 'status' => 'active', 'sort_order' => 1]);
    $department = OrganizationUnit::create(['company_id' => $company->id, 'parent_id' => $division->id, 'code' => 'FIN-ADM', 'name' => 'Finance Admin', 'type' => 'department', 'status' => 'active', 'sort_order' => 1]);

    expect($department->parent->is($division))->toBeTrue()->and($division->children()->count())->toBe(1);
});

it('rejects organization unit access outside the users company scope', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Scoped Org Test', 'code' => 'ORG-SCOPE', 'entity_type' => 'company', 'classification' => 'internal', 'roles' => ['operating_company'], 'status' => 'active']);
    OrganizationUnit::create(['company_id' => $company->id, 'code' => 'OPS', 'name' => 'Operations', 'type' => 'division', 'status' => 'active', 'sort_order' => 1]);

    expect(OrganizationUnit::query()->visibleTo($user)->count())->toBe(0);
});
