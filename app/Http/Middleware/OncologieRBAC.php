<?php
// app/Http/Middleware/OncologieRBAC.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OncologieRBAC
{
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        $user = Auth::guard('oncologie')->user();

        if (!$user) {
            return redirect()->route('oncologie.login');
        }

        if (!$this->hasPermission($user->role, $permission)) {
            // Journaliser la tentative (Loi 25-11)
            \Log::warning('RBAC - Accès refusé', [
                'user_id'    => $user->id,
                'role'       => $user->role,
                'permission' => $permission,
                'url'        => $request->fullUrl(),
                'ip'         => $request->ip(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Accès non autorisé.'], 403);
            }

            return redirect()->route('oncologie.dashboard')
                ->with('error', '⛔ Accès refusé — Votre rôle « ' . ucfirst($user->role) . ' » ne permet pas cette action.');
        }

        return $next($request);
    }

   protected function hasPermission(string $role, string $permission): bool
{
    // Accès total pour l'administrateur
    if ($role === 'administrateur') {
        return true;
    }

    $matrix = config('oncologie_permissions.permissions', []);

    return in_array($role, $matrix[$permission] ?? [], true);
}
}