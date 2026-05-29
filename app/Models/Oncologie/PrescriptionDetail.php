<?php

namespace App\Models\Oncologie;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionDetail extends Model
{
    use HasFactory;

    protected $connection = 'oncologie';
    protected $table = 'prescription_details';

    protected $fillable = [
        'prescription_id',
        'medicament_id',
        'dose_calculee',
        'dose_standard',
        'type_calcul', // 'm2' | 'kg' | 'fixe'
    ];

    protected $casts = [
        'dose_calculee' => 'float',
        'dose_standard' => 'float',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }

    public function medicament()
    {
        return $this->belongsTo(Medicament::class, 'medicament_id')
            ->withDefault(['nom' => 'N/A']);
    }
}