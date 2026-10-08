<?php

namespace App\Models;

use Core\Models\Company;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class DocumentParty extends Model
{
    protected $connection = 'documents';
    protected $table = 'document_parties';

    protected $fillable = ['document_id', 'company_id', 'role'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
