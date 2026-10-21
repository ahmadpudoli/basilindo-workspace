<?php

use Core\Models\Role;
use Core\Models\Company;
use Core\Models\User;
use App\Modules\DocumentManagement\Domain\Models\DocumentType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the create document type form without an edit model', function () {
    $user = User::factory()->create(['email' => 'admin@example.com', 'is_system_account' => true]);
    $user->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));

    $this->actingAs($user)
        ->get(route('workspace.documents.types.create'))
        ->assertOk()
        ->assertSee('Tambah tipe dokumen')
        ->assertSee('workspace-compact-dropdown');
});

it('stores a document type for multiple companies and keeps empty selection global', function () {
    $user = User::query()->where('email', 'admin@example.com')->first() ?? User::factory()->create(['email' => 'admin@example.com', 'is_system_account' => true]);
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
        ->assertRedirect(route('workspace.documents.types'));

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
        ->assertRedirect(route('workspace.documents.types'));

    $global = DocumentType::query()->where('code', 'global-contract')->firstOrFail();
    expect(Illuminate\Support\Facades\DB::connection('documents')->table('document_type_companies')->where('document_type_id', $global->id)->count())->toBe(0);
});
