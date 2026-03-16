<?php

namespace App\Models;

use Carbon;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'p_slug',
                  'p_libelle',
                  'p_categorie',
              ];

    public $timestamps = true;

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_permissions', 'id_permission','id_role');
    }

}
