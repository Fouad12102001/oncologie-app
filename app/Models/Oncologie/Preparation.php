<?php

namespace App\Models\Oncologie;

use Illuminate\Database\Eloquent\Model;

class Preparation extends Model
{
    protected $table = 'preparations';

    protected $fillable = [
        'prescription_id',
        'preparateur',
        'pharmacien_valideur',
        'date_preparation',
        'heure_preparation',
        'volume_final',
        'stabilite',
        'observations',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}