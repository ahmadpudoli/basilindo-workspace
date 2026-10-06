<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditEvent extends Model
{
    public $timestamps = false;
    protected $fillable = ['company_id', 'actor_id', 'action', 'subject_type', 'subject_id', 'request_id', 'metadata', 'created_at'];
    protected function casts(): array { return ['metadata' => 'array', 'created_at' => 'datetime']; }
    public function subject() { return $this->morphTo(); }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}
