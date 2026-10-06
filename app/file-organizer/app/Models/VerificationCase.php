<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class VerificationCase extends Model
{
    use HasUuids;
    protected $fillable = ['company_id', 'status', 'assigned_to', 'created_by', 'summary', 'resolved_at'];
    protected function casts(): array { return ['resolved_at' => 'datetime']; }
    public function items() { return $this->hasMany(VerificationItem::class); }
}
