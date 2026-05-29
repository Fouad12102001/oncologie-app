<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Oncologie\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('nom', 'like', "%$s%")
                  ->orWhere('prenom', 'like', "%$s%")
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

        $patients      = $query->latest()->paginate(15);
        $totalPatients = Patient::count();
        $vivants       = Patient::vivant()->count();
        $decedes       = Patient::decede()->count();

        return view('oncologie.patients.index', compact(
            'patients', 'totalPatients', 'vivants', 'decedes'
        ));
    }

    public function create()
    {
        $sexes        = ['Masculin', 'Féminin'];
        $types_cancer = $this->typesCancer();
        $statuts      = [Patient::VIVANT, Patient::DECEDE];

        return view('oncologie.patients.create', compact('sexes', 'types_cancer', 'statuts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'numero_dossier' => 'required|string|unique:patients,numero_dossier',
            'sexe'           => 'required|in:Masculin,Féminin',
            'date_naissance' => 'required|date|before:today',
            'wilaya'         => 'required|string',
            'type_cancer'    => 'required|string',
            'statut_vital'   => 'required|in:vivant,decede',
            'date_deces'     => 'nullable|date|required_if:statut_vital,decede',
        ]);

        Patient::create($request->all());

        return redirect()->route('oncologie.patients.index')
            ->with('success', '✅ Patient créé avec succès.');
    }

    public function show(Patient $patient)
    {
        return view('oncologie.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        $sexes        = ['Masculin', 'Féminin'];
        $types_cancer = $this->typesCancer();
        $statuts      = [Patient::VIVANT, Patient::DECEDE];

        return view('oncologie.patients.edit', compact('patient', 'sexes', 'types_cancer', 'statuts'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'numero_dossier' => 'required|string|unique:patients,numero_dossier,' . $patient->id,
            'sexe'           => 'required|in:Masculin,Féminin',
            'date_naissance' => 'required|date|before:today',
            'wilaya'         => 'required|string',
            'type_cancer'    => 'required|string',
            'statut_vital'   => 'required|in:vivant,decede',
            'date_deces'     => 'nullable|date|required_if:statut_vital,decede',
        ]);

        $patient->update($request->all());

        return redirect()->route('oncologie.patients.index')
            ->with('success', '✅ Patient mis à jour.');
    }

    public function destroy(Patient $patient)
    {
        if ($patient->prescriptions()->count() > 0) {
            return back()->with('error',
                '❌ Impossible : ce patient a des prescriptions actives.'
            );
        }

        $patient->delete();
        return redirect()->route('oncologie.patients.index')
            ->with('success', '✅ Patient supprimé.');
    }

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

            $filename = 'patient_' . $patient->numero_dossier . '_' . date('Ymd') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return back()->with('error', '❌ Erreur génération PDF : ' . $e->getMessage());
        }
    }

    public function exportExcelSingle(Patient $patient)
    {
        try {
            $filename = 'patient_' . $patient->numero_dossier . '_' . date('Ymd') . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($patient) {
                $file = fopen('php://output', 'w');

                // BOM UTF-8 pour Excel
                fputs($file, "\xEF\xBB\xBF");

                // En-têtes
                fputcsv($file, [
                    'Champ', 'Valeur'
                ], ';');

                // Données
                $rows = [
                    ['ID',                $patient->id],
                    ['Numéro dossier',    $patient->numero_dossier],
                    ['Nom',              $patient->nom],
                    ['Prénom',           $patient->prenom],
                    ['Sexe',             $patient->sexe],
                    ['Date de naissance', optional($patient->date_naissance)->format('d/m/Y')],
                    ['Âge',              $patient->age . ' ans'],
                    ['Wilaya',           $patient->wilaya ?? '-'],
                    ['Daïra',            $patient->daira ?? '-'],
                    ['Type de cancer',   $patient->type_cancer],
                    ['Poids (kg)',       $patient->poids ?? '-'],
                    ['Taille (cm)',      $patient->taille ?? '-'],
                    ['Surface corporelle', $patient->surface_corporelle ? $patient->surface_corporelle . ' m²' : '-'],
                    ['Statut vital',     ucfirst($patient->statut_vital)],
                    ['Date décès',       optional($patient->date_deces)->format('d/m/Y') ?? '-'],
                    ['Créé le',          $patient->created_at->format('d/m/Y H:i')],
                ];

                foreach ($rows as $row) {
                    fputcsv($file, $row, ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', '❌ Erreur export : ' . $e->getMessage());
        }
    }

    private function typesCancer(): array
    {
        return [
            'Cancer du sein', 'Cancer du poumon', 'Cancer colorectal',
            'Cancer de la prostate', 'Cancer du col utérin', 'Cancer gastrique',
            'Cancer du foie', 'Leucémie', 'Lymphome', 'Cancer de la thyroïde',
            'Cancer du rein', 'Cancer de la vessie', 'Mélanome', 'Autre',
        ];
    }
}
