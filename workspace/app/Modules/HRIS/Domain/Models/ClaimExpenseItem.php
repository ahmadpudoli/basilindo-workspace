<?php

namespace App\Modules\HRIS\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClaimExpenseItem extends Model
{
    protected $connection = 'hris';
    protected $table = 'claim_expense_items';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['claim_id', 'line_number', 'receipt_date', 'activity_date', 'expense_category_id', 'category', 'description', 'currency', 'amount', 'attachment_reference', 'attachment_object_key', 'is_active'];
    protected function casts(): array { return ['receipt_date' => 'date', 'activity_date' => 'date', 'amount' => 'decimal:2']; }
    protected static function booted(): void { static::creating(function (self $item): void { $item->id ??= (string) Str::ulid(); }); }
    public function claim() { return $this->belongsTo(Claim::class); }
    public function expenseCategory() { return $this->belongsTo(HrisExpenseCategory::class, 'expense_category_id'); }
}
