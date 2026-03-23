<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\TrackPageVisits::class,
        ]);

        // Exclude logout route from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'deconnexion',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                $path = $request->path();

                // Determine which panel triggered the 403
                if (str_starts_with($path, 'loueur')) {
                    $loginUrl = '/loueur/login';
                    $panel = 'loueur';
                } elseif (str_starts_with($path, 'admin')) {
                    $loginUrl = '/admin/login';
                    $panel = 'admin';
                } elseif (str_starts_with($path, 'chauffeur')) {
                    $loginUrl = '/chauffeur/login';
                    $panel = 'chauffeur';
                } else {
                    return null; // Let Laravel handle non-panel 403s
                }

                // Clear stale session
                try {
                    auth()->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                } catch (\Exception $ex) {
                    // ignore
                }

                return response()->view('errors.403', [
                    'loginUrl' => $loginUrl,
                    'panel' => $panel,
                ], 403);
            }
        });
    })->create();
