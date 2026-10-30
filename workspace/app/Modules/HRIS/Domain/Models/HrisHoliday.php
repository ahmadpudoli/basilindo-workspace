<?php

namespace App\Modules\HRIS\Domain\Models;

use Core\Models\Company;
use Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HrisHoliday extends Model
{
    use HasUlids, SoftDeletes;

    protected $connection = 'hris';
    protected $table = 'holidays';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'holiday_date', 'name', 'type', 'description', 'deduct_annual_leave',
        'is_global', 'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'holiday_date' => 'date',
            'deduct_annual_leave' => 'boolean',
            'is_global' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        $companyIds = $user->companies()->pluck('companies.id');

        $holidayIds = DB::connection('hris')->table('holiday_companies')
            ->whereIn('company_id', $companyIds)
            ->pluck('holiday_id');

        $qualifiedId = $query->qualifyColumn('id');

        return $query->where(function (Builder $visible) use ($holidayIds, $qualifiedId): void {
            $visible->where('is_global', true)
                ->orWhereIn($qualifiedId, $holidayIds);
        });
    }

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        $holidayIds = DB::connection('hris')->table('holiday_companies')
            ->where('company_id', $companyId)
            ->pluck('holiday_id');

        $qualifiedId = $query->qualifyColumn('id');

        return $query->where(function (Builder $visible) use ($holidayIds, $qualifiedId): void {
            $visible->where('is_global', true)
                ->orWhereIn($qualifiedId, $holidayIds);
        });
    }

    /**
     * Load company models from Core for a collection of HRIS holidays.
     * The pivot is owned by HRIS, while the company master is owned by Core.
     */
    public static function loadCompanies(Collection $holidays): void
    {
        $holidayIds = $holidays->modelKeys();
        if ($holidayIds === []) {
            return;
        }

        $pivotRows = DB::connection('hris')->table('holiday_companies')
            ->whereIn('holiday_id', $holidayIds)
            ->get(['holiday_id', 'company_id']);
        $companies = Company::query()
            ->whereIn('id', $pivotRows->pluck('company_id')->unique()->values())
            ->get()
            ->keyBy('id');

        $pivotRows->groupBy('holiday_id')->each(function (Collection $rows, string $holidayId) use ($holidays, $companies): void {
            $holidays->firstWhere($holidays->first()->getKeyName(), $holidayId)?->setRelation(
                'companies',
                $rows->map(fn (object $row) => $companies->get($row->company_id))->filter()->values(),
            );
        });

        $holidays->each(function (self $holiday) use ($pivotRows, $companies): void {
            if ($holiday->relationLoaded('companies')) {
                return;
            }
            $holiday->setRelation(
                'companies',
                $pivotRows->where('holiday_id', $holiday->getKey())
                    ->map(fn (object $row) => $companies->get($row->company_id))->filter()->values(),
            );
        });
    }

    public function companyIds(): Collection
    {
        return DB::connection('hris')->table('holiday_companies')
            ->where('holiday_id', $this->getKey())
            ->pluck('company_id');
    }

    public static function activeDatesForCompany(int $companyId, int $year): array
    {
        return static::query()->where('is_active', true)->forCompany($companyId)->whereYear('holiday_date', $year)
            ->pluck('holiday_date')->map(fn ($date): string => $date->toDateString())->all();
    }

    public static function collectiveDaysForCompany(int $companyId, int $year): int
    {
        return static::query()->where('is_active', true)->where('type', 'collective_leave')->where('deduct_annual_leave', true)
            ->forCompany($companyId)->whereYear('holiday_date', $year)->distinct('holiday_date')->count('holiday_date');
    }
}
