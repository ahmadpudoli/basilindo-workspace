<?php

use App\Filament\Resources\DocumentTypes\Pages\EditDocumentType;
use App\Models\Document;
use App\Models\DocumentType;
use Core\Models\Company;
use Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('rejects company and metadata changes after a document type is used', function () {
    $user = User::factory()->create();
    $company = Company::create([
        'name' => 'LTN',
        'code' => 'LTN-'.Str::random(8),
        'classification' => 'internal',
        'status' => 'active',
    ]);
    $otherCompany = Company::create([
        'name' => 'Legospay',
        'code' => 'LEG-'.Str::random(8),
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

    $page = new EditDocumentType;
    $recordProperty = (new ReflectionClass($page))->getProperty('record');
    $recordProperty->setAccessible(true);
    $recordProperty->setValue($page, $type);
    $method = (new ReflectionClass($page))->getMethod('mutateFormDataBeforeSave');
    $method->setAccessible(true);

    expect(fn () => $method->invoke($page, [
        'company_id' => $otherCompany->id,
        'metadata_rows' => [
            ['field_name' => 'nomor_baru', 'type' => 'string', 'mandatory' => '1'],
        ],
    ]))->toThrow(ValidationException::class);

    expect($type->fresh()->company_id)->toBe($company->id)
        ->and($type->fresh()->metadataDefinitions())->toHaveKey('no_invoice');
});
