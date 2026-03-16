<?php

namespace App\Models;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'slug',
        'name',
        'category',
    ];

    public $timestamps = true;

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}
