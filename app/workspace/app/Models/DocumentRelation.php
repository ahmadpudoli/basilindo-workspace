<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRelation extends Model
{
    protected $connection = 'documents';
    protected $table = 'document_relations';
    protected $fillable = ['source_document_id', 'target_document_id', 'relation_type', 'created_by'];
    public function source() { return $this->belongsTo(Document::class, 'source_document_id'); }
    public function target() { return $this->belongsTo(Document::class, 'target_document_id'); }
}
