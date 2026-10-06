<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    use HasUuids;
    protected $fillable = ['company_id', 'requested_by', 'status', 'criteria', 'disk', 'object_key', 'document_count', 'expires_at', 'completed_at'];
    protected function casts(): array { return ['criteria' => 'array', 'expires_at' => 'datetime', 'completed_at' => 'datetime']; }

    public function company() { return $this->belongsTo(Company::class); }

    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
}
