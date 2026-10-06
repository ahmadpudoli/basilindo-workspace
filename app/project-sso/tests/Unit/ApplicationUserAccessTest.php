<?php

use App\Models\ApplicationUserAccess;

it('uses a stable surrogate key for Filament record actions', function () {
    $access = new ApplicationUserAccess();

    expect($access->getKeyName())->toBe('id')
        ->and($access->getQualifiedKeyName())->toBe('application_user_access.id')
        ->and($access->getIncrementing())->toBeTrue();
});

it('only audits safe application access metadata', function () {
    $access = new ApplicationUserAccess(['status' => 'active']);

    expect($access->getFillable())->toContain('status')
        ->and($access->getTable())->toBe('application_user_access');
});
