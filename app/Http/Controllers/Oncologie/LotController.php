<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Oncologie\Lot;
use App\Models\Oncologie\Medicament;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LotController extends Controller
{
    public function index(Request $request)
    {
        $query = Lot::with('medicament')->latest();

        if ($request->filled('medicament')) {
            $query->whereHas('medicament', fn($q) =>
                $q->where('nom', 'like', '%' . $request->medicament . '%')
            );
        }

        if ($request->alerte === 'expire') {
            $query->where('date_expiration', '<', now());
        }

        $lots = $query->get();

        return view('oncologie.lots.index', compact('lots'));
    }

    public function create()
    {
        $medicaments = Medicament::orderBy('nom')->get();
        return view('oncologie.lots.create', compact('medicaments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicament_id'     => 'required|exists:medicaments,id',
            'numero'            => 'required|string|max:100',
            'quantite_initiale' => 'required|integer|min:1',
            'date_fabrication'  => 'nullable|date',
            'date_expiration'   => 'required|date',
        ]);

        $expiration = Carbon::parse($request->date_expiration);

        // Bloquer si déjà expiré
        if ($expiration->isPast()) {
            return back()
                ->withErrors(['date_expiration' => '⛔ Ce lot est déjà expiré.'])
                ->withInput();
        }

        $lot = Lot::create($request->all());

        $message = $lot->expireBientot(90)
            ? '⚠️ Lot ajouté mais expire bientôt (moins de 3 mois).'
            : '✅ Lot ajouté avec succès.';

        $key = $lot->expireBientot(90) ? 'warning' : 'success';

        return redirect()->route('oncologie.lots.index')->with($key, $message);
    }

    public function show(Lot $lot)
    {
        $lot->load('medicament', 'dispensations');
        return view('oncologie.lots.show', compact('lot'));
    }

    public function edit(Lot $lot)
    {
        $medicaments = Medicament::orderBy('nom')->get();
        return view('oncologie.lots.edit', compact('lot', 'medicaments'));
    }

    public function update(Request $request, Lot $lot)
    {
        $request->validate([
            'medicament_id'     => 'required|exists:medicaments,id',
            'numero'            => 'required|string|max:100',
            'quantite_initiale' => 'required|integer|min:1',
            'date_fabrication'  => 'nullable|date',
            'date_expiration'   => 'required|date',
        ]);

        $lot->update($request->all());

        return redirect()->route('oncologie.lots.index')
            ->with('success', '✅ Lot mis à jour.');
    }

    public function destroy(Lot $lot)
    {
        if ($lot->dispensations()->count() > 0) {
            return back()->with('error',
                '❌ Impossible : ce lot a été utilisé dans des dispensations.'
            );
        }

        $lot->delete();
        return redirect()->route('oncologie.lots.index')
            ->with('success', '✅ Lot supprimé.');
    }

    public function exportPdf()
    {
        $lots = Lot::with('medicament')->latest()->get();
        $pdf  = Pdf::loadView('oncologie.lots.pdf', compact('lots'));
        return $pdf->download('lots.pdf');
    }
}
