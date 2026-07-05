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

    // ========================
    // ALERTES GLOBALES (utilisées dans le topbar + dashboard)
    // Prend en compte les suppressions manuelles (AlerteDismissal) :
    // - une alerte supprimée par l'utilisateur reste cachée tant que la
    //   situation ne change pas
    // - si la condition redevient fausse (ex: stock réapprovisionné), la
    //   suppression est nettoyée automatiquement, pour que l'alerte
    //   réapparaisse "fraîche" si le problème revient plus tard
    // ========================
    public static function listeAlertes(): array
    {
        $medicaments = static::with('mouvements')->get();

        $dismissals = \App\Models\Oncologie\AlerteDismissal::all()
            ->keyBy(fn ($d) => $d->medicament_id . '-' . $d->type);

        $alertes = [];

        foreach ($medicaments as $m) {
            $checks = [
                'rupture' => ['actif' => $m->enRupture(),      'icone' => '🚨', 'label' => 'Rupture de stock'],
                'stock'   => ['actif' => $m->stockCritique(),  'icone' => '⚠️', 'label' => 'Stock critique'],
                'expire'  => ['actif' => $m->estExpire(),      'icone' => '⛔', 'label' => 'Expiré'],
                'bientot' => ['actif' => $m->bientotExpire(),  'icone' => '📅', 'label' => 'Expire bientôt'],
            ];

            foreach ($checks as $type => $info) {
                $key = $m->id . '-' . $type;

                if (!$info['actif']) {
                    // Situation résolue : on nettoie une éventuelle suppression obsolète
                    if (isset($dismissals[$key])) {
                        $dismissals[$key]->delete();
                    }
                    continue;
                }

                // Toujours actif mais supprimé manuellement par l'utilisateur -> on ne réaffiche pas
                if (isset($dismissals[$key])) {
                    continue;
                }

                $alertes[] = [
                    'type'          => $type,
                    'icone'         => $info['icone'],
                    'message'       => "{$m->nom} — {$info['label']}",
                    'url'           => route('oncologie.medicaments.show', $m->id),
                    'medicament_id' => $m->id,
                ];
            }
        }

        return $alertes;
    }
}