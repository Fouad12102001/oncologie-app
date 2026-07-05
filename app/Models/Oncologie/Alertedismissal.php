<?php

namespace App\Models\Oncologie;

use Illuminate\Database\Eloquent\Model;

class AlerteDismissal extends Model
{
    protected $connection = 'oncologie';
    protected $table = 'alerte_dismissals';

    protected $fillable = ['medicament_id', 'type', 'dismissed_at'];

    protected $casts = [
        'dismissed_at' => 'datetime',
    ];
}