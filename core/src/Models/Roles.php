<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $connection = 'core';

    protected $fillable = ['name', 'guard_name'];
}
