<?php

namespace App\Models\Oncologie;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    use HasFactory;

    protected $connection = 'oncologie';
    protected $table = 'mouvements_stock';
    public $timestamps = false;

    protected $fillable = [
        'medicament_id',
        'type',
        'quantite',
        'date_mouvement',
    ];

    protected $casts = [
        'quantite'       => 'integer',
        'date_mouvement' => 'datetime',
    ];

    const TYPE_ENTREE = 'entree';
    const TYPE_SORTIE = 'sortie';

    public static function types(): array
    {
        return [self::TYPE_ENTREE, self::TYPE_SORTIE];
    }

    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class, 'medicament_id');
    }

    public function isEntree(): bool
    {
        return $this->type === self::TYPE_ENTREE;
    }

    public function isSortie(): bool
    {
        return $this->type === self::TYPE_SORTIE;
    }

    public function signeQuantite(): int
    {
        return $this->isEntree() ? $this->quantite : -$this->quantite;
    }
}