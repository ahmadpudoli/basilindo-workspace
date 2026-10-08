<?php

use Core\Models\Company;
use App\Models\DocumentType;
use Core\Models\User;
use App\Services\Documents\DocumentUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('uploads a private document object and persists its version metadata', function () {
    Storage::fake('s3');
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $type = DocumentType::create(['name' => 'Invoice', 'code' => 'invoice']);

    $document = app(DocumentUploadService::class)->upload(
        UploadedFile::fake()->createWithContent('invoice.pdf', '%PDF-test-content%'),
        [
            'company_id' => $company->id,
            'document_type_id' => $type->id,
            'title' => 'Invoice Test',
            'reference_number' => 'INV-001',
            'normalized_reference_number' => 'INV001',
        ],
        $user->id,
    );

    expect($document->currentVersion)->not->toBeNull()
        ->and($document->currentVersion->disk)->toBe('s3')
        ->and($document->status)->toBe('quarantine')
        ->and($document->normalized_reference_number)->toBe('INV001')
        ->and(Storage::disk('s3')->exists($document->currentVersion->object_key))->toBeTrue();
});

it('rejects an upload when document type metadata is incomplete', function () {
    Storage::fake('s3');
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $type = DocumentType::create([
        'name' => 'Invoice',
        'code' => 'invoice',
        'required_fields' => ['invoice_number' => 'string', 'total' => 'numeric'],
    ]);

    expect(fn () => app(DocumentUploadService::class)->upload(
        UploadedFile::fake()->createWithContent('invoice.pdf', '%PDF-test-content%'),
        [
            'company_id' => $company->id,
            'document_type_id' => $type->id,
            'title' => 'Invoice Test',
            'metadata' => ['invoice_number' => 'INV-001'],
        ],
        $user->id,
    ))->toThrow(\Illuminate\Validation\ValidationException::class);
});
