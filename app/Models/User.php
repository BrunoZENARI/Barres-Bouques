<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'telephone',
        'adresse_numero',
        'adresse_rue',
        'adresse_complement1',
        'adresse_complement2',
        'adresse_code_postal',
        'adresse_ville',
        'role_id',
        'is_active',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_naissance'    => 'date',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'user_id');
    }

    public function hasPermission($slug)
    {
        return (bool) $this->role->permissions->where('slug', $slug)->count();
    }

    public function isActive()
    {
        return (bool) $this->is_active;
    }
}
