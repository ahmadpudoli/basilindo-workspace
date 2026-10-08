<?php

namespace App\Models;

use Core\Models\Project;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmOpportunity extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'crm_opportunities';

    protected $fillable = ['account_id', 'name', 'stage', 'amount', 'currency', 'expected_close_date', 'owner_user_id', 'project_id', 'notes'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:4', 'expected_close_date' => 'date'];
    }

    public function account()
    {
        return $this->belongsTo(CrmAccount::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
