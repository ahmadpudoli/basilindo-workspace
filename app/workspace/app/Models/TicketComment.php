<?php

namespace App\Models;

use Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketComment extends Model
{
    protected $connection = 'pm';
    protected $table = 'ticket_comments';
    protected $fillable = ['ticket_id', 'user_id', 'comment'];

    public function ticket(): BelongsTo { return $this->belongsTo(Ticket::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
