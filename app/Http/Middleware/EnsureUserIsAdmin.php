<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has admin role
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}
