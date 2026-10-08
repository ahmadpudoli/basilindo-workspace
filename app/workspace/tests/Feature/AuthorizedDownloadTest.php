<?php

use Core\Models\Company;
use App\Models\Document;
use App\Models\DocumentType;
use Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('allows a company member to download a private document', function () {
    Storage::fake('s3');
    $member = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $member->companies()->attach($company);
    $type = DocumentType::create(['name' => 'Invoice', 'code' => 'invoice']);
    $document = Document::create(['company_id' => $company->id, 'document_type_id' => $type->id, 'title' => 'Invoice', 'original_filename' => 'invoice.pdf']);
    $key = 'documents/'.$document->id.'/invoice.pdf';
    Storage::disk('s3')->put($key, 'invoice-content');
    $document->versions()->create(['version' => 1, 'object_key' => $key, 'mime_type' => 'application/pdf', 'file_size' => 15, 'checksum' => hash('sha256', 'invoice-content')]);

    $response = $this->actingAs($member)->get(route('documents.download', $document));

    $response->assertOk()->assertHeader('content-type', 'application/pdf');
});

it('rejects a user from another company', function () {
    Storage::fake('s3');
    $member = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $otherCompany = Company::create(['name' => 'Other', 'code' => 'OTH-'.Str::random(8)]);
    $member->companies()->attach($otherCompany);
    $type = DocumentType::create(['name' => 'Invoice', 'code' => 'invoice']);
    $document = Document::create(['company_id' => $company->id, 'document_type_id' => $type->id, 'title' => 'Invoice', 'original_filename' => 'invoice.pdf']);

    $this->actingAs($member)->get(route('documents.download', $document))->assertForbidden();
});

it('previews safe PDF content inline through the same company policy', function () {
    Storage::fake('s3');
    $member = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $member->companies()->attach($company);
    $type = DocumentType::create(['name' => 'Invoice', 'code' => 'invoice']);
    $document = Document::create(['company_id' => $company->id, 'document_type_id' => $type->id, 'title' => 'Invoice', 'original_filename' => 'invoice.pdf']);
    $key = 'documents/'.$document->id.'/invoice.pdf';
    Storage::disk('s3')->put($key, 'invoice-content');
    $document->versions()->create(['version' => 1, 'object_key' => $key, 'mime_type' => 'application/pdf', 'file_size' => 15, 'checksum' => hash('sha256', 'invoice-content')]);

    $response = $this->actingAs($member)->get(route('documents.preview', $document));

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertHeader('content-disposition', 'inline; filename="invoice.pdf"');
});
