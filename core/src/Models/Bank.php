<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $connection = 'core';
    protected $table = 'banks';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['bank_code', 'bic', 'name', 'short_name', 'status', 'source_updated_at', 'sort_order'];

    protected function casts(): array
    {
        return ['source_updated_at' => 'date', 'sort_order' => 'integer'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
