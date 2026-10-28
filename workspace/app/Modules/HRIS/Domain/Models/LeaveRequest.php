<?php

namespace App\Modules\HRIS\Domain\Models;

use Core\Models\Company;
use Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LeaveRequest extends Model
{
    protected $connection = 'hris';
    protected $table = 'leave_requests';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['employee_id', 'company_id', 'type', 'status', 'total_days', 'reason', 'supporting_document_id', 'created_by', 'requester_user_id', 'submitted_at', 'revision_requested_by', 'revision_requested_role', 'revision_type', 'revision_reason', 'revision_requested_at', 'revision_submitted_at', 'revision_number'];
    protected function casts(): array { return ['total_days' => 'integer', 'revision_number' => 'integer', 'submitted_at' => 'datetime', 'manager_approved_at' => 'datetime', 'hr_approved_at' => 'datetime', 'revision_requested_at' => 'datetime', 'revision_submitted_at' => 'datetime', 'cancelled_at' => 'datetime']; }

    protected static function booted(): void { static::creating(fn (self $request) => $request->id ??= (string) Str::ulid()); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function requester() { return $this->belongsTo(User::class, 'requester_user_id'); }
    public function company() { return $this->belongsTo(Company::class); }
    public function approvalEvents() { return $this->hasMany(ApprovalEvent::class, 'request_id', 'id')->where('request_type', 'leave')->orderBy('created_at'); }
    public function dates() { return $this->hasMany(LeaveRequestDate::class, 'leave_request_id')->orderBy('leave_date'); }
    public function getDateListAttribute(): string { return collect($this->dates)->map(fn (LeaveRequestDate $date): string => $date->leave_date?->format('d/m/Y') ?? '-')->implode(', '); }
    public function getStartDateAttribute(): mixed { return collect($this->dates)->first()?->leave_date; }
    public function getEndDateAttribute(): mixed { return collect($this->dates)->last()?->leave_date; }
    public function scopeActive($query) { return $query->whereIn('status', ['pending_manager', 'pending_hr', 'approved']); }
    public function scopeNeedsRevision($query) { return $query->where('status', 'needs_revision'); }
    public function getStatusLabelAttribute(): string { return ['pending_manager' => 'Menunggu atasan', 'pending_hr' => 'Menunggu HR', 'needs_revision' => 'Revisi', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status)); }
}
