<?php

use App\Models\Bundle;
use Core\Models\Company;
use App\Models\Document;
use App\Models\DocumentType;
use Core\Models\User;
use App\Services\Bundles\DocumentBundleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('creates a ZIP bundle with a manifest in private storage', function () {
    Storage::fake('s3');
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $type = DocumentType::create(['name' => 'Finance', 'code' => 'finance']);
    $documents = collect(['one.pdf', 'two.pdf'])->map(function (string $filename) use ($company, $type) {
        $document = Document::create(['company_id' => $company->id, 'document_type_id' => $type->id, 'title' => $filename, 'original_filename' => $filename]);
        $objectKey = 'documents/'.$document->id.'/'.$filename;
        Storage::disk('s3')->put($objectKey, '%PDF-content%');
        $document->versions()->create(['version' => 1, 'object_key' => $objectKey, 'mime_type' => 'application/pdf', 'file_size' => 13, 'checksum' => hash('sha256', '%PDF-content%')]);
        return $document->fresh('currentVersion');
    });

    $service = app(DocumentBundleService::class);
    $bundle = $service->create($documents->all(), $user->id);
    $bundle = $service->generate($bundle);

    expect($bundle->status)->toBe('completed')
        ->and($bundle->object_key)->not->toBeNull()
        ->and(Storage::disk('s3')->exists($bundle->object_key))->toBeTrue();
});
