<?php

use Core\Models\Company;
use App\Models\Document;
use App\Models\DocumentType;
use Core\Models\User;
use App\Services\Verification\DocumentMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('creates an approved verification case when reference and amount match', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $type = DocumentType::create(['name' => 'Finance', 'code' => 'finance']);
    $attributes = fn () => [
        'company_id' => $company->id,
        'document_type_id' => $type->id,
        'title' => 'Finance document',
        'original_filename' => 'document.pdf',
        'normalized_reference_number' => 'REF001',
        'amount' => 1500000,
        'currency' => 'IDR',
    ];

    $case = app(DocumentMatchingService::class)->createCase([
        Document::create($attributes()),
        Document::create($attributes()),
    ], $user->id);

    expect($case->status)->toBe('approved')
        ->and($case->items)->toHaveCount(4)
        ->and($case->items->every(fn ($item) => $item->result === 'passed'))->toBeTrue();
});

it('keeps a verification case open when finance values differ', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $type = DocumentType::create(['name' => 'Finance', 'code' => 'finance']);

    $first = Document::create(['company_id' => $company->id, 'document_type_id' => $type->id, 'title' => 'One', 'original_filename' => 'one.pdf', 'normalized_reference_number' => 'REF001', 'amount' => 100, 'currency' => 'IDR']);
    $second = Document::create(['company_id' => $company->id, 'document_type_id' => $type->id, 'title' => 'Two', 'original_filename' => 'two.pdf', 'normalized_reference_number' => 'REF002', 'amount' => 200, 'currency' => 'IDR']);

    $case = app(DocumentMatchingService::class)->createCase([$first, $second], $user->id);

    expect($case->status)->toBe('open')
        ->and($case->items->where('result', 'failed'))->toHaveCount(4);
});
