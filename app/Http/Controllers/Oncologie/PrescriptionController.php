<?php
 
namespace App\Http\Controllers\Oncologie;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Oncologie\Prescription;
use App\Models\Oncologie\Patient;
use App\Models\Oncologie\Protocole;
use App\Models\Oncologie\PrescriptionDetail;
use App\Models\Oncologie\OncoUser;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
 
class PrescriptionController extends Controller
{
    // ========================
    // INDEX
    // ========================
    public function index(Request $request)
{
$query = Prescription::with([
'patient',
'protocole',
'details.medicament'
]);


// Recherche patient
if ($request->filled('patient')) {

    $search = $request->patient;

    $query->whereHas('patient', function ($q) use ($search) {

        $q->where('nom', 'like', "%{$search}%")
          ->orWhere('prenom', 'like', "%{$search}%")
          ->orWhere('numero_dossier', 'like', "%{$search}%");

    });
}

// Statut
if ($request->filled('statut')) {
    $query->where('statut', $request->statut);
}

// Date
if ($request->filled('date')) {
    $query->whereDate(
        'date_prescription',
        $request->date
    );
}

// Risque rénal
if ($request->filled('renal')) {

    switch ($request->renal) {

        case 'normal':
            $query->where('clairance_renale', '>=', 90);
            break;

        case 'modere':
            $query->whereBetween(
                'clairance_renale',
                [30,89]
            );
            break;

        case 'severe':
            $query->where(
                'clairance_renale',
                '<',
                30
            );
            break;
    }
}

$prescriptions = $query
    ->latest()
    ->paginate(20)
    ->withQueryString();

$stats = [

    'total' => Prescription::count(),

    'validees' => Prescription::where(
        'statut',
        'validee'
    )->count(),

    'attente' => Prescription::where(
        'statut',
        'en_attente'
    )->count(),

    'annulees' => Prescription::where(
        'statut',
        'annulee'
    )->count(),

    'risque_renal' => Prescription::where(
        'clairance_renale',
        '<',
        30
    )->count(),

    'cycles' => Prescription::sum(
        'cycle'
    ),

];

$alertes = [];

Prescription::with('patient')
    ->where('clairance_renale','<',30)
    ->take(10)
    ->get()
    ->each(function($p) use (&$alertes){

        $alertes[] =
            "Patient "
            .$p->patient->nom
            ." "
            .$p->patient->prenom
            ." : DFG critique ("
            .$p->clairance_renale
            ." ml/min)";
    });

return view(
    'oncologie.prescriptions.index',
    compact(
        'prescriptions',
        'stats',
        'alertes'
    )
);


}


 
    // ========================
    // CREATE
    // ========================
    public function create()
    {
        $patients = Patient::vivant()
            ->orderBy('nom')
            ->get();
 
        $protocoles = Protocole::orderBy('nom')->get();
 
        $medecins = OncoUser::where('role', 'medecin')
            ->orderBy('name')
            ->get();
 
        return view(
            'oncologie.prescriptions.create',
            compact('patients', 'protocoles', 'medecins')
        );
    }
 
