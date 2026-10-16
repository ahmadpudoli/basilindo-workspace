<?php

namespace App\Models;

use Core\Models\User;
use Illuminate\Database\Eloquent\Model;

class TicketHistory extends Model
{
    protected $connection = 'pm';
    protected $table = 'ticket_histories';
    protected $fillable = ['ticket_id', 'user_id', 'ticket_status_id'];

    public function ticket() { return $this->belongsTo(Ticket::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function status() { return $this->belongsTo(TicketStatus::class, 'ticket_status_id'); }
}
