<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationItem extends Model
{
    protected $fillable = ['verification_case_id', 'document_id', 'rule_code', 'result', 'evidence', 'discrepancy', 'reviewer_comment', 'reviewed_by', 'reviewed_at'];
    protected function casts(): array { return ['evidence' => 'array', 'reviewed_at' => 'datetime']; }
    public function verificationCase() { return $this->belongsTo(VerificationCase::class); }
    public function document() { return $this->belongsTo(Document::class); }
}
