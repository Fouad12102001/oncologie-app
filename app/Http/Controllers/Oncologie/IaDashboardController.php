<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use App\Models\Oncologie\Medicament;
use App\Models\Oncologie\MouvementStock;

class IaDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord IA global.
     *
     * On ne lance PAS les appels au service IA ici (ce serait bloquant :
     * un appel Holt-Winters + un appel z-score par médicament, en série,
     * pourrait prendre plusieurs secondes/minutes sur une grosse pharmacie).
     * On se contente de fournir la liste des médicaments éligibles
     * (ceux ayant au moins quelques sorties de stock), et c'est le
     * JavaScript de la vue qui interroge le service IA médicament par
     * médicament, en affichant une barre de progression.
     */
    public function index()
    {
        // Seuil minimal de sorties pour qu'une prévision/anomalie ait un sens
        // (cf. forecasting.py : < 14 points -> fallback "moyenne simple, fiabilité faible" ;
        //  cf. anomaly_detection.py : < 5 points -> aucune anomalie calculée).
        $medicaments = Medicament::withCount([
                'mouvements as sorties_count' => function ($q) {
                    $q->where('type', MouvementStock::TYPE_SORTIE);
                },
            ])
            ->having('sorties_count', '>=', 5)
            ->orderBy('nom')
            ->get(['id', 'nom']);

        return view('oncologie.ia.dashboard', compact('medicaments'));
    }
}