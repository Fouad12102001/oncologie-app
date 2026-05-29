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
    public function index()
    {
        $dispensations = Dispensation::with([
                'prescription.patient',
                'medicament',
                'lot'
            ])
            ->orderBy('date_dispensation', 'desc')
            ->paginate(20);

        return view('oncologie.dispensations.index', compact('dispensations'));
    }

    public function create()
    {
        $prescriptions = Prescription::with('patient')
            ->where('statut', 'validee')
            ->latest()
            ->get();

        $medicaments = Medicament::with('mouvements')->get()
            ->filter(fn($m) => !$m->estExpire() && $m->stockActuel() > 0)
            ->values();

        return view('oncologie.dispensations.create', compact(
            'prescriptions', 'medicaments'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'prescription_id' => 'required|exists:oncologie.prescriptions,id',
            'medicament_id'   => 'required|exists:oncologie.medicaments,id',
            'quantite'        => 'required|integer|min:1',
        ]);

        $medicament = Medicament::findOrFail($request->medicament_id);
        $quantite   = (int) $request->quantite;

        // Vérification stock
        if ($medicament->stockActuel() < $quantite) {
            return back()->with('error',
                '❌ Stock insuffisant. Disponible : ' . $medicament->stockActuel()
            );
        }

        // FIFO : chercher le lot le plus ancien non expiré avec stock
        $lot = Lot::where('medicament_id', $request->medicament_id)
            ->whereDate('date_expiration', '>', now())
            ->orderBy('date_fabrication', 'asc')
            ->get()
            ->first(fn($l) => $l->stockRestant() >= $quantite);

        if (!$lot) {
            return back()->with('error',
                '❌ Aucun lot valide disponible pour cette quantité.'
            );
        }

        try {
            DB::connection('oncologie')->transaction(function () use (
                $request, $lot, $medicament, $quantite
            ) {
                // Créer la dispensation
                Dispensation::create([
                    'prescription_id' => $request->prescription_id,
                    'medicament_id'   => $request->medicament_id,
                    'lot_id'          => $lot->id,
                    'quantite'        => $quantite,
                    'date_dispensation'         => now(),
                ]);

                // Enregistrer mouvement de sortie
                MouvementStock::create([
                    'medicament_id'   => $request->medicament_id,
                    'type'            => MouvementStock::TYPE_SORTIE,
                    'quantite'        => $quantite,
                    'date_mouvement'  => now(),
                ]);
            });

            return redirect()->route('oncologie.dispensations.index')
                ->with('success', '✅ Dispensation FIFO effectuée — Lot #' . $lot->numero);

        } catch (\Exception $e) {
            return back()->with('error', '❌ Erreur : ' . $e->getMessage());
        }
    }

    public function show(Dispensation $dispensation)
    {
        $dispensation->load(['prescription.patient', 'medicament', 'lot']);
        return view('oncologie.dispensations.show', compact('dispensation'));
    }

    public function export(Request $request)
    {
        $dispensations = Dispensation::with([
                'prescription.patient',
                'medicament',
                'lot'
            ])
            ->orderBy('date_dispensation', 'desc')
            ->get();

        $format = $request->get('format', 'csv');

        if ($format === 'pdf') {
            try {
                $pdf = Pdf::loadView('oncologie.dispensations.export', compact('dispensations'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isRemoteEnabled'      => false,
                        'defaultFont'          => 'DejaVu Sans',
                    ]);

                return $pdf->download('dispensations_' . date('Ymd') . '.pdf');

            } catch (\Exception $e) {
                return back()->with('error', '❌ Erreur PDF : ' . $e->getMessage());
            }
        }

        // CSV
        $filename = 'dispensations_' . date('Ymd') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($dispensations) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID','Patient','Médicament','Lot','Quantité','Date'
            ], ';');

            foreach ($dispensations as $disp) {
                fputcsv($file, [
                    $diso->id,
                    optional(optional($disp->prescription)->patient)->nom . ' '
                        . optional(optional($disp->prescription)->patient)->prenom,
                    optional($disp->medicament)->nom ?? '-',
                    optional($disp->lot)->numero ?? '-',
                    $disp->quantite,
                    optional($disp->date_formattee)->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}