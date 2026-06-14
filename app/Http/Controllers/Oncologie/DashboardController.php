<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Oncologie\Patient;
use App\Models\Oncologie\Prescription;
use App\Models\Oncologie\Medicament;
use App\Models\Oncologie\Dispensation;
use App\Models\Oncologie\Lot;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPatients = Patient::count();
        $vivants       = Patient::vivant()->count();
        $decedes       = Patient::decede()->count();

        $prescriptions = Prescription::count();
        $enAttente     = Prescription::where('statut','en_attente')->count();
        $validees      = Prescription::where('statut','validee')->count();

        $medicaments   = Medicament::with('mouvements')->get();

        $ruptures      = $medicaments->filter(fn($m) => $m->enRupture())->count();
        $expires       = $medicaments->filter(fn($m) => $m->estExpire())->count();
        $bientot       = $medicaments->filter(fn($m) => $m->bientotExpire() && !$m->estExpire())->count();

        $dispToday     = Dispensation::whereDate('date_dispensation', today())->count();
        $dispTotal     = Dispensation::count();

        $totalLots     = Lot::count();
        $lotsExpires   = Lot::all()->filter(fn($l) => $l->estExpire())->count();

        $recent = Dispensation::with([
                'prescription.patient',
                'medicament'
            ])
            ->latest('date_dispensation')
            ->limit(5)
            ->get();

        $user = Auth::guard('oncologie')->user();

        return view('oncologie.dashboard', compact(
            'totalPatients',
            'vivants',
            'decedes',
            'prescriptions',
            'enAttente',
            'validees',
            'medicaments',
            'ruptures',
            'expires',
            'bientot',
            'dispToday',
            'dispTotal',
            'totalLots',
            'lotsExpires',
            'recent',
            'user'
        ));
    }
}