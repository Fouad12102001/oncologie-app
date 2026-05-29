<?php

namespace App\Models\Oncologie;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class OncoUser extends Authenticatable
{
    use Notifiable;
    

    protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'is_locked',
    'login_attempts',
    'locked_at'
];
    protected $hidden = [
        'password',
        'remember_token',
    ];
}