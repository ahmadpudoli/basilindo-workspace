<?php

namespace App\Models;

use Core\Models\Company;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmAccount extends Model
{
    use HasUuids, SoftDeletes;

    protected $connection = 'crm';
    protected $table = 'accounts';

    protected $fillable = [
        'company_id', 'name', 'status', 'industry', 'segment', 'owner_user_id',
        'estimated_value', 'currency', 'metadata',
    ];

    protected function casts(): array
    {
        return ['estimated_value' => 'decimal:4', 'metadata' => 'array'];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contacts()
    {
        return $this->hasMany(CrmContact::class, 'account_id');
    }

    public function leads()
    {
        return $this->hasMany(CrmLead::class, 'account_id');
    }

    public function opportunities()
    {
        return $this->hasMany(CrmOpportunity::class, 'account_id');
    }

    public function activities()
    {
        return $this->hasMany(CrmActivity::class, 'account_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'crm_account_id');
    }
}
