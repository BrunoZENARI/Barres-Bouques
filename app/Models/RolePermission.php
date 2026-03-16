<?php

namespace App\Models;

use Carbon;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles_permissions';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_permission',
                  'id_role',
              ];

    public $timestamps = false;

    public function roles()
    {
        return $this->hasMany(Role::class, 'id');
    }
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'id');
    }

}
