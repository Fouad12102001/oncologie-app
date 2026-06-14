<?php

namespace App\Models\Oncologie;

use Illuminate\Database\Eloquent\Model;

class HistoriquePrescription extends Model
{
    protected $table = 'historique_prescriptions';

    protected $fillable = [
        'prescription_id',
        'ancien_statut',
        'nouveau_statut',
        'utilisateur_id',
        'commentaire',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(OncoUser::class, 'utilisateur_id');
    }
}