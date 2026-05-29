<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use App\Models\Oncologie\Patient;
use App\Models\Oncologie\Prescription;
use App\Models\Oncologie\Medicament;
use App\Models\Oncologie\Dispensation;
use App\Models\Oncologie\Lot;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    public function index()
    {
        // ===== PATIENTS =====
        $totalPatients  = Patient::count();
        $vivants        = Patient::vivant()->count();
        $decedes        = Patient::decede()->count();
        $masculin       = Patient::where('sexe', 'Masculin')->count();
        $feminin        = Patient::where('sexe', 'Féminin')->count();

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
            ->limit(8)
            ->get();

        $parMois = Patient::selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('mois')
            ->orderBy('mois')
            ->get()
            ->keyBy('mois');

        $patientsParMois = [];
        for ($m = 1; $m <= 12; $m++) {
            $patientsParMois[] = $parMois->get($m)?->total ?? 0;
        }

        // ===== PRESCRIPTIONS =====
        $totalPrescriptions = Prescription::count();
        $validees   = Prescription::where('statut', 'validee')->count();
        $enAttente  = Prescription::where('statut', 'en_attente')->count();
        $annulees   = Prescription::where('statut', 'annulee')->count();

        $prescParMois = Prescription::selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('mois')
            ->orderBy('mois')
            ->get()
            ->keyBy('mois');

        $prescriptionsMois = [];
        for ($m = 1; $m <= 12; $m++) {
            $prescriptionsMois[] = $prescParMois->get($m)?->total ?? 0;
        }

        // ===== MÉDICAMENTS =====
        $medicaments    = Medicament::with('mouvements')->get();
        $totalMeds      = $medicaments->count();
        $ruptures       = $medicaments->filter(fn($m) => $m->enRupture())->count();
        $expires        = $medicaments->filter(fn($m) => $m->estExpire())->count();
        $bientot        = $medicaments->filter(fn($m) => $m->bientotExpire() && !$m->estExpire())->count();
        $alerteStock    = $medicaments->filter(fn($m) => $m->statutStock() === 'alerte')->count();
        $stockOk        = $totalMeds - $ruptures - $alerteStock;

        // ===== DISPENSATIONS =====
        $totalDisp      = Dispensation::count();
        $dispAujourdhui = Dispensation::whereDate('date_dispensation', today())->count();
        $dispCeSemaine  = Dispensation::whereBetween('date_dispensation', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $dispCeMois     = Dispensation::whereMonth('date_dispensation', now()->month)
                            ->whereYear('date_dispensation', now()->year)->count();

        $dispParMois = Dispensation::selectRaw('MONTH(date_dispensation) as mois, COUNT(*) as total, SUM(quantite) as qte')
            ->whereYear('date_dispensation', now()->year)
            ->groupBy('mois')
            ->orderBy('mois')
            ->get()
            ->keyBy('mois');

        $dispensationsMois = [];
        $quantitesMois = [];
        for ($m = 1; $m <= 12; $m++) {
            $dispensationsMois[] = $dispParMois->get($m)?->total ?? 0;
            $quantitesMois[]     = $dispParMois->get($m)?->qte ?? 0;
        }

        // ===== LOTS =====
        $totalLots     = Lot::count();
        $lotsExpires   = Lot::get()->filter(fn($l) => $l->estExpire())->count();
        $lotsBientot   = Lot::get()->filter(fn($l) => $l->expireBientot(90) && !$l->estExpire())->count();
        $lotsValides   = $totalLots - $lotsExpires - $lotsBientot;

        $moisLabels = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];

        return view('oncologie.statistiques.index', compact(
            'totalPatients','vivants','decedes','masculin','feminin',
            'parCancer','parWilaya','patientsParMois',
            'totalPrescriptions','validees','enAttente','annulees','prescriptionsMois',
            'totalMeds','ruptures','expires','bientot','alerteStock','stockOk',
            'totalDisp','dispAujourdhui','dispCeSemaine','dispCeMois',
            'dispensationsMois','quantitesMois',
            'totalLots','lotsExpires','lotsBientot','lotsValides',
            'moisLabels'
        ));
    }
}