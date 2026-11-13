<?php

namespace App\Modules\HRIS\Domain\Models;

use Core\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ApprovalEvent extends Model
{
    public $timestamps = false;
    protected $connection = 'hris';
    protected $table = 'approval_events';
    protected $fillable = ['request_type', 'request_id', 'step', 'action', 'actor_id', 'comment', 'created_at'];
    protected function casts(): array { return ['created_at' => 'datetime']; }
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? null : Carbon::parse($value, 'UTC')->setTimezone(config('app.timezone', 'Asia/Jakarta')),
        );
    }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}
