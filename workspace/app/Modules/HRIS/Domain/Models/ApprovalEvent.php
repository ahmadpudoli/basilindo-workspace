<?php

namespace App\Modules\HRIS\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalEvent extends Model
{
    public $timestamps = false;
    protected $connection = 'hris';
    protected $table = 'approval_events';
    protected $fillable = ['request_type', 'request_id', 'step', 'action', 'actor_id', 'comment', 'created_at'];
    protected function casts(): array { return ['created_at' => 'datetime']; }
}
