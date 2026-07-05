<?php
 
namespace App\Models\Oncologie;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
 
class Patient extends Model
{
    use HasFactory;
 
    protected $connection = 'oncologie';
    protected $table      = 'patients';
 
    protected $fillable = [
        'nom', 'prenom', 'numero_dossier', 'sexe',
        'date_naissance', 'age', 'type_cancer',
        'poids', 'taille', 'wilaya', 'daira',
        'statut_vital', 'date_deces',
        'allergies', 'creatinine', 'clairance_renale',
        'surface_corporelle', 'groupe_sanguin',
        'telephone', 'medecin_traitant',
        'stade_cancer', 'antecedents', 'notes',
        'imc', 'dfg_ckdepi',
    ];
 
    protected $casts = [
        'date_naissance' => 'date',
        'date_deces'     => 'date',
        'poids'          => 'float',
        'taille'         => 'float',
        'creatinine'     => 'float',
        'age'            => 'integer',
    ];
 
    const VIVANT = 'vivant';
    const DECEDE = 'decede';
 
    // ════════════════════════════════════════
    // BOOT — calculs automatiques à chaque sauvegarde
    // ════════════════════════════════════════
    protected static function booted(): void
    {
        static::saving(function (Patient $p) {
            // Âge depuis date de naissance
            if ($p->date_naissance) {
                $p->age = Carbon::parse($p->date_naissance)->age;
            }
 
            // Statut par défaut
            if (empty($p->statut_vital)) {
                $p->statut_vital = self::VIVANT;
            }
 
            // Date décès automatique si décédé sans date
            if ($p->statut_vital === self::DECEDE && empty($p->date_deces)) {
                $p->date_deces = now();
            }
 
            // Réinitialiser date_deces si redevient vivant
            if ($p->statut_vital === self::VIVANT) {
                $p->date_deces = null;
            }
 
            // Surface corporelle — Mosteller : SC = √(Taille × Poids / 3600)
            if ($p->poids && $p->taille) {
                $p->surface_corporelle = round(sqrt(($p->poids * $p->taille) / 3600), 2);
            }
 
            // IMC : Poids / (Taille_m²)
            if ($p->poids && $p->taille) {
                $tailleM = $p->taille / 100;
                $p->imc  = round($p->poids / ($tailleM * $tailleM), 1);
            }
 
            // Clairance Cockcroft-Gault (si créatinine disponible)
            if ($p->creatinine && $p->creatinine > 0 && $p->age && $p->poids) {
                $p->clairance_renale = self::calculCockcroft(
                    $p->age, $p->poids, $p->creatinine, $p->sexe
                );
            }
 
            // DFG CKD-EPI 2021
            if ($p->creatinine && $p->creatinine > 0 && $p->age) {
                $p->dfg_ckdepi = self::calculCKDEPI($p->age, $p->creatinine, $p->sexe);
            }
        });
    }
 
    // ════════════════════════════════════════
    // FORMULES PHARMACOLOGIQUES
    // ════════════════════════════════════════
 
    /**
     * Surface corporelle — Mosteller
     * SC = √( Taille(cm) × Poids(kg) / 3600 )
     */
    public static function calculSC(float $taille, float $poids): float
    {
        if ($taille <= 0 || $poids <= 0) return 0.0;
        return round(sqrt(($taille * $poids) / 3600), 2);
    }
 
    /**
     * Cockcroft-Gault  (créatinine en mg/dL)
     * Homme : (140 − Age) × Poids / (72 × Créatinine)
     * Femme : × 0.85
     */
    public static function calculCockcroft(
        float $age, float $poids, float $creatinine, string $sexe
    ): float {
        if ($creatinine <= 0 || $age <= 0 || $poids <= 0) return 0.0;
        $clcr = ((140 - $age) * $poids) / (72 * $creatinine);
        if (strtolower($sexe[0] ?? '') === 'f') $clcr *= 0.85;
        return round($clcr, 2);
    }
 
    /**
     * CKD-EPI 2021 sans ethnie (créatinine en mg/dL)
     * Femme : κ = 0.7, α = −0.241
     * Homme : κ = 0.9, α = −0.302
     */
    public static function calculCKDEPI(
        float $age, float $creatinine, string $sexe
    ): float {
        if ($creatinine <= 0 || $age <= 0) return 0.0;
        $isFemme = strtolower($sexe[0] ?? '') === 'f';
        $kappa   = $isFemme ? 0.7  : 0.9;
        $alpha   = $isFemme ? -0.241 : -0.302;
        $ratio   = $creatinine / $kappa;
        $eGFR    = $ratio <= 1
            ? 142 * pow($ratio, $alpha)    * pow(0.9938, $age)
            : 142 * pow($ratio, -1.200)    * pow(0.9938, $age);
        if ($isFemme) $eGFR *= 1.012;
        return round($eGFR, 2);
    }
 
    /**
     * Calvert — Carboplatine
     * Dose = AUC × (DFG + 25)
     */
    public static function calculCalvert(float $auc, float $dfg): float
    {
        return round($auc * ($dfg + 25), 2);
    }
 
    // ════════════════════════════════════════
    // ACCESSORS
    // ════════════════════════════════════════
 
    public function getEstVivantAttribute(): bool
    {
        return $this->statut_vital === self::VIVANT;
    }
 
    public function getStatutLabelAttribute(): string
    {
        return $this->est_vivant ? '🟢 Vivant' : '🔴 Décédé';
    }
 
    /** SC recalculée à la volée si non persistée */
    public function getSurfaceCorporelleCalculeeAttribute(): ?float
    {
        if ($this->surface_corporelle) return $this->surface_corporelle;
        if (!$this->poids || !$this->taille) return null;
        return self::calculSC($this->taille, $this->poids);
    }
 
    public function getImcLabelAttribute(): string
    {
        $imc = $this->imc ?? 0;
        if ($imc <= 0)   return '—';
        if ($imc < 18.5) return 'Insuffisance pondérale';
        if ($imc < 25)   return 'Normal';
        if ($imc < 30)   return 'Surpoids';
        return 'Obésité';
    }
 
    public function getStatutDfgAttribute(): string
    {
        $dfg = $this->dfg_ckdepi ?? $this->clairance_renale ?? 0;
        if ($dfg <= 0)   return 'Non calculé';
        if ($dfg >= 90)  return 'Normal (≥ 90)';
        if ($dfg >= 60)  return 'Légèrement diminué (60-89)';
        if ($dfg >= 30)  return 'Modérément diminué (30-59)';
        if ($dfg >= 15)  return 'Sévèrement diminué (15-29)';
        return 'Insuffisance rénale terminale (< 15)';
    }
 
    public function getDureeeSurvieAttribute(): ?string
    {
        if ($this->est_vivant || !$this->date_deces || !$this->date_naissance) return null;
        return $this->date_naissance->diffForHumans($this->date_deces, true);
    }
 
    // ════════════════════════════════════════
    // SCOPES
    // ════════════════════════════════════════
 
    public function scopeVivant($query)       { return $query->where('statut_vital', self::VIVANT); }
    public function scopeDecede($query)       { return $query->where('statut_vital', self::DECEDE); }
    public function scopeParCancer($query, string $type) { return $query->where('type_cancer', $type); }
    public function scopeParWilaya($query, string $w)    { return $query->where('wilaya', $w); }
 
    // ════════════════════════════════════════
    // RELATIONS
    // ════════════════════════════════════════
 
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }
 
    public function prescriptionsActives()
    {
        return $this->hasMany(Prescription::class, 'patient_id')
            ->where('statut', 'validee');
    }
}