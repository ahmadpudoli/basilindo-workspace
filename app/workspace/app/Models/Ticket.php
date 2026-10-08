<?php

namespace App\Models;

use Core\Models\Project;
use Core\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pm';
    protected $table = 'tickets';


    protected $fillable = ['project_id', 'ticket_status_id', 'priority_id', 'name', 'description', 'start_date', 'due_date', 'uuid', 'created_by'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'due_date' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $ticket): void {
            if (! $ticket->uuid) {
                $project = Project::find($ticket->project_id);
                $prefix = $project?->ticket_prefix ?: 'TKT';
                $ticket->uuid = Str::upper($prefix.'-'.Str::random(6));
            }
            $ticket->created_by ??= auth()->id();
        });
    }

    public function project() { return $this->belongsTo(Project::class); }
    public function status() { return $this->belongsTo(TicketStatus::class, 'ticket_status_id'); }
    public function priority() { return $this->belongsTo(TicketPriority::class, 'priority_id'); }
    public function assignees() { return $this->belongsToMany(User::class, 'ticket_users'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
