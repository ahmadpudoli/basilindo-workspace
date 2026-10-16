<?php

namespace App\Models;

use Core\Models\Project;
use Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectNote extends Model
{
    protected $connection = 'pm';
    protected $table = 'project_notes';
    protected $fillable = ['project_id', 'created_by', 'title', 'content', 'note_date'];

    protected function casts(): array { return ['note_date' => 'date']; }

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
