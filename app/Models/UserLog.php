<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class UserLog extends Model
{
    protected $connection = 'mongodb';  // Utiliser la connexion MongoDB
    protected $collection = 'user_logs'; // Spécifier la collection MongoDB

    protected $fillable = [
        'user_id', 'action', 'url', 'method', 'ip', 'user_agent',
        'status_code', 'duration', 'created_at'
    ];
}
