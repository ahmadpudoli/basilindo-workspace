<?php

namespace App\Models;

use Core\Models\Company;
use Core\Models\User;
use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    protected $connection = 'documents';
    protected $table = 'saved_searches';
    protected $fillable = ['user_id', 'company_id', 'name', 'filters'];

    protected function casts(): array
    {
        return ['filters' => 'array'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function company() { return $this->belongsTo(Company::class); }
}
