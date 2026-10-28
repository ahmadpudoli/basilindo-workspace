<?php

namespace App\Modules\HRIS\Domain\Models;

use Core\Models\Company;
use Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrisWorkflowApprover extends Model
{
    use SoftDeletes;
    protected $connection = 'hris';
    protected $table = 'workflow_approvers';

    protected $fillable = ['company_id', 'workflow_key', 'step', 'user_id', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function scopeForWorkflow(Builder $query, int $companyId, string $workflowKey, string $step): Builder
    {
        return $query->where('company_id', $companyId)->where('workflow_key', $workflowKey)->where('step', $step)->where('is_active', true);
    }

    public static function approverIds(int $companyId, string $workflowKey, string $step): array
    {
        return static::query()->forWorkflow($companyId, $workflowKey, $step)->pluck('user_id')->map(fn ($id): int => (int) $id)->all();
    }
}
