<?php

namespace App\Modules\HRIS\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OvertimeRequest extends Model
{
    protected $connection = 'hris';
    protected $table = 'overtime_requests';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['employee_id', 'company_id', 'work_date', 'start_time', 'end_time', 'duration_minutes', 'status', 'reason', 'created_by'];
    protected function casts(): array { return ['work_date' => 'date', 'duration_minutes' => 'integer', 'manager_approved_at' => 'datetime', 'hr_approved_at' => 'datetime']; }
    protected static function booted(): void { static::creating(fn (self $item) => $item->id ??= (string) Str::ulid()); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function scopeVisibleTo($query, $user) { return $user->hasRole('super_admin') ? $query : $query->whereIn('company_id', $user->companies()->pluck('companies.id')); }
}
