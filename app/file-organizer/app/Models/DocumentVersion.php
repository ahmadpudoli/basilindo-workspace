<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    use HasUuids;

    protected $fillable = ['document_id', 'version', 'disk', 'object_key', 'mime_type', 'file_size', 'checksum', 'uploaded_by', 'is_current'];

    protected function casts(): array { return ['is_current' => 'boolean']; }
    public function document() { return $this->belongsTo(Document::class); }
}
