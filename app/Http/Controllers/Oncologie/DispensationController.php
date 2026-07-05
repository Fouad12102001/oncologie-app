<?php
 
namespace App\Http\Controllers\Oncologie;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Oncologie\Dispensation;
use App\Models\Oncologie\Prescription;
use App\Models\Oncologie\Medicament;
use App\Models\Oncologie\Lot;
use App\Models\Oncologie\MouvementStock;
use Barryvdh\DomPDF\Facade\Pdf;
 
class DispensationController extends Controller
{
    // ════════════════════════════════════════
    // INDEX
    // ════════════════════════════════════════
    public function index(Request $request)
    {
        $query = Dispensation::with([
            'prescription.patient',
            'medicament',
            'lot',
            'user',
        ])->recent();
 
        // Filtres
        if ($request->filled('patient')) {
            $s = $request->patient;
            $query->whereHas('prescription.patient', fn($q) =>
                $q->where('nom', 'like', "%$s%")
                  ->orWhere('prenom', 'like', "%$s%")
                  ->orWhere('numero_dossier', 'like', "%$s%")
            );
        }
 
        if ($request->filled('medicament_id')) {
            $query->where('medicament_id', $request->medicament_id);
        }
 
        if ($request->filled('date_debut')) {
            $query->whereDate('date_dispensation', '>=', $request->date_debut);
        }
 
        if ($request->filled('date_fin')) {
            $query->whereDate('date_dispensation', '<=', $request->date_fin);
        }
 
        $dispensations = $query->paginate(20)->withQueryString();
 
        // Stats rapides pour le header
        $totalDispensations   = Dispensation::count();
        $dispensationsAujourd = Dispensation::whereDate('date_dispensation', today())->count();
        $dispensationsMois    = Dispensation::whereMonth('date_dispensation', now()->month)
            ->whereYear('date_dispensation', now()->year)
            ->count();
        $quantiteTotaleMois   = Dispensation::whereMonth('date_dispensation', now()->month)
            ->whereYear('date_dispensation', now()->year)
            ->sum('quantite');
 
        // Liste médicaments pour filtre
        $medicamentsFiltres = Medicament::orderBy('nom')->get(['id', 'nom']);
 
        return view('oncologie.dispensations.index', compact(
            'dispensations',
            'totalDispensations', 'dispensationsAujourd',
            'dispensationsMois', 'quantiteTotaleMois',
            'medicamentsFiltres'
        ));
    }
 
    // ════════════════════════════════════════
    // CREATE
    // ════════════════════════════════════════
    public function create()
    {
        $prescriptions = Prescription::with('patient')
            ->where('statut', 'validee')
            ->latest()
            ->get();
 
        $medicaments = Medicament::with('mouvements')->get()
            ->filter(fn($m) => !$m->estExpire() && $m->stockActuel() > 0)
            ->values();
 
        return view('oncologie.dispensations.create', compact('prescriptions', 'medicaments'));
    }
 
    // ════════════════════════════════════════
    // STORE — FIFO
    // ════════════════════════════════════════
    public function store(Request $request)
    {
        $request->validate([
            'prescription_id' => 'required|exists:oncologie.prescriptions,id',
            'medicament_id'   => 'required|exists:oncologie.medicaments,id',
            'quantite'        => 'required|integer|min:1',
            'notes'           => 'nullable|string|max:500',
        ]);
 
        $medicament = Medicament::findOrFail($request->medicament_id);
        $quantite   = (int) $request->quantite;
 
        // Vérification stock global
        if ($medicament->stockActuel() < $quantite) {
            return back()
                ->withInput()
                ->with('error', '❌ Stock insuffisant — disponible : ' . $medicament->stockActuel() . ' unités.');
        }
 
        // FIFO : lot le plus ancien non expiré avec stock suffisant
        $lot = Lot::where('medicament_id', $request->medicament_id)
            ->whereDate('date_expiration', '>', now())
            ->orderBy('date_fabrication', 'asc')
            ->get()
            ->first(fn($l) => $l->stockRestant() >= $quantite);
 
        if (!$lot) {
            // Essai avec plusieurs lots (FIFO partiel non supporté ici → remonter l'erreur)
            return back()
                ->withInput()
                ->with('error', '❌ Aucun lot valide disponible pour cette quantité en FIFO. Vérifiez les dates d\'expiration.');
        }
 
        try {
            DB::connection('oncologie')->transaction(function () use ($request, $lot, $quantite) {
                // Enregistrer la dispensation
                Dispensation::create([
                    'prescription_id'   => $request->prescription_id,
                    'medicament_id'     => $request->medicament_id,
                    'lot_id'            => $lot->id,
                    'quantite'          => $quantite,
                    'date_dispensation' => now(),
                    'user_id'           => auth()->id(),
                    'notes'             => $request->notes,
                ]);
 
                // Mouvement de sortie
                MouvementStock::create([
                    'medicament_id'  => $request->medicament_id,
                    'type'           => MouvementStock::TYPE_SORTIE,
                    'quantite'       => $quantite,
                    'date_mouvement' => now(),
                    'lot_id'         => $lot->id,
                    'motif'          => 'Dispensation prescription #' . $request->prescription_id,
                ]);
            });
 
            return redirect()->route('oncologie.dispensations.index')
                ->with('success', '✅ Dispensation FIFO effectuée — Lot ' . $lot->numero . ' · ' . $quantite . ' unités délivrées.');
 
        } catch (\Exception $e) {
            return back()->withInput()->with('error', '❌ Erreur transaction : ' . $e->getMessage());
        }
    }
 
