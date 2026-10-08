<?php

namespace App\Models;

use Core\Models\Company;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmLead extends Model
{
    use HasUuids, SoftDeletes;

    protected $connection = 'crm';
    protected $table = 'leads';

    protected $fillable = ['company_id', 'account_id', 'name', 'email', 'phone', 'source', 'status', 'owner_user_id', 'notes'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function account()
    {
        return $this->belongsTo(CrmAccount::class);
    }
}
