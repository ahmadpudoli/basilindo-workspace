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
    protected $fillable = ['request_number', 'employee_id', 'company_id', 'type', 'status', 'category', 'purpose', 'amount', 'currency', 'supporting_document_id', 'accountability_document_id', 'created_by', 'department', 'project_id', 'client_id', 'po_number', 'payment_method', 'paid_to', 'bank_name', 'bank_account', 'paid_amount', 'paid_at', 'payment_reference', 'payment_proof_id', 'completed_at', 'paid_by'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'bank_account' => 'encrypted', 'paid_at' => 'date', 'completed_at' => 'datetime', 'manager_approved_at' => 'datetime', 'finance_approved_at' => 'datetime', 'final_approved_at' => 'datetime']; }
    protected static function booted(): void { static::creating(function (self $item): void { $item->id ??= (string) Str::ulid(); if ($item->status !== 'draft') $item->request_number ??= self::generateRequestNumber(); }); }
    public static function generateRequestNumber(): string { return 'HRIS-CLAIM-'.now()->format('Ymd').'-'.Str::upper(Str::random(10)); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function expenseItems() { return $this->hasMany(ClaimExpenseItem::class)->orderBy('line_number'); }
    public function getStatusLabelAttribute(): string { return ['draft' => 'Draft', 'pending_manager' => 'Menunggu atasan', 'pending_finance' => 'Menunggu Finance', 'pending_final' => 'Menunggu approval final', 'pending_payment' => 'Menunggu pembayaran', 'paid' => 'Dibayar', 'completed' => 'Selesai', 'awaiting_accountability' => 'Menunggu pertanggungjawaban', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'needs_revision' => 'Perlu revisi', 'submitted' => 'Diajukan'][$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status)); }
    public function scopeVisibleTo($query, $user) { return $user->hasRole('super_admin') ? $query : $query->whereIn('company_id', $user->companies()->pluck('companies.id')); }
}
