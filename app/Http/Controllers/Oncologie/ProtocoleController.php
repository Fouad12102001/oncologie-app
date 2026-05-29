<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use App\Models\Oncologie\Protocole;

class ProtocoleController extends Controller
{
    /**
     * AJAX : Retourner les médicaments d'un protocole avec doses
     */
    public function medicaments(Protocole $protocole)
    {
        $medicaments = $protocole->medicaments()
            ->get()
            ->map(fn($med) => [
                'id'            => $med->id,
                'nom'           => $med->nom,
                'dose_standard' => $med->pivot->dose_standard,
                'type_calcul'   => $med->pivot->type_calcul,
                'ordre'         => $med->pivot->ordre,
            ]);

        return response()->json($medicaments);
    }
}
