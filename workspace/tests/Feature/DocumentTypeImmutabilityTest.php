<?php

use App\Modules\DocumentManagement\Domain\Models\Document;
use App\Modules\DocumentManagement\Domain\Models\DocumentType;
use Core\Models\Company;
use Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('normalizes document type metadata definitions after a document is used', function () {
    $user = User::factory()->create();
    $company = Company::create([
        'name' => 'LTN',
        'code' => 'LTN-'.Str::random(8),
        'classification' => 'internal',
        'status' => 'active',
    ]);
    $type = DocumentType::create([
        'company_id' => $company->id,
        'name' => 'Invoice',
        'code' => 'INVOICE',
        'required_fields' => ['no_invoice' => ['type' => 'string', 'mandatory' => true]],
    ]);

    Document::create([
        'company_id' => $company->id,
        'document_type_id' => $type->id,
        'uploaded_by' => $user->id,
        'title' => 'Invoice LTN',
        'original_filename' => 'invoice.pdf',
    ]);

    expect($type->metadataDefinitions())->toBe([
        'no_invoice' => ['type' => 'string', 'mandatory' => true],
    ]);
});
