<?php

namespace App\Modules\HRIS\Domain\Models;

use Core\Models\Company;
use Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Employee extends Model
{
    use SoftDeletes;

    protected $connection = 'hris';
    protected $table = 'employees';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['company_id', 'user_id', 'employee_number', 'full_name', 'email', 'phone', 'job_title', 'manager_id', 'hire_date', 'status'];

    protected function casts(): array { return ['hire_date' => 'date']; }

    protected static function booted(): void
    {
        static::creating(function (self $employee): void { $employee->id ??= (string) Str::ulid(); });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function manager() { return $this->belongsTo(self::class, 'manager_id'); }
    public function subordinates() { return $this->hasMany(self::class, 'manager_id'); }
    public function leaveRequests() { return $this->hasMany(LeaveRequest::class); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('super_admin')) return $query;
        return $query->whereIn('company_id', $user->companies()->pluck('companies.id'));
    }
}
