<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function ($middleware) {

    $middleware->alias([
        'oncologie.admin' => \App\Http\Middleware\OncologieAdmin::class,
        'onco.auth' => \App\Http\Middleware\OncologieAdmin::class,
        'onco.rbac' => \App\Http\Middleware\OncologieRBAC::class,
    ]);
})
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (
        \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e,
        $request
    ) {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }
        return response()->view('oncologie.errors.403', [], 403);
    });
})->create();

    
