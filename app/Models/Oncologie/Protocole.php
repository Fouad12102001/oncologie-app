<?php

namespace App\Models\Oncologie;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Protocole extends Model
{
    use HasFactory;

    protected $connection = 'oncologie';
    protected $table = 'protocoles';

    protected $fillable = [
        'nom', 'description', 'type_cancer', 'duree', 'statut',
    ];

    protected $casts = [
        'duree'  => 'integer',
        'statut' => 'string',
    ];

    // ========================
    // RELATIONS
    // ========================

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'protocole_id');
    }

    public function medicaments()
    {
        return $this->belongsToMany(
            Medicament::class,
            'protocole_medicament',
            'protocole_id',
            'medicament_id'
        )
        ->withPivot(['dose_standard', 'type_calcul', 'ordre'])
        ->orderByPivot('ordre')
        ->withTimestamps();
    }
}