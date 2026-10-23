<?php

use Core\Models\Company;
use Core\Models\Permission;
use Core\Models\User;
use App\Modules\DocumentManagement\Domain\Models\Document;
use App\Modules\DocumentManagement\Domain\Models\DocumentType;

it('shows only the module navigation allowed by permissions', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Navigation Company', 'code' => 'NAV-'.fake()->unique()->bothify('####')]);
    $user->companies()->attach($company);
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'view_any_document', 'guard_name' => 'web']));
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'access_documents', 'guard_name' => 'web']));
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'view_document', 'guard_name' => 'web']));

    $response = $this->actingAs($user)->get(route('workspace.documents.list'));

    $response->assertOk()
        ->assertSee(route('workspace.documents.list'), false)
        ->assertDontSee(route('workspace.company'), false)
        ->assertDontSee(route('workspace.crm'), false)
        ->assertDontSee('>Perusahaan<', false)
        ->assertDontSee('>Account<', false);

    $this->actingAs($user)->get(route('workspace.company'))->assertForbidden();
    $this->actingAs($user)->get(route('workspace.crm'))->assertForbidden();
});

it('does not show a module card when its access permission is missing', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Document Management')
        ->assertDontSee('CRM')
        ->assertDontSee('Project Management')
        ->assertDontSee('Company');
});

it('scopes the document dashboard statistics and latest documents to user companies', function () {
    $user = User::factory()->create();
    $allowedCompany = Company::create(['name' => 'Allowed Dashboard Company', 'code' => 'DASH-'.fake()->unique()->bothify('####')]);
    $otherCompany = Company::create(['name' => 'Other Dashboard Company', 'code' => 'OTHD-'.fake()->unique()->bothify('####')]);
    $user->companies()->attach($allowedCompany);
    $user->givePermissionTo([
        Permission::firstOrCreate(['name' => 'access_documents', 'guard_name' => 'web']),
        Permission::firstOrCreate(['name' => 'view_any_document', 'guard_name' => 'web']),
    ]);

    $allowedType = DocumentType::create(['company_id' => $allowedCompany->id, 'name' => 'Allowed Type', 'code' => 'allowed-type']);
    DocumentType::create(['company_id' => $otherCompany->id, 'name' => 'Hidden Type', 'code' => 'hidden-type']);
    DocumentType::create(['company_id' => null, 'name' => 'Global Type', 'code' => 'global-type']);
    Document::create(['company_id' => $allowedCompany->id, 'document_type_id' => $allowedType->id, 'title' => 'Allowed Document', 'original_filename' => 'allowed.pdf', 'status' => 'ready']);
    Document::create(['company_id' => $otherCompany->id, 'document_type_id' => $allowedType->id, 'title' => 'Hidden Document', 'original_filename' => 'hidden.pdf', 'status' => 'ready']);

    $response = $this->actingAs($user)->get(route('workspace.documents'));

    $response->assertOk()->assertSee('Allowed Document')->assertDontSee('Hidden Document');
    expect($response->viewData('stats')[0]['value'])->toBe(1)
        ->and($response->viewData('stats')[1]['value'])->toBe(2)
        ->and($response->viewData('documents'))->toHaveCount(1);
});
