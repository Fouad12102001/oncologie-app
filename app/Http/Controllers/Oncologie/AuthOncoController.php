<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\Oncologie\OncoUser;

class AuthOncoController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('oncologie')->check()) {
            return redirect()->route('oncologie.dashboard');
        }
        return view('oncologie.auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $email = $credentials['email'];

    $user = OncoUser::where('email', $email)->first();

    // 1. utilisateur inexistant
    if (!$user) {
        return back()
            ->withErrors(['email' => 'Identifiants invalides.']);
    }

    // 2. compte verrouillé
    if ($user->is_locked) {
        return back()->with('locked', true);
    }

    // 3. tentative login
    if (Auth::guard('oncologie')->attempt($credentials)) {

        // reset attempts
        $user->update([
            'login_attempts' => 0
        ]);

        $request->session()->regenerate();

        return redirect()->route('oncologie.dashboard');
    }

    // 4. failed login
    $user->increment('login_attempts');

    // 5. lock account
    if ($user->login_attempts >= 3) {
        $user->update([
            'is_locked' => 1,
            'locked_at' => now()
        ]);
    }

    return back()
        ->with('attempts_left', max(0, 3 - $user->login_attempts))
        ->withErrors(['email' => 'Identifiants invalides.']);
}

    public function register(Request $request)
    {
        $request->validate([
            'prenom'                => 'required|string|max:100',
            'nom'                   => 'required|string|max:100',
            'email'                 => 'required|email|unique:onco_users,email',
            'role'                  => 'required|in:medecin,pharmacien,infirmier,administrateur',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        OncoUser::create([
            'name'     => $request->prenom . ' ' . $request->nom,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'is_locked' => 0,// En attente de validation admin
        ]);

        return back()->with('register_success', true);
    }

    public function sendResetEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        // Logique de reset (à implémenter avec notifications)
        return back()->with('reset_sent', true);
    }

    public function logout(Request $request)
    {
        Auth::guard('oncologie')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('oncologie.login');
    }
}