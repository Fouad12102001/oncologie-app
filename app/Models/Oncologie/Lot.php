<?php

namespace App\Models\Oncologie;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lot extends Model
{
    use HasFactory;

    protected $connection = 'oncologie';
    protected $table = 'lots';

    protected $fillable = [
        'medicament_id',
        'numero',
        'quantite_initiale',
        'date_fabrication',
        'date_expiration',
    ];

    protected $casts = [
        'date_fabrication'  => 'date',
        'date_expiration'   => 'date',
        'quantite_initiale' => 'integer',
    ];

    // ========================
    // RELATIONS
    // ========================

    public function medicament()
    {
        return $this->belongsTo(Medicament::class, 'medicament_id');
    }

    public function dispensations()
    {
        return $this->hasMany(Dispensation::class, 'lot_id');
    }

    // ========================
    // STOCK DU LOT
    // ========================

    public function stockRestant(): int
    {
        $dispenseTotal = $this->dispensations()->sum('quantite');
        return max(0, $this->quantite_initiale - (int) $dispenseTotal);
    }

    // ========================
    // EXPIRATION
    // ========================

    public function estExpire(): bool
    {
        return $this->date_expiration->isPast();
    }

    public function expireBientot(int $jours = 90): bool
    {
        if ($this->estExpire()) return false;
        return now()->diffInDays($this->date_expiration) <= $jours;
    }

    public function moisRestants(): int
    {
        return (int) now()->diffInMonths($this->date_expiration, false);
    }

    // ========================
    // STATUT ALERTE
    // ========================

    public function statutAlerte(): string
    {
        if ($this->estExpire() || $this->stockRestant() <= 0) return 'danger';
        if ($this->expireBientot(90)) return 'warning';
        return 'ok';
    }

    public function alerteRouge(): bool
    {
        return $this->estExpire() || $this->stockRestant() <= 0;
    }

    public function alerteOrange(): bool
    {
        return $this->expireBientot(90) && !$this->estExpire();
    }

    // ========================
    // ACCESSORS
    // ========================

    public function getIsExpireAttribute(): bool
    {
        return $this->estExpire();
    }
}