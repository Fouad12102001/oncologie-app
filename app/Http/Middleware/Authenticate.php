<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Redirection selon le guard utilisé
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {

            // Si guard = oncologie → rediriger vers login oncologie
            if ($request->is('oncologie/*')) {
                return route('oncologie.login');
            }

            // Sinon → login standard
            return route('login');
        }

        return null;
    }
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
{
    return $request->expectsJson()
        ? response()->json(['message' => 'Unauthenticated.'], 401)
        : redirect()->route('oncologie.login');
}
}