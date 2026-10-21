<?php

use App\Modules\DocumentManagement\Domain\Models\Document;
use App\Modules\DocumentManagement\Domain\Models\DocumentType;
use Core\Models\Company;
use Core\Models\User;
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
        ->assertDontSee('crm_account_id');
});
