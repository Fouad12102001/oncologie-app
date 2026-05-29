<?php

namespace App\Models\Oncologie;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProtocoleMedicament extends Pivot
{
    protected $connection = 'oncologie';
    protected $table = 'protocole_medicament';

    protected $fillable = [
        'protocole_id',
        'medicament_id',
        'dose_standard',
        'type_calcul', // 'm2' | 'kg' | 'fixe'
        'ordre',
    ];

    protected $casts = [
        'dose_standard' => 'float',
        'ordre'         => 'integer',
    ];
}
