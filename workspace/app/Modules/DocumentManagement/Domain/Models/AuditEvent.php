<?php

namespace App\Modules\DocumentManagement\Domain\Models;

use Core\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditEvent extends Model
{
    protected $connection = 'documents';
    protected $table = 'audit_events';
    public $timestamps = false;
    protected $fillable = ['company_id', 'actor_id', 'action', 'module', 'subject_type', 'subject_id', 'request_id', 'ip_address', 'user_agent', 'device', 'http_method', 'route_name', 'metadata', 'old_values', 'new_values', 'created_at'];
    protected function casts(): array { return ['metadata' => 'array', 'old_values' => 'array', 'new_values' => 'array', 'created_at' => 'datetime']; }
    public function subject() { return $this->morphTo(); }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}
