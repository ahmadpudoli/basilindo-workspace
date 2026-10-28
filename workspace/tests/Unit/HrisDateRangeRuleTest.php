<?php

use App\Modules\HRIS\Application\Services\DateRangeRule;
use Carbon\CarbonImmutable;

it('treats a shared boundary date as an overlap', function () {
    expect(DateRangeRule::overlaps(CarbonImmutable::parse('2026-01-10'), CarbonImmutable::parse('2026-01-12'), CarbonImmutable::parse('2026-01-12'), CarbonImmutable::parse('2026-01-15')))->toBeTrue();
});

it('allows adjacent non-overlapping ranges', function () {
    expect(DateRangeRule::overlaps(CarbonImmutable::parse('2026-01-10'), CarbonImmutable::parse('2026-01-12'), CarbonImmutable::parse('2026-01-13'), CarbonImmutable::parse('2026-01-15')))->toBeFalse();
});
