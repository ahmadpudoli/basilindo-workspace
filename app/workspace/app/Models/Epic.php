<?php

namespace App\Models;

use Core\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Epic extends Model
{
    protected $connection = 'pm';
    protected $table = 'epics';
    protected $fillable = ['project_id', 'name', 'description', 'start_date', 'end_date', 'sort_order'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function tickets(): HasMany { return $this->hasMany(Ticket::class); }
}
