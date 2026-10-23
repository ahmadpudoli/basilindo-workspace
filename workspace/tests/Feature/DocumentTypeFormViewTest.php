<?php

use Core\Models\Role;
use Core\Models\Company;
use Core\Models\User;
use Core\Models\Permission;
use App\Modules\DocumentManagement\Domain\Models\Document;
use App\Modules\DocumentManagement\Domain\Models\DocumentType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the create document type form without an edit model', function () {
    $user = User::query()->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->first();
    if (! $user) {
        $user = User::factory()->create(['email' => 'admin@example.com', 'is_system_account' => true]);
        $user->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));
    }

    $this->actingAs($user)
        ->get(route('workspace.documents.types.create'))
        ->assertOk()
        ->assertSee('Tambah tipe dokumen')
        ->assertSee('workspace-compact-dropdown')
        ->assertSee('data-client-validation="document-type"', false)
        ->assertSee('novalidate', false);
});

it('authorizes document type CRUD through dedicated permissions', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'CRUD Type Company', 'code' => 'CRT-'.fake()->unique()->bothify('####')]);
    $user->companies()->attach($company);
    $type = DocumentType::create(['company_id' => $company->id, 'name' => 'CRUD Type', 'code' => 'crud-type']);

    $this->actingAs($user)
        ->get(route('workspace.documents.types.create'))
        ->assertForbidden();

    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'create_document_type', 'guard_name' => 'web']));
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'view_any_document_type', 'guard_name' => 'web']));
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'view_document_type', 'guard_name' => 'web']));
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'update_document_type', 'guard_name' => 'web']));
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'delete_document_type', 'guard_name' => 'web']));

    $this->actingAs($user)
        ->get(route('workspace.documents.types.create'))
        ->assertOk();

    expect($user->can('update_document_type'))->toBeTrue()
        ->and($user->can('delete_document_type'))->toBeTrue();
});

it('stores a document type for multiple companies and keeps empty selection global', function () {
    $user = User::query()->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->first() ?? User::factory()->create(['email' => 'admin@example.com', 'is_system_account' => true]);
    if (! $user->hasRole('super_admin')) {
        $user->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));
    }
    $firstCompany = Company::create(['name' => 'First Company', 'code' => 'FST-'.fake()->unique()->bothify('####')]);
    $secondCompany = Company::create(['name' => 'Second Company', 'code' => 'SND-'.fake()->unique()->bothify('####')]);

    $this->actingAs($user)
        ->post(route('workspace.documents.types.store'), [
            'name' => 'Invoice Multi Company',
            'code' => 'invoice-multi',
            'company_ids' => [$firstCompany->id, $secondCompany->id],
            'required_fields' => '[]',
            'is_active' => '1',
        ])
        ->assertRedirect(route('workspace.documents.types.show', ['type' => DocumentType::query()->where('code', 'invoice-multi')->value('id') ?? 0]));

    $type = DocumentType::query()->where('code', 'invoice-multi')->firstOrFail();
    expect(
        Illuminate\Support\Facades\DB::connection('documents')->table('document_type_companies')->where('document_type_id', $type->id)->pluck('company_id')->sort()->values()->all()
    )->toBe([$firstCompany->id, $secondCompany->id]);

    $this->actingAs($user)
        ->post(route('workspace.documents.types.store'), [
            'name' => 'Global Contract',
            'code' => 'global-contract',
            'required_fields' => '[]',
            'is_active' => '1',
        ])
        ->assertRedirect(route('workspace.documents.types.show', ['type' => DocumentType::query()->where('code', 'global-contract')->value('id') ?? 0]));

    $global = DocumentType::query()->where('code', 'global-contract')->firstOrFail();
    expect(Illuminate\Support\Facades\DB::connection('documents')->table('document_type_companies')->where('document_type_id', $global->id)->count())->toBe(0);
});

it('lists document types with server-side search, sorting, and page size', function () {
    $user = User::query()->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->first() ?? User::factory()->create(['email' => 'admin@example.com', 'is_system_account' => true]);
    if (! $user->hasRole('super_admin')) {
        $user->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));
    }
    DocumentType::create(['name' => 'Invoice', 'code' => 'invoice', 'required_fields' => [], 'is_active' => true]);
    DocumentType::create(['name' => 'Contract', 'code' => 'contract', 'required_fields' => [], 'is_active' => false]);

    $response = $this->actingAs($user)->get(route('workspace.documents.types', [
        'q' => 'invoice',
        'sort' => 'name',
        'direction' => 'asc',
        'status' => 'active',
        'limit' => 10,
    ]));

    $response->assertOk()
        ->assertSee('Invoice')
        ->assertDontSee('Contract');
    expect($response->viewData('types')->perPage())->toBe(10)
        ->and($response->viewData('sort'))->toBe('name')
        ->and($response->viewData('direction'))->toBe('asc')
        ->and($response->viewData('status'))->toBe('active');
});

it('only lists document types within the user company scope', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'access_documents', 'guard_name' => 'web']));
    $allowedCompany = Company::create(['name' => 'CARANO', 'code' => 'CAR-'.fake()->unique()->bothify('####')]);
    $otherCompany = Company::create(['name' => 'Other Company', 'code' => 'OTH-'.fake()->unique()->bothify('####')]);
    $user->companies()->attach($allowedCompany);

    DocumentType::create(['company_id' => $allowedCompany->id, 'name' => 'CARANO Invoice', 'code' => 'carano-invoice']);
    DocumentType::create(['company_id' => $otherCompany->id, 'name' => 'Other Invoice', 'code' => 'other-invoice']);

    $this->actingAs($user)
        ->get(route('workspace.documents.types'))
        ->assertOk()
        ->assertSee('CARANO Invoice')
        ->assertDontSee('Other Invoice');
});

it('rejects deleting a document type already used by a document', function () {
    $user = User::query()->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->first() ?? User::factory()->create(['email' => 'admin@example.com', 'is_system_account' => true]);
    if (! $user->hasRole('super_admin')) {
        $user->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));
    }
    $company = Company::create(['name' => 'Document Company', 'code' => 'DOC-'.fake()->unique()->bothify('####')]);
    $type = DocumentType::create(['name' => 'Used Type', 'code' => 'used-type', 'required_fields' => [], 'is_active' => true]);
    Document::create(['company_id' => $company->id, 'document_type_id' => $type->id, 'title' => 'Used document', 'original_filename' => 'used.pdf']);

    $response = $this->actingAs($user)->delete(route('workspace.documents.types.destroy', $type));

    $response->assertRedirect()->assertSessionHas('error', 'Tipe dokumen tidak dapat dihapus karena sudah digunakan pada upload dokumen.');
    expect(DocumentType::query()->whereKey($type->id)->exists())->toBeTrue();
});


it('shows the document type audit panel only with the dedicated permission', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Type Audit Company', 'code' => 'TAD-'.fake()->unique()->bothify('####')]);
    $user->companies()->attach($company);
    $type = DocumentType::create(['name' => 'Audited Type', 'code' => 'audited-type', 'required_fields' => [], 'is_active' => true]);
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'view_document_type', 'guard_name' => 'web']));

    $this->actingAs($user)
        ->get(route('workspace.documents.types.show', $type))
        ->assertOk()
        ->assertDontSee('data-subject-audit-panel', false);

    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'view_document_audit_trail', 'guard_name' => 'web']));

    $this->actingAs($user)
        ->get(route('workspace.documents.types.show', $type))
        ->assertOk()
        ->assertSee('data-subject-audit-panel', false)
        ->assertSee('Audit trail');
});
