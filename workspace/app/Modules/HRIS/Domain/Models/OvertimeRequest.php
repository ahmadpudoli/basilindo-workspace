<?php

namespace App\Modules\HRIS\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeRequest extends Model
{
    use SoftDeletes;
    protected $connection = 'hris';
    protected $table = 'overtime_requests';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['request_number', 'employee_id', 'company_id', 'project_id', 'opportunity_id', 'work_reference_type', 'reference_name_snapshot', 'reference_status_snapshot', 'work_date', 'start_time', 'end_time', 'duration_minutes', 'status', 'reason', 'overtime_type', 'work_location', 'organization_unit_name', 'job_title_snapshot', 'created_by', 'requester_user_id', 'revision_requested_by', 'revision_requested_role', 'revision_type', 'revision_reason', 'revision_requested_at'];
    protected function casts(): array { return ['work_date' => 'date', 'duration_minutes' => 'integer', 'manager_approved_at' => 'datetime', 'hr_approved_at' => 'datetime', 'revision_requested_at' => 'datetime']; }
    protected static function booted(): void { static::creating(function (self $item): void { $item->id ??= (string) Str::ulid(); if ($item->status !== 'draft') $item->request_number ??= self::generateRequestNumber(); }); }
    public static function generateRequestNumber(): string { return 'HRIS-OVERTIME-'.now()->format('Ymd').'-'.Str::upper(Str::random(10)); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function project() { return $this->belongsTo(\Core\Models\Project::class); }
    public function opportunity() { return $this->belongsTo(\App\Modules\CRM\Domain\Models\CrmOpportunity::class, 'opportunity_id'); }
    public function entries() { return $this->hasMany(OvertimeRequestEntry::class)->orderBy('sort_order'); }
    public function approvalEvents() { return $this->hasMany(ApprovalEvent::class, 'request_id')->where('request_type', 'overtime')->orderBy('created_at'); }
    public function scopeVisibleTo($query, $user) { return $user->hasRole('super_admin') ? $query : $query->whereIn('company_id', $user->companies()->pluck('companies.id')); }
}