    // ========================
    // STORE
    // ========================
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'         => 'required|exists:oncologie.patients,id',
            'protocole_id'       => 'required|exists:oncologie.protocoles,id',
            'medecin_nom'        => 'required|string',
            'date_prescription'  => 'required|date',
            'poids'              => 'required|numeric|min:1|max:300',
            'taille'             => 'required|numeric|min:50|max:250',
            'creatinine'         => 'nullable|numeric|min:0.1|max:30',
            'surface_corporelle' => 'nullable|numeric',
            'clairance_renale'   => 'nullable|numeric',
            'methode_dfg'        => 'nullable|in:cockcroft,ckdepi',
        ]);
 
        $prescription = Prescription::create([
            'patient_id'         => $request->patient_id,
            'protocole_id'       => $request->protocole_id,
            'medecin_nom'        => $request->medecin_nom,
            'date_prescription'  => $request->date_prescription,
            'statut'             => 'en_attente',
            'poids'              => $request->poids,
            'taille'             => $request->taille,
            'creatinine'         => $request->creatinine,
            'surface_corporelle' => $request->surface_corporelle,
            'clairance_renale'   => $request->clairance_renale,
            'methode_dfg'        => $request->methode_dfg ?? 'cockcroft',
            'notes_cliniques'    => $request->notes_cliniques,
        ]);
 
        // Enregistrer les détails (médicaments + doses calculées)
        if ($request->has('medicaments')) {
            foreach ($request->medicaments as $medId => $data) {
                PrescriptionDetail::create([
                    'prescription_id'  => $prescription->id,
                    'medicament_id'    => $medId,
                    'dose_calculee'    => $data['dose'] ?? 0,
                    'methode_calcul'   => $data['methode'] ?? 'standard',
                    'formule_detaillee'=> $data['formule'] ?? null,
                ]);
            }
        }
 
        return redirect()->route('oncologie.prescriptions.show', $prescription)
            ->with('success', '✅ Prescription créée avec succès.');
    }
 
    // ========================
    // SHOW
    // ========================
    public function show(Prescription $prescription)
    {
        $prescription->load([
            'patient', 'protocole',
            'details.medicament',
            'dispensations.medicament',
            'dispensations.lot',
        ]);
        return view('oncologie.prescriptions.show', compact('prescription'));
    }
 
    // ========================
    // EDIT / UPDATE
    // ========================
    public function edit(Prescription $prescription)
    {
        if ($prescription->isValidee()) {
            return back()->with('error', '❌ Impossible de modifier une prescription validée.');
        }
 
        $patients   = Patient::vivant()->orderBy('nom')->get();
        $protocoles = Protocole::orderBy('nom')->get();
        $medecins   = OncoUser::where('role', 'medecin')->orderBy('name')->get();
 
        return view('oncologie.prescriptions.edit', compact(
            'prescription', 'patients', 'protocoles', 'medecins'
        ));
    }
 
    public function update(Request $request, Prescription $prescription)
    {
        if ($prescription->isValidee()) {
            return back()->with('error', '❌ Prescription déjà validée.');
        }
 
        $request->validate([
            'patient_id'         => 'required|exists:oncologie.patients,id',
            'protocole_id'       => 'required|exists:oncologie.protocoles,id',
            'medecin_nom'        => 'required|string',
            'date_prescription'  => 'required|date',
            'poids'              => 'required|numeric|min:1|max:300',
            'taille'             => 'required|numeric|min:50|max:250',
            'creatinine'         => 'nullable|numeric|min:0.1|max:30',
            'surface_corporelle' => 'nullable|numeric',
            'clairance_renale'   => 'nullable|numeric',
            'methode_dfg'        => 'nullable|in:cockcroft,ckdepi',
        ]);
 
        $prescription->update($request->only(
            'patient_id', 'protocole_id', 'medecin_nom', 'date_prescription',
            'poids', 'taille', 'creatinine', 'surface_corporelle',
            'clairance_renale', 'methode_dfg', 'notes_cliniques'
        ));
 
        if ($request->has('medicaments')) {
            $prescription->details()->delete();
            foreach ($request->medicaments as $medId => $data) {
                PrescriptionDetail::create([
                    'prescription_id'  => $prescription->id,
                    'medicament_id'    => $medId,
                    'dose_calculee'    => $data['dose'] ?? 0,
                    'methode_calcul'   => $data['methode'] ?? 'standard',
                    'formule_detaillee'=> $data['formule'] ?? null,
                ]);
            }
        }
 
        return redirect()->route('oncologie.prescriptions.show', $prescription)
            ->with('success', '✅ Prescription mise à jour.');
    }
 
    public function destroy(Prescription $prescription)
    {
        if ($prescription->isValidee()) {
            return back()->with('error', '❌ Impossible de supprimer une prescription validée.');
        }
        $prescription->delete();
        return redirect()->route('oncologie.prescriptions.index')
            ->with('success', '✅ Prescription supprimée.');
    }
 
    // ========================
    // VALIDER
    // ========================
    public function valider(Prescription $prescription)
    {
        if ($prescription->isValidee()) {
            return back()->with('warning', '⚠️ Déjà validée.');
        }
 
        $prescription->update(['statut' => 'validee']);
 
        return back()->with('success', '✅ Prescription validée avec succès.');
    }
 
    // ========================
    // CALCULS CÔTÉ SERVEUR (API JSON)
    // ========================
 
    /**
     * Calcul Surface Corporelle — Mosteller
     * SC = √( Taille(cm) × Poids(kg) / 3600 )
     */
    public static function calculSC(float $taille, float $poids): float
    {
        if ($taille <= 0 || $poids <= 0) return 0.0;
        return round(sqrt(($taille * $poids) / 3600), 2);
    }
 
    /**
     * Clairance créatinine — Cockcroft-Gault
     * Homme : (140 - Age) × Poids / (72 × Créatinine)
     * Femme  : × 0.85
     * Créatinine en mg/dL
     */
    public static function calculCockcroft(
        float $age,
        float $poids,
        float $creatinine,
        string $sexe
    ): float {
        if ($creatinine <= 0 || $age <= 0 || $poids <= 0) return 0.0;
        $clcr = ((140 - $age) * $poids) / (72 * $creatinine);
        if (strtolower($sexe[0] ?? '') === 'f') {
            $clcr *= 0.85;
        }
        return round($clcr, 2);
    }
 
    /**
     * DFG estimé — CKD-EPI (2021, sans ethnie)
     * Créatinine en mg/dL
     *
     * Femme  : κ=0.7, α=−0.241   si Scr≤κ : 142 × (Scr/κ)^α × 0.9938^Age × 1.012
     *                              si Scr>κ  : 142 × (Scr/κ)^−1.200 × 0.9938^Age × 1.012
     * Homme  : κ=0.9, α=−0.302   si Scr≤κ : 142 × (Scr/κ)^α × 0.9938^Age
     *                              si Scr>κ  : 142 × (Scr/κ)^−1.200 × 0.9938^Age
     */
    public static function calculCKDEPI(
        float $age,
        float $creatinine,
        string $sexe
    ): float {
        if ($creatinine <= 0 || $age <= 0) return 0.0;
 
        $isFemme = strtolower($sexe[0] ?? '') === 'f';
        $kappa   = $isFemme ? 0.7  : 0.9;
        $alpha   = $isFemme ? -0.241 : -0.302;
        $ratio   = $creatinine / $kappa;
 
        if ($ratio <= 1) {
            $eGFR = 142 * pow($ratio, $alpha) * pow(0.9938, $age);
        } else {
            $eGFR = 142 * pow($ratio, -1.200) * pow(0.9938, $age);
        }
 
        if ($isFemme) $eGFR *= 1.012;
 
        return round($eGFR, 2);
    }
 
    /**
     * Dose Carboplatine — Calvert
     * Dose = AUC × (DFG + 25)
     */
    public static function calculCalvert(float $auc, float $dfg): float
    {
        return round($auc * ($dfg + 25), 2);
    }
 
    /**
     * Endpoint AJAX : calcule SC + DFG et renvoie JSON
     */
    public function calculerParametres(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'poids'      => 'required|numeric|min:1|max:300',
            'taille'     => 'required|numeric|min:50|max:250',
            'age'        => 'required|numeric|min:0|max:120',
            'sexe'       => 'required|string',
            'creatinine' => 'nullable|numeric|min:0.1|max:30',
            'methode'    => 'nullable|in:cockcroft,ckdepi',
        ]);
 
        $poids      = (float) $request->poids;
        $taille     = (float) $request->taille;
        $age        = (float) $request->age;
        $sexe       = $request->sexe;
        $creatinine = (float) ($request->creatinine ?? 0);
        $methode    = $request->methode ?? 'cockcroft';
 
        $sc = self::calculSC($taille, $poids);
 
        $cockcroft = 0;
        $ckdepi    = 0;
        $alertes   = [];
 
        if ($creatinine > 0) {
            $cockcroft = self::calculCockcroft($age, $poids, $creatinine, $sexe);
            $ckdepi    = self::calculCKDEPI($age, $creatinine, $sexe);
        }
 
        $dfg = $methode === 'ckdepi' ? $ckdepi : $cockcroft;
 
        // Alertes cliniques
        if (!$poids)      $alertes[] = ['niveau' => 'warning', 'msg' => 'Poids non renseigné'];
        if (!$taille)     $alertes[] = ['niveau' => 'warning', 'msg' => 'Taille non renseignée'];
        if (!$creatinine) $alertes[] = ['niveau' => 'info',    'msg' => 'Créatinine non renseignée — clairance non calculée'];
 
        if ($dfg > 0) {
            if ($dfg < 15)  $alertes[] = ['niveau' => 'danger',  'msg' => 'Insuffisance rénale terminale (DFG < 15 ml/min) — Contre-indication à de nombreux cytotoxiques'];
            elseif ($dfg < 30) $alertes[] = ['niveau' => 'danger',  'msg' => 'Insuffisance rénale sévère (DFG < 30 ml/min) — Adaptation de dose requise'];
            elseif ($dfg < 60) $alertes[] = ['niveau' => 'warning', 'msg' => 'Insuffisance rénale modérée (DFG < 60 ml/min) — Surveillance renforcée'];
        }
 
        if ($sc > 0 && $sc < 1.2) $alertes[] = ['niveau' => 'info', 'msg' => 'Petite surface corporelle (SC < 1,2 m²) — Vérifier plafonnement des doses'];
        if ($sc > 2.2)             $alertes[] = ['niveau' => 'info', 'msg' => 'Grande surface corporelle (SC > 2,2 m²) — Certains protocoles plafonnent à 2,0 m²'];
 
        return response()->json([
            'sc'        => $sc,
            'cockcroft' => $cockcroft,
            'ckdepi'    => $ckdepi,
            'dfg'       => $dfg,
            'alertes'   => $alertes,
        ]);
    }
 
    // ========================
    // PDF SINGLE
    // ========================
    public function pdf(Prescription $prescription)
    {
        try {
            $prescription->load([
                'patient',
                'protocole',
                'details.medicament',
                'dispensations.lot',
            ]);
 
            $pdf = Pdf::loadView('oncologie.prescriptions.pdf', compact('prescription'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'DejaVu Sans',
                ]);
 
            $filename = 'prescription_' . $prescription->id . '_' . date('Ymd') . '.pdf';
            return $pdf->download($filename);
 
        } catch (\Exception $e) {
            return back()->with('error', '❌ Erreur PDF : ' . $e->getMessage());
        }
    }
 
    // ========================
    // EXPORT (PDF ou CSV)
    // ========================
    public function export(Request $request)
    {
        $query = Prescription::with(['patient', 'protocole', 'details.medicament']);
 
        if ($request->filled('statut')) $query->where('statut', $request->statut);
        if ($request->filled('date'))   $query->whereDate('date_prescription', $request->date);
 
        $prescriptions = $query->latest()->get();
        $format        = $request->get('format', 'pdf');
 
        if ($format === 'pdf') {
            try {
                $pdf = Pdf::loadView('oncologie.prescriptions.export', compact('prescriptions'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isRemoteEnabled'      => false,
                        'defaultFont'          => 'DejaVu Sans',
                    ]);
                return $pdf->download('prescriptions_' . date('Ymd') . '.pdf');
            } catch (\Exception $e) {
                return back()->with('error', '❌ Erreur PDF : ' . $e->getMessage());
            }
        }
 
        $filename = 'prescriptions_' . date('Ymd') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
 
        $callback = function () use ($prescriptions) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID','Patient','Médecin','Protocole','Date','Statut','SC (m²)','DFG','Médicaments'], ';');
 
            foreach ($prescriptions as $p) {
                fputcsv($file, [
                    $p->id,
                    optional($p->patient)->nom . ' ' . optional($p->patient)->prenom,
                    $p->medecin_nom ?? '-',
                    optional($p->protocole)->nom ?? '-',
                    optional($p->date_prescription)?->format('d/m/Y'),
                    $p->statut,
                    $p->surface_corporelle ?? '-',
                    $p->clairance_renale ?? '-',
                    $p->medicaments_noms ?? '-',
                ], ';');
            }
            fclose($file);
        };
 
        return response()->stream($callback, 200, $headers);
    }
 
    // ========================
    // STATISTIQUES
    // ========================
    public function stats()
    {
        try {
            $total     = Prescription::count();
            $validees  = Prescription::where('statut', 'validee')->count();
            $enAttente = Prescription::where('statut', 'en_attente')->count();
            $annulees  = Prescription::where('statut', 'annulee')->count();
 
            $parMois = Prescription::selectRaw(
                'MONTH(date_prescription) as mois,
                 COUNT(*) as total,
                 SUM(CASE WHEN statut="validee" THEN 1 ELSE 0 END) as nb_validees,
                 SUM(CASE WHEN statut="annulee" THEN 1 ELSE 0 END) as nb_annulees'
            )
            ->whereYear('date_prescription', now()->year)
            ->groupBy('mois')
            ->orderBy('mois')
            ->get()
            ->keyBy('mois');
 
            $moisLabels   = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
            $totauxMois   = [];
            $valideesMois = [];
            $annuleesMois = [];
 
            for ($m = 1; $m <= 12; $m++) {
                $totauxMois[]   = $parMois->get($m)?->total       ?? 0;
                $valideesMois[] = $parMois->get($m)?->nb_validees ?? 0;
                $annuleesMois[] = $parMois->get($m)?->nb_annulees ?? 0;
            }
 
            $topMedecins = Prescription::select('medecin_nom')
                ->selectRaw('COUNT(*) as total')
                ->whereNotNull('medecin_nom')
                ->groupBy('medecin_nom')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
 
            $topProtocoles = Prescription::select('protocole_id')
                ->selectRaw('COUNT(*) as total')
                ->with('protocole')
                ->groupBy('protocole_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
 
            return view('oncologie.prescriptions.stats', compact(
                'total', 'validees', 'enAttente', 'annulees',
                'moisLabels', 'totauxMois', 'valideesMois', 'annuleesMois',
                'topMedecins', 'topProtocoles'
            ));
 
        } catch (\Exception $e) {
            return back()->with('error', '❌ Erreur statistiques : ' . $e->getMessage());
        }
    }
 
}