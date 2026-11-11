<?php

namespace App\Modules\CRM\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmContact extends Model
{
    use HasUuids, SoftDeletes;

    protected $connection = 'crm';
    protected $table = 'contacts';

    protected $fillable = ['account_id', 'name', 'email', 'phone', 'job_title', 'is_primary', 'metadata', 'source_system', 'source_database', 'external_id', 'is_integrated', 'last_synced_at'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'metadata' => 'array', 'is_integrated' => 'boolean', 'last_synced_at' => 'datetime'];
    }

    public function account()
    {
        return $this->belongsTo(CrmAccount::class);
    }
}
