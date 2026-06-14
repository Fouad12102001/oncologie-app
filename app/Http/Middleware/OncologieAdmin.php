<?php
// app/Http/Middleware/OncologieAdmin.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OncologieAdmin
{
    // Session timeout 30 min (Loi 25-11 — sécurité données médicales)
    const SESSION_TIMEOUT_MINUTES = 30;

    public function handle(Request $request, Closure $next): mixed
    {
        $guard = Auth::guard('oncologie');

        // 1. Authentification
        if (!$guard->check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Non authentifié.'], 401)
                : redirect()->route('oncologie.login');
        }

        $user = $guard->user();

        // 2. Compte verrouillé
        if ($user->is_locked) {
            $guard->logout();
            return redirect()->route('oncologie.login')
                ->with('error', '🔒 Votre compte est verrouillé. Contactez l\'administrateur.');
        }

        // 3. Session timeout
        $lastActivity = session('onco_last_activity');
        if ($lastActivity && now()->diffInMinutes($lastActivity) > self::SESSION_TIMEOUT_MINUTES) {
            $guard->logout();
            session()->forget('onco_last_activity');
            return redirect()->route('oncologie.login')
                ->with('error', '⏱ Session expirée (30 min d\'inactivité). Reconnectez-vous.');
        }
        session(['onco_last_activity' => now()]);

        // 4. Partager données avec toutes les vues
        $permissions = config('oncologie_permissions.permissions', []);
        $rolePermissions = collect($permissions)
            ->filter(fn($roles) => in_array($user->role, $roles))
            ->keys()
            ->toArray();

        view()->share('authUser', $user);
        view()->share('authPermissions', $rolePermissions);
        view()->share('authMenu', config('oncologie_permissions.menus.' . $user->role, []));

        return $next($request);
    }
}