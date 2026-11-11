<?php

namespace App\Modules\CRM\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CrmPipelineStage extends Model
{
    use HasUuids;

    protected $connection = 'crm';
    protected $table = 'pipeline_stages';
    protected $fillable = ['code', 'name', 'sequence', 'is_won', 'is_delivery', 'is_folded', 'requires_expected_close', 'is_active'];
    protected function casts(): array { return ['is_won' => 'boolean', 'is_delivery' => 'boolean', 'is_folded' => 'boolean', 'requires_expected_close' => 'boolean', 'is_active' => 'boolean']; }
    public function opportunities() { return $this->hasMany(CrmOpportunity::class, 'stage_id'); }
}
