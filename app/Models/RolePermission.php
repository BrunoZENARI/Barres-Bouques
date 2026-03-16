<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'role_permissions';

    protected $fillable = [
        'permission_id',
        'role_id',
    ];

    public $timestamps = false;
}
