<?php

namespace App\Models\Oncologie;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Medicament extends Model
{
    use HasFactory;

    protected $connection = 'oncologie';
    protected $table = 'medicaments';

    protected $fillable = [
        'nom',
        'quantite_min',
        'quantite_initiale',
        'date_fabrication',
        'date_expiration',
    ];

    protected $casts = [
        'quantite_min'      => 'integer',
        'quantite_initiale' => 'integer',
        'date_fabrication'  => 'date',
        'date_expiration'   => 'date',
    ];

    // ========================
    // RELATIONS
    // ========================

    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'medicament_id');
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class, 'medicament_id');
    }

    public function protocoles()
    {
        return $this->belongsToMany(
            Protocole::class,
            'protocole_medicament',
            'medicament_id',
            'protocole_id'
        )->withPivot(['dose_standard', 'type_calcul', 'ordre'])
         ->withTimestamps();
    }

    // ========================
    // STOCK (via mouvements)
    // ========================

    public function totalEntrees(): int
    {
        return (int) $this->mouvements()
            ->where('type', MouvementStock::TYPE_ENTREE)
            ->sum('quantite');
    }

    public function totalSorties(): int
    {
        return (int) $this->mouvements()
            ->where('type', MouvementStock::TYPE_SORTIE)
            ->sum('quantite');
    }

    public function stockActuel(): int
    {
        return max(0, $this->totalEntrees() - $this->totalSorties());
    }

    public function getStockAttribute(): int
    {
        return $this->stockActuel();
    }

    // ========================
    // STATUTS STOCK
    // ========================

    public function enRupture(): bool
    {
        return $this->stockActuel() <= 0;
    }

    public function stockCritique(): bool
    {
        return $this->stockActuel() > 0
            && $this->stockActuel() <= $this->quantite_min;
    }

    public function statutStock(): string
    {
        $stock = $this->stockActuel();

        if ($stock <= 0) return 'rupture';
        if ($stock <= $this->quantite_min) return 'alerte';
        return 'ok';
    }

    // ========================
    // STATUTS EXPIRATION
    // ========================

    public function estExpire(): bool
    {
        if (!$this->date_expiration) return false;
        return $this->date_expiration->isPast();
    }

    public function bientotExpire(int $jours = 90): bool
    {
        if (!$this->date_expiration) return false;
        if ($this->estExpire()) return false;

        return now()->diffInDays($this->date_expiration) <= $jours;
    }

    public function statutExpiration(): string
    {
        if (!$this->date_expiration) return 'ok';
        if ($this->estExpire()) return 'expired';
        if ($this->bientotExpire()) return 'soon';
        return 'ok';
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
