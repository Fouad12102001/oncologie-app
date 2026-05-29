<?php

namespace App\Models\Oncologie;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Prescription extends Model
{
    use HasFactory;

    protected $connection = 'oncologie';
    protected $table = 'prescriptions';

    protected $fillable = [
        'patient_id', 'medecin_id', 'protocole_id',
        'medecin_nom', 'date_prescription',
        'dosage', 'frequence', 'duree', 'statut',
    ];

    protected $casts = [
        'date_prescription' => 'datetime',
        'dosage'            => 'float',
        'statut'            => 'string',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    protected $attributes = [
        'statut' => 'en_attente',
    ];

    // ========================
    // RELATIONS
    // ========================

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id')
            ->withDefault(['nom' => 'N/A', 'prenom' => '']);
    }

    public function medecin()
    {
        return $this->belongsTo(OncoUser::class, 'medecin_id')
            ->withDefault(['name' => 'Non défini']);
    }

    public function protocole()
    {
        return $this->belongsTo(Protocole::class, 'protocole_id')
            ->withDefault(['nom' => 'Aucun protocole']);
    }

    public function details()
    {
        return $this->hasMany(PrescriptionDetail::class, 'prescription_id')
            ->with('medicament');
    }

    public function dispensations()
    {
        return $this->hasMany(Dispensation::class, 'prescription_id')
            ->with(['lot', 'medicament']);
    }

    // ========================
    // SCOPES
    // ========================

    public function scopeStatut(Builder $query, ?string $statut)
    {
        return $statut ? $query->where('statut', $statut) : $query;
    }

    public function scopePatient(Builder $query, ?string $search)
    {
        return $search
            ? $query->whereHas('patient', fn($q) =>
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%"))
            : $query;
    }

    public function scopeDate(Builder $query, ?string $date)
    {
        return $date ? $query->whereDate('date_prescription', $date) : $query;
    }

    // ========================
    // HELPERS
    // ========================

    public function isValidee(): bool
    {
        return $this->statut === 'validee';
    }

    public function isAnnulee(): bool
    {
        return $this->statut === 'annulee';
    }

    public function isEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }

    // Alias pour la vue dispensation
    public function getValidationAttribute(): bool
    {
        return $this->isValidee();
    }

    // ========================
    // ACCESSORS
    // ========================

    public function getMedicamentsNomsAttribute(): string
    {
        if (!$this->relationLoaded('details') || $this->details->isEmpty()) {
            return 'N/A';
        }
        return $this->details->pluck('medicament.nom')->filter()->join(', ') ?: 'N/A';
    }

    public function getDosesCalculeesAttribute(): string
    {
        if (!$this->relationLoaded('details') || $this->details->isEmpty()) {
            return 'N/A';
        }

        $poids  = $this->patient->poids ?? 0;
        $taille = $this->patient->taille ?? 0;
        $sc     = ($poids && $taille) ? sqrt($poids * $taille / 3600) : 0;

        return $this->details->map(function ($detail) use ($poids, $sc) {
            $dose = $detail->dose_calculee ?? 0;
            return "{$detail->medicament->nom}: " . number_format($dose, 2) . " mg";
        })->implode(' | ');
    }

    public function getSurfaceCorporelleAttribute(): ?float
    {
        $poids  = $this->patient->poids ?? 0;
        $taille = $this->patient->taille ?? 0;

        if (!$poids || !$taille) return null;

        return round(sqrt($poids * $taille / 3600), 2);
    }
}
