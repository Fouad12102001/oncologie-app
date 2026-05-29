<?php

namespace App\Models\Oncologie;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory;

    protected $connection = 'oncologie';
    protected $table = 'patients';

    protected $fillable = [
        'nom', 'prenom', 'numero_dossier', 'sexe',
        'date_naissance', 'age', 'type_cancer',
        'poids', 'taille', 'wilaya', 'daira',
        'statut_vital', 'date_deces',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_deces'     => 'date',
        'poids'          => 'float',
        'taille'         => 'float',
        'age'            => 'integer',
    ];

    const VIVANT = 'vivant';
    const DECEDE = 'decede';

    // ========================
    // BOOT
    // ========================

    protected static function booted()
    {
        static::saving(function ($patient) {
            // Calcul âge automatique
            if ($patient->date_naissance) {
                $patient->age = Carbon::parse($patient->date_naissance)->age;
            }

            // Statut par défaut
            if (empty($patient->statut_vital)) {
                $patient->statut_vital = self::VIVANT;
            }

            // Gestion décès intelligente
            if ($patient->statut_vital === self::DECEDE && empty($patient->date_deces)) {
                $patient->date_deces = now();
            }

            // Si redevient vivant → supprimer date décès
            if ($patient->statut_vital === self::VIVANT) {
                $patient->date_deces = null;
            }
        });
    }

    // ========================
    // ACCESSORS
    // ========================

    public function getEstVivantAttribute(): bool
    {
        return $this->statut_vital === self::VIVANT;
    }

    public function getStatutLabelAttribute(): string
    {
        return $this->est_vivant ? '🟢 Vivant' : '🔴 Décédé';
    }

    public function getSurfaceCorporelleAttribute(): ?float
    {
        if (!$this->poids || !$this->taille) return null;
        return round(sqrt(($this->poids * $this->taille) / 3600), 2);
    }

    // ========================
    // SCOPES
    // ========================

    public function scopeVivant($query)
    {
        return $query->where('statut_vital', self::VIVANT);
    }

    public function scopeDecede($query)
    {
        return $query->where('statut_vital', self::DECEDE);
    }

    // ========================
    // RELATIONS
    // ========================

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }
}