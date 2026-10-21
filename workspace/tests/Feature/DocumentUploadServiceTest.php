<?php

use App\Modules\DocumentManagement\Domain\Models\DocumentType;
use App\Modules\DocumentManagement\Application\Services\Documents\DocumentUploadService;
use Core\Models\Company;
use Core\Models\Project;
use Core\Models\User;
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
        ->and($document->currentVersion->object_key)->toMatch('/^app-document-management\/[0-9A-HJKMNP-TV-Z]{26}\//')
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

it('accepts optional typed metadata and validates datetime metadata', function () {
    Storage::fake('s3');
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $type = DocumentType::create([
        'name' => 'Invoice',
        'code' => 'invoice',
        'required_fields' => [
            'invoice_number' => ['type' => 'string', 'mandatory' => true],
            'invoice_date' => ['type' => 'date', 'mandatory' => false],
            'approved_at' => ['type' => 'datetime', 'mandatory' => false],
            'total' => ['type' => 'numeric', 'mandatory' => true],
        ],
    ]);

    $document = app(DocumentUploadService::class)->upload(
        UploadedFile::fake()->createWithContent('invoice.pdf', '%PDF-test-content%'),
        [
            'company_id' => $company->id,
            'document_type_id' => $type->id,
            'title' => 'Invoice Test',
            'metadata' => [
                'invoice_number' => 'INV-001',
                'total' => '125000',
                'approved_at' => '2026-10-09 10:30:00',
            ],
        ],
        $user->id,
    );

    expect($document->metadata['approved_at'])->toBe('2026-10-09 10:30:00');
});

it('normalizes numeric decimal metadata and rejects non-numeric text', function () {
    Storage::fake('s3');
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $type = DocumentType::create([
        'name' => 'Invoice',
        'code' => 'invoice',
        'required_fields' => ['total' => ['type' => 'numeric_decimal', 'mandatory' => true]],
    ]);

    $document = app(DocumentUploadService::class)->upload(
        UploadedFile::fake()->createWithContent('invoice.pdf', '%PDF-test-content%'),
        [
            'company_id' => $company->id,
            'document_type_id' => $type->id,
            'title' => 'Invoice Decimal',
            'metadata' => ['total' => '125000,75'],
        ],
        $user->id,
    );

    expect($document->metadata['total'])->toBe('125000.75');

    expect(fn () => app(DocumentUploadService::class)->upload(
        UploadedFile::fake()->createWithContent('invalid.pdf', '%PDF-test-content-invalid%'),
        [
            'company_id' => $company->id,
            'document_type_id' => $type->id,
            'title' => 'Invoice Invalid',
            'metadata' => ['total' => '125000abc'],
        ],
        $user->id,
    ))->toThrow(\Illuminate\Validation\ValidationException::class);
});

it('normalizes Indonesian formatted integer and decimal metadata', function () {
    Storage::fake('s3');
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $type = DocumentType::create([
        'name' => 'Invoice',
        'code' => 'invoice',
        'required_fields' => [
            'quantity' => ['type' => 'numeric', 'mandatory' => true],
            'total' => ['type' => 'numeric_decimal', 'mandatory' => true],
        ],
    ]);

    $document = app(DocumentUploadService::class)->upload(
        UploadedFile::fake()->createWithContent('invoice.pdf', '%PDF-test-content-indonesian-format%'),
        [
            'company_id' => $company->id,
            'document_type_id' => $type->id,
            'title' => 'Invoice Indonesia',
            'amount' => '9.000.000,50',
            'metadata' => ['quantity' => '1.234.567', 'total' => '1.234.567,89'],
        ],
        $user->id,
    );

    expect($document->metadata['quantity'])->toBe('1234567')
        ->and($document->metadata['total'])->toBe('1234567.89')
        ->and((string) $document->amount)->toBe('9000000.5000');
});


it('accepts only integer values for numeric metadata', function () {
    Storage::fake('s3');
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $type = DocumentType::create([
        'name' => 'Invoice',
        'code' => 'invoice',
        'required_fields' => ['quantity' => ['type' => 'numeric', 'mandatory' => true]],
    ]);

    expect(fn () => app(DocumentUploadService::class)->upload(
        UploadedFile::fake()->createWithContent('decimal.pdf', '%PDF-test-content-decimal%'),
        [
            'company_id' => $company->id,
            'document_type_id' => $type->id,
            'title' => 'Invoice Integer',
            'metadata' => ['quantity' => '10.5'],
        ],
        $user->id,
    ))->toThrow(\Illuminate\Validation\ValidationException::class);
});

it('rejects a project that belongs to another internal company', function () {
    Storage::fake('s3');
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $otherCompany = Company::create(['name' => 'Other Company', 'code' => 'OTH-'.Str::random(8)]);
    $project = Project::create(['company_id' => $otherCompany->id, 'name' => 'Other Project']);
    $type = DocumentType::create(['name' => 'Invoice', 'code' => 'invoice']);

    expect(fn () => app(DocumentUploadService::class)->upload(
        UploadedFile::fake()->createWithContent('invoice.pdf', '%PDF-test-content%'),
        [
            'company_id' => $company->id,
            'project_id' => $project->id,
            'document_type_id' => $type->id,
            'title' => 'Invoice Test',
        ],
        $user->id,
    ))->toThrow(\Illuminate\Validation\ValidationException::class);
});

it('rejects a company-specific document type from another internal company', function () {
    Storage::fake('s3');
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $otherCompany = Company::create(['name' => 'Other Company', 'code' => 'OTH-'.Str::random(8)]);
    $type = DocumentType::create([
        'company_id' => $otherCompany->id,
        'name' => 'Other Invoice',
        'code' => 'other-invoice',
    ]);

    expect(fn () => app(DocumentUploadService::class)->upload(
        UploadedFile::fake()->createWithContent('invoice.pdf', '%PDF-test-content%'),
        [
            'company_id' => $company->id,
            'document_type_id' => $type->id,
            'title' => 'Invoice Test',
        ],
        $user->id,
    ))->toThrow(\Illuminate\Validation\ValidationException::class);
});
