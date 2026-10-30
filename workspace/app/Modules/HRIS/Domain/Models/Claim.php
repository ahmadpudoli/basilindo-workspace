<?php

namespace App\Modules\HRIS\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Claim extends Model
{
    protected $connection = 'hris';
    protected $table = 'claims';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['request_number', 'employee_id', 'company_id', 'type', 'status', 'category', 'purpose', 'amount', 'currency', 'supporting_document_id', 'accountability_document_id', 'created_by'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'manager_approved_at' => 'datetime', 'finance_approved_at' => 'datetime', 'final_approved_at' => 'datetime']; }
    protected static function booted(): void { static::creating(function (self $item): void { $item->id ??= (string) Str::ulid(); $item->request_number ??= self::generateRequestNumber(); }); }
    public static function generateRequestNumber(): string { return 'HRIS-CLAIM-'.now()->format('Ymd').'-'.Str::upper(Str::random(10)); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function scopeVisibleTo($query, $user) { return $user->hasRole('super_admin') ? $query : $query->whereIn('company_id', $user->companies()->pluck('companies.id')); }
}
