<?php

namespace App\Modules\HRIS\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Claim extends Model
{
    use SoftDeletes;
    protected $connection = 'hris';
    protected $table = 'claims';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['request_number', 'employee_id', 'company_id', 'type', 'expense_scope', 'work_reference_type', 'status', 'category', 'purpose', 'amount', 'currency', 'supporting_document_id', 'accountability_document_id', 'created_by', 'requester_user_id', 'submitted_at', 'revision_requested_by', 'revision_requested_role', 'revision_type', 'revision_reason', 'revision_requested_at', 'revision_submitted_at', 'revision_number', 'department', 'project_id', 'opportunity_id', 'client_id', 'po_number', 'reference_name_snapshot', 'reference_status_snapshot', 'payment_method', 'paid_to', 'bank_name', 'bank_account', 'paid_amount', 'paid_at', 'payment_reference', 'payment_proof_id', 'completed_at', 'paid_by'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'bank_account' => 'encrypted', 'paid_at' => 'date', 'submitted_at' => 'datetime', 'revision_requested_at' => 'datetime', 'revision_submitted_at' => 'datetime', 'completed_at' => 'datetime', 'manager_approved_at' => 'datetime', 'finance_approved_at' => 'datetime', 'final_approved_at' => 'datetime']; }
    protected static function booted(): void { static::creating(function (self $item): void { $item->id ??= (string) Str::ulid(); if ($item->status !== 'draft') $item->request_number ??= self::generateRequestNumber(); }); }
    public static function generateRequestNumber(): string { return 'HRIS-CLAIM-'.now()->format('Ymd').'-'.Str::upper(Str::random(10)); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function managerApprover() { return $this->belongsTo(\Core\Models\User::class, 'manager_approved_by'); }
    public function financeApprover() { return $this->belongsTo(\Core\Models\User::class, 'finance_approved_by'); }
    public function finalApprover() { return $this->belongsTo(\Core\Models\User::class, 'final_approved_by'); }
    public function expenseItems() { return $this->hasMany(ClaimExpenseItem::class)->where('is_active', true)->orderBy('line_number'); }
    public function approvalEvents() { return $this->hasMany(ApprovalEvent::class, 'request_id')->where('request_type', 'claim')->orderBy('created_at'); }
    public function project() { return $this->belongsTo(\Core\Models\Project::class); }
    public function opportunity() { return $this->belongsTo(\App\Modules\CRM\Domain\Models\CrmOpportunity::class, 'opportunity_id'); }
    public function getStatusLabelAttribute(): string { return ['draft' => 'Draft', 'pending_manager' => 'Menunggu persetujuan atasan', 'pending_finance' => 'Menunggu persetujuan Finance', 'pending_final' => 'Menunggu approval final', 'pending_payment' => 'Menunggu pembayaran', 'paid' => 'Dibayar', 'completed' => 'Selesai', 'awaiting_accountability' => 'Menunggu pertanggungjawaban', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'needs_revision' => 'Perlu revisi', 'submitted' => 'Diajukan'][$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status)); }
    public function scopeVisibleTo($query, $user) { return $user->hasRole('super_admin') ? $query : $query->whereIn('company_id', $user->companies()->pluck('companies.id')); }
}
