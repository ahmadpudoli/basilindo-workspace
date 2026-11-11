<?php

namespace App\Modules\HRIS\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OvertimeRequestEntry extends Model
{
    protected $connection = 'hris';
    protected $table = 'overtime_request_entries';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['overtime_request_id', 'work_date', 'day_type', 'start_time', 'end_time', 'duration_minutes', 'work_description', 'sort_order'];
    protected function casts(): array { return ['work_date' => 'date', 'duration_minutes' => 'integer', 'sort_order' => 'integer']; }
    protected static function booted(): void { static::creating(fn (self $entry) => $entry->id ??= (string) Str::ulid()); }
    public function overtimeRequest() { return $this->belongsTo(OvertimeRequest::class); }
}
