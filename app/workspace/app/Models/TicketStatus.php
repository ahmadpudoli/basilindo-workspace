<?php

namespace App\Models;

use Core\Models\Project;
use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    protected $connection = 'pm';
    protected $table = 'ticket_statuses';
    protected $fillable = ['project_id', 'name', 'color', 'is_completed', 'sort_order'];
    protected function casts(): array { return ['is_completed' => 'boolean']; }
    public function project() { return $this->belongsTo(Project::class); }
    public function tickets() { return $this->hasMany(Ticket::class, 'ticket_status_id'); }
}
