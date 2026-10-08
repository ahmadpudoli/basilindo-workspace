<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPriority extends Model
{
    protected $connection = 'pm';
    protected $table = 'ticket_priorities';
    protected $fillable = ['name', 'color', 'sort_order'];
    public function tickets() { return $this->hasMany(Ticket::class, 'priority_id'); }
}
