<?php

use App\Models\Company;
use App\Models\DocumentType;
use App\Models\User;
use App\Services\Documents\DocumentUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('uploads a private document object and persists its version metadata', function () {
    Storage::fake('s3');
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS']);
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
        ->and(Storage::disk('s3')->exists($document->currentVersion->object_key))->toBeTrue();
});
