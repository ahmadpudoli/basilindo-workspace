<?php

use Core\Models\Company;
use App\Models\Document;
use App\Models\DocumentType;
use Core\Models\User;
use App\Services\Verification\DocumentMatchingService;
use App\Services\Verification\VerificationReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('updates review decisions and closes a case after all items are reviewed', function () {
    $reviewer = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS-'.Str::random(8)]);
    $reviewer->companies()->attach($company);
    $type = DocumentType::create(['name' => 'Finance', 'code' => 'finance']);
    $document = fn (string $reference) => Document::create(['company_id' => $company->id, 'document_type_id' => $type->id, 'title' => 'Finance', 'original_filename' => 'finance.pdf', 'normalized_reference_number' => $reference, 'amount' => 100]);
    $case = app(DocumentMatchingService::class)->createCase([$document('REF001'), $document('REF002')], $reviewer->id);

    foreach ($case->items as $item) {
        app(VerificationReviewService::class)->review($item, $reviewer, 'passed', 'Reviewed by finance');
    }

    expect($case->fresh()->status)->toBe('approved')
        ->and($case->fresh()->items->every(fn ($item) => $item->reviewer_comment === 'Reviewed by finance'))->toBeTrue();
});
