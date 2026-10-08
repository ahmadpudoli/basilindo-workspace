<?php

namespace App\Models;

use Core\Models\Company;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CrmActivity extends Model
{
    use HasUuids;

    protected $connection = 'crm';
    protected $table = 'activities';

    protected $fillable = ['company_id', 'account_id', 'opportunity_id', 'assigned_to', 'type', 'subject', 'description', 'due_at', 'completed_at'];

    protected function casts(): array
    {
        return ['due_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function account()
    {
        return $this->belongsTo(CrmAccount::class);
    }

    public function opportunity()
    {
        return $this->belongsTo(CrmOpportunity::class);
    }
}
