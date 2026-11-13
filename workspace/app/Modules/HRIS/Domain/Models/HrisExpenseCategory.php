<?php

namespace App\Modules\HRIS\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HrisExpenseCategory extends Model
{
    protected $connection = 'hris';
    protected $table = 'expense_categories';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['name', 'description', 'sort_order', 'is_active', 'archived_at', 'archived_by'];
    protected function casts(): array { return ['is_active' => 'boolean', 'archived_at' => 'datetime']; }
    protected static function booted(): void { static::creating(function (self $category): void { $category->id ??= (string) Str::ulid(); }); }

    public function scopeActive($query) { return $query->where('is_active', true)->whereNull('archived_at'); }
}
