<?php

namespace App\Models\Oncologie;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class OncoUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'onco_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_locked',
        'login_attempts',
        'locked_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_locked' => 'boolean',
            'login_attempts' => 'integer',
            'locked_at' => 'datetime',
        ];
    }
}