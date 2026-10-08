<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmContact extends Model
{
    use HasUuids, SoftDeletes;

    protected $connection = 'crm';
    protected $table = 'contacts';

    protected $fillable = ['account_id', 'name', 'email', 'phone', 'job_title', 'is_primary', 'metadata'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'metadata' => 'array'];
    }

    public function account()
    {
        return $this->belongsTo(CrmAccount::class);
    }
}
