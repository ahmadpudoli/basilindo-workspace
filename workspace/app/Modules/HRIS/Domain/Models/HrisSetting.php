<?php

namespace App\Modules\HRIS\Domain\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

class HrisSetting extends Model
{
    protected $connection = 'hris';
    protected $table = 'settings';
    protected $fillable = ['company_id', 'key', 'value', 'effective_from', 'is_active', 'archived_at', 'archived_by'];
    protected function casts(): array { return ['value' => 'array', 'effective_from' => 'date', 'is_active' => 'boolean', 'archived_at' => 'datetime']; }

    public static function resolve(?int $companyId, string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->where('is_active', true)->whereNull('archived_at')
            ->where(function ($query) use ($companyId): void { $query->where('company_id', $companyId)->orWhereNull('company_id'); })
            ->where(function ($query): void { $query->whereNull('effective_from')->orWhereDate('effective_from', '<=', now()->toDateString()); })
            ->orderByRaw('CASE WHEN company_id IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('effective_from')->first();
        return $setting?->value ?? $default;
    }

    public static function resolveForYear(?int $companyId, string $key, int $year, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->where('is_active', true)->whereNull('archived_at')
            ->where(function ($query) use ($companyId): void { $query->where('company_id', $companyId)->orWhereNull('company_id'); })
            ->where(function ($query) use ($year): void { $query->whereNull('effective_from')->orWhereYear('effective_from', $year); })
            ->orderByRaw('CASE WHEN company_id IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('effective_from')->first();

        return $setting?->value ?? $default;
    }

    public static function resolveLeaveSubmissionCutoff(?int $companyId, int $year): ?CarbonImmutable
    {
        $value = static::resolveForYear($companyId, 'hris.leave_submission_cutoff', $year);
        if (is_array($value)) $value = $value['date'] ?? $value['value'] ?? null;
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) || ! str_starts_with($value, (string) $year)) return null;

        try {
            return CarbonImmutable::createFromFormat('!Y-m-d', $value, config('app.timezone', 'Asia/Jakarta'))->endOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
