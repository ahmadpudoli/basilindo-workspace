<?php

namespace App\Modules\HRIS\Application\Services;

use Carbon\CarbonInterface;

class DateRangeRule
{
    public static function overlaps(CarbonInterface $start, CarbonInterface $end, CarbonInterface $otherStart, CarbonInterface $otherEnd): bool
    {
        return $start->startOfDay()->lessThanOrEqualTo($otherEnd->startOfDay()) && $end->startOfDay()->greaterThanOrEqualTo($otherStart->startOfDay());
    }
}
