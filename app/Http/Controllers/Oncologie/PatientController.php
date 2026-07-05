<?php
 
namespace App\Http\Controllers\Oncologie;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Oncologie\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
 
class PatientController extends Controller
{
    // ════════════════════════════════════════
    // INDEX avec statistiques inline
    // ════════════════════════════════════════
    public function index(Request $request)
    {
        $query = Patient::query();
 
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('nom',    'like', "%$s%")
                  ->orWhere('prenom','like', "%$s%")
                  ->orWhere('numero_dossier','like', "%$s%")
            );
        }
 
        if ($request->filled('numero_dossier')) {
            $query->where('numero_dossier', 'like', '%' . $request->numero_dossier . '%');
        }
 
        if ($request->filled('sexe')) {
            $query->where('sexe', $request->sexe);
        }
 
        if ($request->filled('statut_vital')) {
            $query->where('statut_vital', $request->statut_vital);
        }
 
        if ($request->filled('type_cancer')) {
            $query->where('type_cancer', $request->type_cancer);
        }
 
        if ($request->filled('wilaya')) {
            $query->where('wilaya', $request->wilaya);
        }
 
        $patients      = $query->latest()->paginate(15)->withQueryString();
        $totalPatients = Patient::count();
        $vivants       = Patient::vivant()->count();
        $decedes       = Patient::decede()->count();
        $masculin      = Patient::where('sexe', 'Masculin')->count();
        $feminin       = Patient::where('sexe', 'Féminin')->count();
 
        // Statistiques pour les graphiques du modal
        $parCancer = Patient::select('type_cancer')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('type_cancer')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
 
        $parWilaya = Patient::select('wilaya')
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('wilaya')
            ->groupBy('wilaya')
            ->orderByDesc('total')
            ->limit(6)
            ->get();
 
        // Pour les filtres déroulants
        $typesCancerFilter = Patient::select('type_cancer')
            ->distinct()
            ->whereNotNull('type_cancer')
            ->pluck('type_cancer')
            ->sort()
            ->values();
 
        $wilayasFilter = Patient::select('wilaya')
            ->distinct()
            ->whereNotNull('wilaya')
            ->pluck('wilaya')
            ->sort()
            ->values();
 
        return view('oncologie.patients.index', compact(
            'patients', 'totalPatients', 'vivants', 'decedes',
            'masculin', 'feminin', 'parCancer', 'parWilaya',
            'typesCancerFilter', 'wilayasFilter'
        ));
    }
 
    // ════════════════════════════════════════
    // CREATE
    // ════════════════════════════════════════
    public function create()
    {
        $sexes        = ['Masculin', 'Féminin'];
        $types_cancer = $this->typesCancer();
        $statuts      = [Patient::VIVANT, Patient::DECEDE];
 
        return view('oncologie.patients.create', compact('sexes', 'types_cancer', 'statuts'));
    }
 
    // ════════════════════════════════════════
    // STORE
    // ════════════════════════════════════════
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'numero_dossier' => 'required|string|unique:oncologie.patients,numero_dossier',
            'sexe'           => 'required|in:Masculin,Féminin',
            'date_naissance' => 'required|date|before:today',
            'wilaya'         => 'required|string',
            'type_cancer'    => 'required|string',
            'statut_vital'   => 'required|in:vivant,decede',
            'date_deces'     => 'nullable|date|required_if:statut_vital,decede',
            'poids'          => 'nullable|numeric|min:1|max:300',
            'taille'         => 'nullable|numeric|min:50|max:250',
            'creatinine'     => 'nullable|numeric|min:0.1|max:30',
            'telephone'      => 'nullable|string|max:20',
            'allergies'      => 'nullable|string|max:500',
            'groupe_sanguin' => 'nullable|string',
            'stade_cancer'   => 'nullable|string',
            'antecedents'    => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);
 
        $patient = Patient::create($validated);
 
        return redirect()->route('oncologie.patients.show', $patient)
            ->with('success', '✅ Patient créé avec succès.');
    }
 
    // ════════════════════════════════════════
    // SHOW
    // ════════════════════════════════════════
    public function show(Patient $patient)
    {
        $patient->load('prescriptions.protocole');
 
        // Historique prescriptions récentes
        $prescriptionsRecentes = $patient->prescriptions()
            ->with('protocole')
            ->latest('date_prescription')
            ->limit(5)
            ->get();
 
        return view('oncologie.patients.show', compact('patient', 'prescriptionsRecentes'));
    }
 
    // ════════════════════════════════════════
    // EDIT
    // ════════════════════════════════════════
    public function edit(Patient $patient)
    {
        $sexes        = ['Masculin', 'Féminin'];
        $types_cancer = $this->typesCancer();
        $statuts      = [Patient::VIVANT, Patient::DECEDE];
 
        return view('oncologie.patients.edit', compact('patient', 'sexes', 'types_cancer', 'statuts'));
    }
 
    // ════════════════════════════════════════
    // UPDATE
    // ════════════════════════════════════════
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'numero_dossier' => 'required|string|unique:oncologie.patients,numero_dossier,' . $patient->id,
            'sexe'           => 'required|in:Masculin,Féminin',
            'date_naissance' => 'required|date|before:today',
            'wilaya'         => 'required|string',
            'type_cancer'    => 'required|string',
            'statut_vital'   => 'required|in:vivant,decede',
            'date_deces'     => 'nullable|date|required_if:statut_vital,decede',
            'poids'          => 'nullable|numeric|min:1|max:300',
            'taille'         => 'nullable|numeric|min:50|max:250',
            'creatinine'     => 'nullable|numeric|min:0.1|max:30',
            'telephone'      => 'nullable|string|max:20',
            'allergies'      => 'nullable|string|max:500',
            'groupe_sanguin' => 'nullable|string',
            'stade_cancer'   => 'nullable|string',
            'antecedents'    => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);
 
        $patient->update($validated);
 
        return redirect()->route('oncologie.patients.show', $patient)
            ->with('success', '✅ Patient mis à jour.');
    }
 
    // ════════════════════════════════════════
    // DESTROY
    // ════════════════════════════════════════
    public function destroy(Patient $patient)
    {
        if ($patient->prescriptions()->count() > 0) {
            return back()->with('error', '❌ Ce patient a des prescriptions actives — suppression impossible.');
        }
 
        $patient->delete();
 
        return redirect()->route('oncologie.patients.index')
            ->with('success', '✅ Patient supprimé.');
    }
 
    // ════════════════════════════════════════
    // EXPORT PDF (fiche individuelle)
    // ════════════════════════════════════════
    public function exportPdfSingle(Patient $patient)
    {
        try {
            $pdf = Pdf::loadView('oncologie.patients.pdf', compact('patient'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'DejaVu Sans',
                ]);
 
            return $pdf->download('patient_' . $patient->numero_dossier . '_' . date('Ymd') . '.pdf');
 
        } catch (\Exception $e) {
            return back()->with('error', '❌ Erreur PDF : ' . $e->getMessage());
        }
    }
 
    // ════════════════════════════════════════
    // EXPORT CSV (fiche individuelle)
    // ════════════════════════════════════════
    public function exportExcelSingle(Patient $patient)
    {
        $filename = 'patient_' . $patient->numero_dossier . '_' . date('Ymd') . '.csv';
 
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
 
        $callback = function () use ($patient) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Champ', 'Valeur'], ';');
 
            $rows = [
                ['Numéro dossier',    $patient->numero_dossier],
                ['Nom',               $patient->nom],
                ['Prénom',            $patient->prenom],
                ['Sexe',              $patient->sexe],
                ['Date de naissance', optional($patient->date_naissance)->format('d/m/Y')],
                ['Âge',               $patient->age . ' ans'],
                ['Wilaya',            $patient->wilaya ?? '-'],
                ['Daïra',             $patient->daira ?? '-'],
                ['Type de cancer',    $patient->type_cancer],
                ['Stade',             $patient->stade_cancer ?? '-'],
                ['Poids (kg)',        $patient->poids ?? '-'],
                ['Taille (cm)',       $patient->taille ?? '-'],
                ['IMC',               $patient->imc ? $patient->imc . ' (' . $patient->imc_label . ')' : '-'],
                ['SC Mosteller (m²)', $patient->surface_corporelle_calculee ?? '-'],
                ['Créatinine (mg/dL)',$patient->creatinine ?? '-'],
                ['Clairance CG',      $patient->clairance_renale ? $patient->clairance_renale . ' ml/min' : '-'],
                ['DFG CKD-EPI',       $patient->dfg_ckdepi ? $patient->dfg_ckdepi . ' ml/min/1.73m²' : '-'],
                ['Groupe sanguin',    $patient->groupe_sanguin ?? '-'],
                ['Allergies',         $patient->allergies ?? '-'],
                ['Statut vital',      ucfirst($patient->statut_vital)],
                ['Date décès',        optional($patient->date_deces)->format('d/m/Y') ?? '-'],
                ['Créé le',           $patient->created_at->format('d/m/Y H:i')],
            ];
 
            foreach ($rows as $row) {
                fputcsv($file, $row, ';');
            }
 
            fclose($file);
        };
 
        return response()->stream($callback, 200, $headers);
    }
 
    // ════════════════════════════════════════
    // EXPORT PDF LISTE (tous les patients)
    // ════════════════════════════════════════
    public function exportPdfListe(Request $request)
    {
        $query = Patient::query();
        if ($request->filled('statut_vital')) $query->where('statut_vital', $request->statut_vital);
        if ($request->filled('type_cancer'))  $query->where('type_cancer',  $request->type_cancer);
        $patients = $query->orderBy('nom')->get();
 
        try {
            $pdf = Pdf::loadView('oncologie.patients.export_list', compact('patients'))
                ->setPaper('a4', 'landscape')
                ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans']);
 
            return $pdf->download('patients_' . date('Ymd') . '.pdf');
 
        } catch (\Exception $e) {
            return back()->with('error', '❌ Erreur PDF : ' . $e->getMessage());
        }
    }
 
    // ════════════════════════════════════════
    // AJAX — Recherche rapide pour prescriptions
    // ════════════════════════════════════════
    public function ajaxSearch(Request $request)
    {
        $term = $request->get('q', '');
        $patients = Patient::vivant()
            ->where(fn($q) =>
                $q->where('nom',    'like', "%$term%")
                  ->orWhere('prenom','like', "%$term%")
                  ->orWhere('numero_dossier','like', "%$term%")
            )
            ->select('id','nom','prenom','numero_dossier','age','sexe','poids','taille',
                     'creatinine','clairance_renale','surface_corporelle','type_cancer',
                     'allergies','groupe_sanguin','telephone')
            ->limit(20)
            ->get();
 
        return response()->json($patients);
    }
 
    // ════════════════════════════════════════
    // Helpers
    // ════════════════════════════════════════
    private function typesCancer(): array
    {
        return [
            'Cancer du sein', 'Cancer du poumon', 'Cancer colorectal',
            'Cancer de la prostate', 'Cancer du col utérin', 'Cancer gastrique',
            'Cancer du foie', 'Leucémie', 'Lymphome de Hodgkin', 'Lymphome non-Hodgkinien',
            'Cancer de la thyroïde', 'Cancer du rein', 'Cancer de la vessie',
            'Cancer de l\'ovaire', 'Cancer du pancréas', 'Cancer de l\'endomètre',
            'Myélome multiple', 'Mélanome', 'Cancer ORL', 'Cancer du cerveau', 'Autre',
        ];
    }
}