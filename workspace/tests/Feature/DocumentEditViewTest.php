<?php

use App\Modules\DocumentManagement\Domain\Models\Document;
use App\Modules\DocumentManagement\Domain\Models\DocumentType;
use Core\Models\Company;
use Core\Models\User;
use Core\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('renders the document edit form with normalized Livewire document types', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Edit Company', 'code' => 'EDT-'.Str::random(6)]);
    $user->companies()->attach($company);
    $type = DocumentType::create(['name' => 'Invoice', 'code' => 'invoice', 'required_fields' => ['invoice_number' => ['type' => 'string', 'mandatory' => true]], 'is_active' => true]);
    $document = Document::create(['company_id' => $company->id, 'document_type_id' => $type->id, 'uploaded_by' => $user->id, 'title' => 'Invoice edit', 'original_filename' => 'invoice.pdf', 'document_date' => now()->toDateString(), 'status' => 'quarantine']);

    $this->actingAs($user)
        ->get(route('workspace.documents.edit', $document))
        ->assertOk()
        ->assertSee('Edit dokumen')
        ->assertSee('Metadata tambahan')
        ->assertSee('Invoice Number')
        ->assertSee('data-document-company-select', false)
        ->assertSee('data-tag-input', false)
        ->assertSee('Perusahaan client/vendor')
        ->assertDontSee('crm_account_id')
        ->assertDontSee('data-subject-audit-panel', false);
});

it('shows the document audit panel only with the dedicated permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'view_document_audit_trail', 'guard_name' => 'web']));
    $company = Company::create(['name' => 'Audit Company', 'code' => 'AUD-'.Str::random(6)]);
    $user->companies()->attach($company);
    $type = DocumentType::create(['name' => 'Audit Invoice', 'code' => 'audit-invoice', 'required_fields' => [], 'is_active' => true]);
    $document = Document::create(['company_id' => $company->id, 'document_type_id' => $type->id, 'uploaded_by' => $user->id, 'title' => 'Audit edit', 'original_filename' => 'audit.pdf', 'status' => 'quarantine']);

    $this->actingAs($user)
        ->get(route('workspace.documents.edit', $document))
        ->assertOk()
        ->assertSee('data-subject-audit-panel', false)
        ->assertSee('Audit trail');
});
