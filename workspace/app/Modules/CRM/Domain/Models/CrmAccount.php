<?php

namespace App\Modules\CRM\Domain\Models;

use Core\Models\Company;
use App\Modules\DocumentManagement\Domain\Models\Document;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmAccount extends Model
{
    use HasUuids, SoftDeletes;

    protected $connection = 'crm';
    protected $table = 'accounts';

    protected $fillable = [
        'company_id', 'party_company_id', 'account_type', 'name', 'status', 'industry', 'segment', 'owner_user_id',
        'street', 'street2', 'city', 'state', 'zip', 'country',
        'source_system', 'source_database', 'external_id', 'is_integrated', 'last_synced_at',
        'estimated_value', 'currency', 'metadata',
    ];

    protected function casts(): array
    {
        return ['estimated_value' => 'decimal:4', 'metadata' => 'array', 'is_integrated' => 'boolean', 'last_synced_at' => 'datetime'];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function partyCompany()
    {
        return $this->belongsTo(Company::class, 'party_company_id');
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
