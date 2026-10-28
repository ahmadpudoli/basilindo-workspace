<?php

namespace App\Modules\HRIS\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequestDate extends Model
{
    protected $connection = 'hris';
    protected $table = 'leave_request_dates';

    protected $fillable = ['leave_request_id', 'leave_date'];

    protected function casts(): array
    {
        return ['leave_date' => 'date'];
    }

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }
}
