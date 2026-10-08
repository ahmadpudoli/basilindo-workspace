<?php

use App\Models\Document;
use App\Models\DocumentType;
use App\Services\Documents\DocumentLifecycleService;
use Core\Models\Company;
use Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

function lifecycleDocument(): array
{
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Lifecycle Co', 'code' => 'LIF-'.Str::random(8)]);
    $type = DocumentType::create(['name' => 'Invoice', 'code' => 'invoice']);
    $document = Document::create([
        'company_id' => $company->id,
        'document_type_id' => $type->id,
        'title' => 'Invoice',
        'original_filename' => 'invoice.pdf',
        'status' => 'quarantine',
        'quarantined_at' => now(),
    ]);

    return [$document, $user];
}

it('releases a quarantined document and records its lifecycle state', function () {
    [$document, $user] = lifecycleDocument();

    $updated = app(DocumentLifecycleService::class)->release($document, $user->id);

    expect($updated->status)->toBe('ready')
        ->and($updated->released_at)->not->toBeNull()
        ->and($updated->rejection_reason)->toBeNull();
});

it('requires a reason and quarantine state before rejecting a document', function () {
    [$document, $user] = lifecycleDocument();

    expect(fn () => app(DocumentLifecycleService::class)->reject($document, $user->id, ''))
        ->toThrow(ValidationException::class);

    $updated = app(DocumentLifecycleService::class)->reject($document, $user->id, 'File tidak terbaca');

    expect($updated->status)->toBe('rejected')
        ->and($updated->rejection_reason)->toBe('File tidak terbaca')
        ->and($updated->rejected_at)->not->toBeNull();
});

it('supports legal hold changes through the lifecycle service', function () {
    [$document, $user] = lifecycleDocument();

    $updated = app(DocumentLifecycleService::class)->setLegalHold($document, $user->id, true);

    expect($updated->legal_hold)->toBeTrue();
});
