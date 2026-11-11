<?php

namespace App\Modules\CRM\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CrmClosingStatus extends Model
{
    use HasUuids;

    protected $connection = 'crm';
    protected $table = 'closing_statuses';
    protected $fillable = ['code', 'name', 'sequence', 'is_lost', 'is_ignored', 'is_closed', 'is_active'];
    protected function casts(): array { return ['is_lost' => 'boolean', 'is_ignored' => 'boolean', 'is_closed' => 'boolean', 'is_active' => 'boolean']; }
}