    // ════════════════════════════════════════
    // SHOW
    // ════════════════════════════════════════
    public function show(Dispensation $dispensation)
    {
        $dispensation->load(['prescription.patient', 'prescription.protocole', 'medicament', 'lot', 'user']);
        return view('oncologie.dispensations.show', compact('dispensation'));
    }
 
    // ════════════════════════════════════════
    // EXPORT PDF / CSV
    // ════════════════════════════════════════
    public function export(Request $request)
    {
        $query = Dispensation::with(['prescription.patient', 'medicament', 'lot'])->recent();
 
        if ($request->filled('date_debut')) $query->whereDate('date_dispensation', '>=', $request->date_debut);
        if ($request->filled('date_fin'))   $query->whereDate('date_dispensation', '<=', $request->date_fin);
        if ($request->filled('medicament_id')) $query->where('medicament_id', $request->medicament_id);
 
        $dispensations = $query->get();
        $format        = $request->get('format', 'pdf');
 
        if ($format === 'pdf') {
            try {
                $pdf = Pdf::loadView('oncologie.dispensations.export', compact('dispensations'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans']);
 
                return $pdf->download('dispensations_' . date('Ymd') . '.pdf');
            } catch (\Exception $e) {
                return back()->with('error', '❌ Erreur PDF : ' . $e->getMessage());
            }
        }
 
        // CSV
        $filename = 'dispensations_' . date('Ymd') . '.csv';
        $headers  = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="' . $filename . '"'];
 
        $callback = function () use ($dispensations) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Patient', 'Médicament', 'Lot', 'Quantité', 'Date', 'Pharmacien'], ';');
 
            foreach ($dispensations as $d) {
                fputcsv($file, [
                    $d->id,
                    $d->patient_nom_complet,
                    optional($d->medicament)->nom ?? '-',
                    optional($d->lot)->numero ?? '-',
                    $d->quantite,
                    $d->date_formattee ?? '-',
                    optional($d->user)->name ?? '-',
                ], ';');
            }
 
            fclose($file);
        };
 
        return response()->stream($callback, 200, $headers);
    }
 
    // ════════════════════════════════════════
    // STATISTIQUES
    // ════════════════════════════════════════
    public function stats()
    {
        try {
            $total = Dispensation::count();
 
            // Par mois (année en cours)
            $parMois = Dispensation::selectRaw(
                'MONTH(date_dispensation) as mois,
                 COUNT(*) as nb_dispensations,
                 SUM(quantite) as quantite_totale'
            )
            ->whereYear('date_dispensation', now()->year)
            ->groupBy('mois')
            ->orderBy('mois')
            ->get()
            ->keyBy('mois');
 
            $moisLabels       = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
            $dispensationsMois = [];
            $quantitesMois    = [];
 
            for ($m = 1; $m <= 12; $m++) {
                $dispensationsMois[] = $parMois->get($m)?->nb_dispensations ?? 0;
                $quantitesMois[]     = $parMois->get($m)?->quantite_totale  ?? 0;
            }
 
            // Top médicaments
            $topMedicaments = Dispensation::select('medicament_id')
                ->selectRaw('COUNT(*) as nb, SUM(quantite) as qte_totale')
                ->with('medicament')
                ->groupBy('medicament_id')
                ->orderByDesc('nb')
                ->limit(5)
                ->get();
 
            return view('oncologie.dispensations.stats', compact(
                'total', 'moisLabels', 'dispensationsMois', 'quantitesMois', 'topMedicaments'
            ));
 
        } catch (\Exception $e) {
            return back()->with('error', '❌ Erreur statistiques : ' . $e->getMessage());
        }
    }
}