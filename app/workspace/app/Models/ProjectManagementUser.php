<?php

namespace App\Models;

use Core\Models\User as CoreUser;

class ProjectManagementUser extends CoreUser
{
    protected $connection = 'project_management';
    protected $table = 'core_users';
}
