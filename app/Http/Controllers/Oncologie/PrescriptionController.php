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
        $query = Prescription::with(['patient', 'protocole', 'details.medicament']);

        if ($request->filled('patient')) {
            $s = $request->patient;
            $query->whereHas('patient', fn($q) =>
                $q->where('nom',    'like', "%$s%")
                  ->orWhere('prenom','like', "%$s%")
            );
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date')) {
            $query->whereDate('date_prescription', $request->date);
        }

        $prescriptions = $query->latest()->paginate(15);

        return view('oncologie.prescriptions.index', compact('prescriptions'));
    }

    // ========================
    // CREATE
    // ========================
    public function create()
    {
        $patients  = Patient::vivant()->latest()->get();
        $protocoles = Protocole::all();
        $medecins  = OncoUser::where('role', 'medecin')->where('actif', true)->get();

        return view('oncologie.prescriptions.create', compact(
            'patients', 'protocoles', 'medecins'
        ));
    }

    // ========================
    // STORE
    // ========================
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'        => 'required|exists:oncologie.patients,id',
            'protocole_id'      => 'required|exists:oncologie.protocoles,id',
            'medecin_nom'       => 'required|string',
            'date_prescription' => 'required|date',
        ]);

        $prescription = Prescription::create([
            'patient_id'        => $request->patient_id,
            'protocole_id'      => $request->protocole_id,
            'medecin_nom'       => $request->medecin_nom,
            'date_prescription' => $request->date_prescription,
            'statut'            => 'en_attente',
        ]);

        // Enregistrer les détails (médicaments + doses)
        if ($request->has('medicaments')) {
            foreach ($request->medicaments as $medId => $dose) {
                PrescriptionDetail::create([
                    'prescription_id' => $prescription->id,
                    'medicament_id'   => $medId,
                    'dose_calculee'   => $dose,
                ]);
            }
        }

        return redirect()->route('oncologie.prescriptions.index')
            ->with('success', '✅ Prescription créée avec succès.');
    }

    // ========================
    // SHOW
    // ========================
    public function show(Prescription $prescription)
    {
        $prescription->load(['patient', 'protocole', 'details.medicament', 'dispensations.medicament', 'dispensations.lot']);
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

        $patients   = Patient::vivant()->get();
        $protocoles = Protocole::all();

        return view('oncologie.prescriptions.edit', compact(
            'prescription', 'patients', 'protocoles'
        ));
    }

    public function update(Request $request, Prescription $prescription)
    {
        if ($prescription->isValidee()) {
            return back()->with('error', '❌ Prescription déjà validée.');
        }

        $request->validate([
            'patient_id'        => 'required|exists:oncologie.patients,id',
            'protocole_id'      => 'required|exists:oncologie.protocoles,id',
            'medecin_nom'       => 'required|string',
            'date_prescription' => 'required|date',
        ]);

        $prescription->update($request->only(
            'patient_id', 'protocole_id', 'medecin_nom', 'date_prescription'
        ));

        // Mettre à jour les détails
        if ($request->has('medicaments')) {
            $prescription->details()->delete();
            foreach ($request->medicaments as $medId => $dose) {
                PrescriptionDetail::create([
                    'prescription_id' => $prescription->id,
                    'medicament_id'   => $medId,
                    'dose_calculee'   => $dose,
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

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date')) {
            $query->whereDate('date_prescription', $request->date);
        }

        $prescriptions = $query->latest()->get();
        $format        = $request->get('format', 'pdf');

        // ---- PDF ----
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

        // ---- CSV / Excel ----
        $filename = 'prescriptions_' . date('Ymd') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($prescriptions) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID', 'Patient', 'Médecin', 'Protocole',
                'Date', 'Statut', 'Médicaments', 'SC (m²)'
            ], ';');

            foreach ($prescriptions as $p) {
                fputcsv($file, [
                    $p->id,
                    optional($p->patient)->nom . ' ' . optional($p->patient)->prenom,
                    $p->medecin_nom ?? '-',
                    optional($p->protocole)->nom ?? '-',
                    optional($p->date_prescription)->format('d/m/Y'),
                    $p->statut,
                    $p->medicaments_noms ?? '-',
                    $p->surface_corporelle ?? '-',
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
            $total      = Prescription::count();
            $validees   = Prescription::where('statut', 'validee')->count();
            $enAttente  = Prescription::where('statut', 'en_attente')->count();
            $annulees   = Prescription::where('statut', 'annulee')->count();

            // Par mois (année en cours)
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

            $moisLabels    = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
            $totauxMois    = [];
            $valideesMois  = [];
            $annuleesMois  = [];

            for ($m = 1; $m <= 12; $m++) {
                $totauxMois[]   = $parMois->get($m)?->total      ?? 0;
                $valideesMois[] = $parMois->get($m)?->nb_validees ?? 0;
                $annuleesMois[] = $parMois->get($m)?->nb_annulees ?? 0;
            }

            // Top médecins prescripteurs
            $topMedecins = Prescription::select('medecin_nom')
                ->selectRaw('COUNT(*) as total')
                ->whereNotNull('medecin_nom')
                ->groupBy('medecin_nom')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            // Top protocoles
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
