<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = ['company_id', 'name', 'code', 'required_fields', 'is_active'];

    protected function casts(): array
    {
        return ['required_fields' => 'array', 'is_active' => 'boolean'];
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function documents() { return $this->hasMany(Document::class); }
}
