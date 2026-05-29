<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OncologieAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('oncologie')->user();

        if (!$user || $user->role !== 'administrateur') {
            return redirect()->route('oncologie.dashboard')
                ->with('error', '❌ Accès refusé — Réservé aux administrateurs.');
        }

        return $next($request);
    }
}