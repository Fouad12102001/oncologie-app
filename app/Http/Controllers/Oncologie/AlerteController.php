<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Oncologie\AlerteDismissal;

class AlerteController extends Controller
{
    /**
     * Supprime (masque) une alerte pour l'utilisateur.
     * Elle réapparaîtra automatiquement si la situation redevient
     * critique après être passée par un état résolu (voir listeAlertes()).
     */
    public function dismiss(Request $request)
    {
        $request->validate([
            'medicament_id' => 'required|integer|exists:oncologie.medicaments,id',
            'type'          => 'required|string|in:rupture,stock,expire,bientot',
        ]);

        AlerteDismissal::updateOrCreate(
            ['medicament_id' => $request->medicament_id, 'type' => $request->type],
            ['dismissed_at' => now()]
        );

        return response()->json(['status' => 'success']);
    }
}