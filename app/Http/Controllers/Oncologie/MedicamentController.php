<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Oncologie\Medicament;
use App\Models\Oncologie\MouvementStock;
use App\Services\IaService;                 // <-- AJOUT

class MedicamentController extends Controller
{
    // <-- AJOUT : injection du service dans le constructeur
    public function __construct(protected IaService $ia)
    {
    }

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

    // ========================
    // IA : SCAN VISUEL (CLIP) — CORRIGÉ
    // Appelé depuis create.blade.php : sendImage() / capture()
    // Remplace l'ancien appel direct fetch("http://localhost:8001/scan")
    // ========================
    public function scanEtRemplir(Request $request)
    {
        $request->validate(['file' => 'required|image|max:8192']);

        // AVANT : appel Http:: direct sans import + lecture de 'medicament_id' inexistant
        // APRÈS : passe par IaService, qui centralise l'appel HTTP
        $data = $this->ia->scanMedicament($request->file('file'));

        if (($data['status'] ?? null) !== 'success') {
            return response()->json([
                'status'    => $data['status'] ?? 'error',
                'message'   => $data['message'] ?? 'Aucun médicament reconnu avec certitude.',
                'candidats' => $data['candidats'] ?? [],
            ]);
        }

        // On tente de retrouver le médicament correspondant par son nom détecté
        $medicament = Medicament::where('nom', 'like', '%' . $data['nom_detecte'] . '%')->first();

        return response()->json([
            'status'      => 'success',
            'nom_detecte' => $data['nom_detecte'],
            'confidence'  => $data['confidence'],
            'medicament'  => $medicament, // peut être null si nouveau médicament
        ]);
    }

    // ========================
    // IA : LECTURE CODE-BARRES GS1 (lot + expiration fiables)
    // À appeler depuis lots/create.blade.php et lots/edit.blade.php
    // ========================
    public function scanCodeBarres(Request $request)
    {
        $request->validate(['file' => 'required|image|max:8192']);

        $data = $this->ia->scanCodeBarres($request->file('file'));

        if (($data['trouve'] ?? false) !== true) {
            return response()->json(['status' => 'no_match']);
        }

        return response()->json([
            'status'          => 'success',
            'numero_lot'      => $data['numero_lot'],
            'date_expiration' => $data['date_expiration'],
            'gtin'            => $data['gtin'],
        ]);
    }

    // ========================
    // IA : PRÉVISION DE RUPTURE DE STOCK (Holt-Winters)
    // À appeler depuis medicaments/show.blade.php
    // ========================
    public function previsionStock(Medicament $medicament)
    {
        $historique = $medicament->mouvements()
            ->where('type', MouvementStock::TYPE_SORTIE)
            ->where('date_mouvement', '>=', now()->subDays(60))
            ->selectRaw('DATE(date_mouvement) as jour, SUM(quantite) as total')
            ->groupBy('jour')
            ->orderBy('jour')
            ->get()
            ->map(fn ($m) => ['date' => $m->jour, 'quantite' => (float) $m->total])
            ->toArray();

        $result = $this->ia->previsionStock($historique, $medicament->stockActuel());

        return response()->json($result);
    }

    // ========================
    // IA : DÉTECTION D'ANOMALIES SUR LES SORTIES (z-score)
    // À appeler depuis medicaments/show.blade.php
    // ========================
    public function detecterAnomalies(Medicament $medicament)
    {
        $mouvements = $medicament->mouvements()
            ->where('type', MouvementStock::TYPE_SORTIE)
            ->orderBy('date_mouvement')
            ->get()
            ->map(fn ($m) => [
                'id'       => $m->id,
                'quantite' => (float) $m->quantite,
                'date'     => $m->date_mouvement->toDateString(),
            ])
            ->toArray();

        $result = $this->ia->detecterAnomalies($mouvements);

        return response()->json($result);
    }
}