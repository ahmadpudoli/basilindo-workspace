<?php

use App\Models\Company;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Project;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores a document with its searchable ownership metadata and version', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS']);
    $project = Project::create([
        'company_id' => $company->id,
        'name' => 'Project X',
        'description' => 'Finance test project',
    ]);
    $vendor = Vendor::create(['company_id' => $company->id, 'name' => 'Vendor ABC']);
    $type = DocumentType::create(['name' => 'Invoice', 'code' => 'invoice']);

    $document = Document::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'vendor_id' => $vendor->id,
        'document_type_id' => $type->id,
        'uploaded_by' => $user->id,
        'title' => 'Invoice Project X',
        'original_filename' => 'invoice.pdf',
        'reference_number' => 'INV-001',
        'normalized_reference_number' => 'INV001',
        'document_date' => '2026-09-30',
        'amount' => 1000000,
        'currency' => 'IDR',
    ]);

    $version = $document->versions()->create([
        'version' => 1,
        'object_key' => 'companies/1/documents/'.$document->id.'/v1.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'checksum' => hash('sha256', 'invoice'),
        'uploaded_by' => $user->id,
    ]);

    expect($document->id)->toBeString()
        ->and($document->company->is($company))->toBeTrue()
        ->and($document->project->is($project))->toBeTrue()
        ->and($document->vendor->is($vendor))->toBeTrue()
        ->and($document->documentType->is($type))->toBeTrue()
        ->and($document->currentVersion->is($version))->toBeTrue();
});
