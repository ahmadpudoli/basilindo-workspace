<?php

namespace App\Modules\CRM\Domain\Models;

use Core\Models\Company;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmLead extends Model
{
    use HasUuids, SoftDeletes;

    protected $connection = 'crm';
    protected $table = 'leads';

    protected $fillable = ['company_id', 'account_id', 'contact_id', 'name', 'email', 'phone', 'source', 'status', 'owner_user_id', 'notes', 'converted_opportunity_id', 'converted_at'];

    protected function casts(): array { return ['converted_at' => 'datetime']; }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function account()
    {
        return $this->belongsTo(CrmAccount::class);
    }

    public function contact() { return $this->belongsTo(CrmContact::class); }
    public function convertedOpportunity() { return $this->belongsTo(CrmOpportunity::class, 'converted_opportunity_id'); }
}
