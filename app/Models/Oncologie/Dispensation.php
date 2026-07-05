<?php
 
namespace App\Models\Oncologie;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Dispensation extends Model
{
    use HasFactory;
 
    protected $connection = 'oncologie';
    protected $table      = 'dispensations';
 
    protected $fillable = [
        'prescription_id',
        'medicament_id',
        'lot_id',
        'quantite',
        'date_dispensation',
        'user_id',
        'notes',
        'validee_par',
    ];
 
    protected $casts = [
        'quantite'          => 'integer',
        'date_dispensation' => 'datetime',
    ];
 
    // ════════════════════════════════════════
    // RELATIONS
    // ════════════════════════════════════════
 
    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id')->withDefault();
    }
 
    public function medicament()
    {
        return $this->belongsTo(Medicament::class, 'medicament_id')
            ->withDefault(['nom' => 'N/A']);
    }
 
    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id')
            ->withDefault(['numero' => 'N/A']);
    }
 
    public function user()
    {
        return $this->belongsTo(OncoUser::class, 'user_id')
            ->withDefault(['name' => 'Utilisateur inconnu']);
    }
 
    // ════════════════════════════════════════
    // ACCESSORS
    // ════════════════════════════════════════
 
    public function getDateFormatteeAttribute(): ?string
    {
        return $this->date_dispensation
            ? $this->date_dispensation->format('d/m/Y H:i')
            : null;
    }
 
    public function getQuantiteFormateeAttribute(): string
    {
        return number_format($this->quantite, 0, ',', ' ');
    }
 
    public function getPatientNomCompletAttribute(): string
    {
        $patient = optional($this->prescription)->patient;
        if (!$patient) return 'N/A';
        return trim($patient->nom . ' ' . $patient->prenom);
    }
 
    public function getLotInfoAttribute(): string
    {
        $lot = $this->lot;
        if (!$lot || !$lot->numero || $lot->numero === 'N/A') return 'N/A';
        $exp = $lot->date_expiration
            ? ' (exp. ' . \Carbon\Carbon::parse($lot->date_expiration)->format('m/Y') . ')'
            : '';
        return $lot->numero . $exp;
    }
 
    // ════════════════════════════════════════
    // SCOPES
    // ════════════════════════════════════════
 
    public function scopeRecent($query)
    {
        return $query->orderBy('date_dispensation', 'desc');
    }
 
    public function scopePourMedicament($query, int $medicamentId)
    {
        return $query->where('medicament_id', $medicamentId);
    }
 
    public function scopeParPeriode($query, string $debut, string $fin)
    {
        return $query
            ->whereDate('date_dispensation', '>=', $debut)
            ->whereDate('date_dispensation', '<=', $fin);
    }
}