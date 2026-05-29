<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Oncologie\Medicament;
use App\Models\Oncologie\MouvementStock;

class MedicamentController extends Controller
{
    // ========================
    // INDEX + FILTRES
    // ========================
    public function index(Request $request)
    {
        $query = Medicament::with('mouvements');

        if ($request->filled('medicament')) {
            $query->where('nom', 'like', '%' . $request->medicament . '%');
        }

        $medicaments = $query->get();
        $statut = $request->get('statut');

        if ($statut === 'expired') {
            $medicaments = $medicaments->filter(fn($m) => $m->estExpire());
        } elseif ($statut === 'soon') {
            $medicaments = $medicaments->filter(fn($m) => $m->bientotExpire() && !$m->estExpire());
        } elseif ($statut === 'rupture') {
            $medicaments = $medicaments->filter(fn($m) => $m->stockActuel() <= 0);
        } elseif ($statut === 'stock') {
            $medicaments = $medicaments->filter(fn($m) =>
                $m->stockActuel() > 0 && $m->stockActuel() <= $m->quantite_min
            );
        }

        return view('oncologie.medicaments.index', compact('medicaments', 'statut'));
    }

    // ========================
    // CREATE / STORE
    // ========================
    public function create()
    {
        return view('oncologie.medicaments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'               => 'required|string|max:255',
            'quantite_min'      => 'required|integer|min:0',
            'quantite_initiale' => 'required|integer|min:0',
            'date_fabrication'  => 'nullable|date',
            'date_expiration'   => 'nullable|date|after_or_equal:date_fabrication',
        ]);

        $medicament = Medicament::create($request->only([
            'nom', 'quantite_min', 'quantite_initiale',
            'date_fabrication', 'date_expiration',
        ]));

        // Stock initial = entrée automatique
        if ($request->quantite_initiale > 0) {
            MouvementStock::create([
                'medicament_id'  => $medicament->id,
                'type'           => MouvementStock::TYPE_ENTREE,
                'quantite'       => $request->quantite_initiale,
                'date_mouvement' => now(),
            ]);
        }

        return redirect()->route('oncologie.medicaments.index')
            ->with('success', '✅ Médicament ajouté avec succès.');
    }

    // ========================
    // SHOW
    // ========================
    public function show(Medicament $medicament)
    {
        $medicament->load('mouvements', 'lots');
        return view('oncologie.medicaments.show', compact('medicament'));
    }

    // ========================
    // EDIT / UPDATE
    // ========================
    public function edit(Medicament $medicament)
    {
        return view('oncologie.medicaments.edit', compact('medicament'));
    }

    public function update(Request $request, Medicament $medicament)
    {
        $request->validate([
            'nom'              => 'required|string|max:255|unique:medicaments,nom,' . $medicament->id,
            'quantite_min'     => 'required|integer|min:0',
            'date_fabrication' => 'nullable|date',
            'date_expiration'  => 'nullable|date',
        ]);

        $medicament->update($request->only([
            'nom', 'quantite_min', 'date_fabrication', 'date_expiration',
        ]));

        return redirect()->route('oncologie.medicaments.index')
            ->with('success', '✅ Médicament mis à jour.');
    }

    // ========================
    // DESTROY
    // ========================
    public function destroy(Medicament $medicament)
    {
        // Vérifier si des lots existent
        if ($medicament->lots()->count() > 0) {
            return back()->with('error', '❌ Impossible : ce médicament possède des lots associés.');
        }

        $medicament->delete();
        return back()->with('success', '✅ Médicament supprimé.');
    }

    // ========================
    // ENTRÉE / SORTIE STOCK
    // ========================
    public function entree(Request $request, Medicament $medicament)
    {
        $request->validate(['quantite' => 'required|integer|min:1']);

        MouvementStock::create([
            'medicament_id'  => $medicament->id,
            'type'           => MouvementStock::TYPE_ENTREE,
            'quantite'       => $request->quantite,
            'date_mouvement' => now(),
        ]);

        return back()->with('success', '✅ Entrée de stock enregistrée.');
    }

    public function sortie(Request $request, Medicament $medicament)
    {
        $request->validate(['quantite' => 'required|integer|min:1']);

        if ($request->quantite > $medicament->stockActuel()) {
            return back()->with('error', '❌ Stock insuffisant.');
        }

        MouvementStock::create([
            'medicament_id'  => $medicament->id,
            'type'           => MouvementStock::TYPE_SORTIE,
            'quantite'       => $request->quantite,
            'date_mouvement' => now(),
        ]);

        return back()->with('success', '✅ Sortie de stock enregistrée.');
    }

    // ========================
    // LOTS DU MÉDICAMENT
    // ========================
    public function lots(Medicament $medicament)
    {
        $medicament->load('lots');
        return view('oncologie.medicaments.lots', compact('medicament'));
    }
}