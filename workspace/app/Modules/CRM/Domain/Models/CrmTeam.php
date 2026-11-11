<?php

namespace App\Modules\CRM\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CrmTeam extends Model
{
    use HasUuids;
    protected $connection = 'crm';
    protected $table = 'teams';
    protected $fillable = ['company_id', 'name', 'leader_user_id', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function opportunities() { return $this->hasMany(CrmOpportunity::class, 'team_id'); }
}
