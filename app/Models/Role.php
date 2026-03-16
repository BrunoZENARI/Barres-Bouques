<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Role extends Model
{

    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    protected $fillable = [
                  'r_slug',
                  'r_libelle',
              ];

    public $timestamps = true;
              
    public function users()
    {
        return $this->hasMany(User::class);
    }


    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions', 'id_role','id_permission');
    }

}
