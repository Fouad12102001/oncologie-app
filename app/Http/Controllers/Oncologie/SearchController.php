<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Oncologie\Patient;
use App\Models\Oncologie\Medicament;
use App\Models\Oncologie\Prescription;
use App\Models\Oncologie\Lot;

class SearchController extends Controller
{
    /**
     * Recherche globale (patients, médicaments, prescriptions, lots).
     * Appelée en AJAX depuis la barre de recherche du topbar (Ctrl+K).
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '' || mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // Patients
        if (class_exists(Patient::class)) {
            Patient::where('nom', 'like', "%{$q}%")
                ->orWhere('prenom', 'like', "%{$q}%")
                ->limit(5)
                ->get()
                ->each(function ($p) use (&$results) {
                    $results[] = [
                        'icon'  => '👤',
                        'label' => trim(($p->nom ?? '') . ' ' . ($p->prenom ?? '')),
                        'sub'   => 'Patient',
                        'url'   => route('oncologie.patients.show', $p->id),
                    ];
                });
        }

        // Médicaments
        Medicament::where('nom', 'like', "%{$q}%")
            ->limit(5)
            ->get()
            ->each(function ($m) use (&$results) {
                $results[] = [
                    'icon'  => '💊',
                    'label' => $m->nom,
                    'sub'   => 'Médicament — stock ' . $m->stockActuel(),
                    'url'   => route('oncologie.medicaments.show', $m->id),
                ];
            });

        // Lots
        Lot::with('medicament')->where('numero', 'like', "%{$q}%")
            ->limit(5)
            ->get()
            ->each(function ($l) use (&$results) {
                $results[] = [
                    'icon'  => '📦',
                    'label' => "Lot #{$l->numero}",
                    'sub'   => optional($l->medicament)->nom ?? 'Médicament inconnu',
                    'url'   => route('oncologie.lots.show', $l->id),
                ];
            });

        // Prescriptions (si le modèle existe avec ces relations)
        if (class_exists(Prescription::class)) {
            Prescription::with('patient')
                ->whereHas('patient', fn($qr) =>
                    $qr->where('nom', 'like', "%{$q}%")->orWhere('prenom', 'like', "%{$q}%")
                )
                ->limit(5)
                ->get()
                ->each(function ($presc) use (&$results) {
                    $results[] = [
                        'icon'  => '📋',
                        'label' => 'Prescription — ' . optional($presc->patient)->nom,
                        'sub'   => $presc->statut ?? '',
                        'url'   => route('oncologie.prescriptions.show', $presc->id),
                    ];
                });
        }

        return response()->json(['results' => $results]);
    }
}