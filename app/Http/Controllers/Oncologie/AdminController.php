<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\Oncologie\OncoUser;
use App\Models\Oncologie\Protocole;
use App\Models\Oncologie\Medicament;

class AdminController extends Controller
{
    // ========================
    // UTILISATEURS
    // ========================

    public function utilisateurs(Request $request)
    {
        $query = OncoUser::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
            );
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $utilisateurs = $query->latest()->paginate(15);

        $stats = [
            'total'     => OncoUser::count(),
            'bloques' => OncoUser::where('is_locked', true)->count(),
            'actifs'  => OncoUser::where('is_locked', false)->count(),
            'medecins'  => OncoUser::where('role', 'medecin')->count(),
            'pharmaciens' => OncoUser::where('role', 'pharmacien')->count(),
            'infirmiers'  => OncoUser::where('role', 'infirmier')->count(),
            'admins'      => OncoUser::where('role', 'administrateur')->count(),
        ];

        return view('oncologie.admin.utilisateurs', compact('utilisateurs', 'stats'));
    }

    public function creerUtilisateur(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:onco_users,email',
            'role'     => 'required|in:medecin,pharmacien,infirmier,administrateur',
            'password' => 'required|string|min:8',
        ]);

        OncoUser::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'actif'    => true,
        ]);

        return back()->with('success', '✅ Utilisateur créé avec succès.');
    }

    public function modifierUtilisateur(Request $request, OncoUser $user)
    {
        $request->validate([
            'name'  => 'required|string|max:150',
            'email' => 'required|email|unique:onco_users,email,' . $user->id,
            'role'  => 'required|in:medecin,pharmacien,infirmier,administrateur',
        ]);

        $data = $request->only('name', 'email', 'role');

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', '✅ Utilisateur mis à jour.');
    }

    public function toggleActif(OncoUser $user)
{
    $newActif = !$user->actif;

    $user->update([
        'actif'     => $newActif,
        'is_locked' => !$newActif,  // synchronisation
        'login_attempts' => $newActif ? 0 : $user->login_attempts,
        'locked_at' => $newActif ? null : now(),
    ]);

    $msg = $newActif ? '✅ Compte activé.' : '🔒 Compte désactivé.';
    return back()->with('success', $msg);
}

    public function debloquer(OncoUser $user)
    {
        $user->update([
    'is_locked' => 0,
    'login_attempts' => 0,
    'locked_at' => null
]);
        $cacheKey = 'login_attempts_' . md5($user->email);
        Cache::forget($cacheKey);

        return back()->with('success', '✅ Compte débloqué avec succès.');
    }

    public function supprimerUtilisateur(OncoUser $user)
    {
        if ($user->id === auth()->guard('oncologie')->id()) {
            return back()->with('error', '❌ Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();
        return back()->with('success', '✅ Utilisateur supprimé.');
    }

    // ========================
    // LOGS
    // ========================

    // Remplacer UNIQUEMENT la méthode logs() dans AdminController.php :

public function logs()
{
    $derniersPat   = collect([]);
    $derniersPresc = collect([]);
    $derniersDisp  = collect([]);

    try {
        $derniersPat = \App\Models\Oncologie\Patient::latest()
            ->limit(10)
            ->get();
    } catch (\Exception $e) {}

    try {
        $derniersPresc = \App\Models\Oncologie\Prescription::with('patient')
            ->latest()
            ->limit(10)
            ->get();
    } catch (\Exception $e) {}

    try {
        $derniersDisp = \App\Models\Oncologie\Dispensation::with([
                'prescription' => fn($q) => $q->with('patient'),
                'medicament'
            ])
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
    } catch (\Exception $e) {}

    return view('oncologie.admin.logs', compact(
        'derniersPat', 'derniersPresc', 'derniersDisp'
    ));
}

    // ========================
    // RÉFÉRENTIELS
    // ========================

    public function referentiels()
    {
        $protocoles  = Protocole::withCount('medicaments')->latest()->get();
        $medicaments = Medicament::with('mouvements')->latest()->get();

        return view('oncologie.admin.referentiels', compact('protocoles', 'medicaments'));
    }

    public function storeProtocole(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:150|unique:protocoles,nom',
            'type_cancer' => 'nullable|string',
            'description' => 'nullable|string',
            'duree'       => 'nullable|integer|min:1',
        ]);

        Protocole::create($request->only('nom', 'type_cancer', 'description', 'duree'));

        return back()->with('success', '✅ Protocole créé.');
    }

    public function destroyProtocole(Protocole $protocole)
    {
        if ($protocole->prescriptions()->count() > 0) {
            return back()->with('error', '❌ Protocole lié à des prescriptions — suppression impossible.');
        }

        $protocole->delete();
        return back()->with('success', '✅ Protocole supprimé.');
    }
}