<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class NumberingRule extends Model
{
    protected $connection = 'core';
    protected $table = 'numbering_rules';
    protected $fillable = ['company_id', 'form_key', 'name', 'format', 'counter_scope', 'period_key', 'next_number', 'is_active'];
    protected function casts(): array { return ['next_number' => 'integer', 'is_active' => 'boolean']; }
    public function company() { return $this->belongsTo(Company::class); }
}
