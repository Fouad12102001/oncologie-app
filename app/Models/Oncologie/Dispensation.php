<?php

namespace App\Models\Oncologie;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispensation extends Model
{
    use HasFactory;

    protected $connection = 'oncologie';
    protected $table = 'dispensations';

    protected $fillable = [
        'prescription_id',
        'medicament_id',
        'lot_id',
        'quantite',
        'date_dispensation',
        'user_id',
    ];

    protected $casts = [
        'quantite'           => 'integer',
        'date_dispensation'  => 'datetime',
    ];

    // ========================
    // RELATIONS
    // ========================

    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id')
            ->withDefault();
    }

    public function medicament()
    {
        return $this->belongsTo(Medicament::class, 'medicament_id')
            ->withDefault([
                'nom' => 'N/A'
            ]);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id')
            ->withDefault([
                'numero' => 'N/A'
            ]);
    }

    public function user()
    {
        return $this->belongsTo(OncoUser::class, 'user_id')
            ->withDefault([
                'name' => 'Utilisateur inconnu'
            ]);
    }

    // ========================
    // ACCESSORS UTILES
    // ========================

    public function getDateFormatteeAttribute()
    {
        return $this->date_dispensation
            ? $this->date_dispensation->format('d/m/Y H:i')
            : null;
    }

    public function getQuantiteFormateeAttribute()
    {
        return number_format($this->quantite, 0, ',', ' ');
    }

    public function scopeRecent($query)
{
    return $query->orderBy('date_dispensation', 'desc');
}
}