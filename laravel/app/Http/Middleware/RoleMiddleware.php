<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Pokud je vyžadována role admin, zkontroluj, zda je uživatel admin
        if ($role === 'admin' && !$user->isAdmin()) {
            abort(403, __('app.permission_denied'));
        }
        
        // Pokud je vyžadována role user a výš, zkontroluj, zda není demo
        if ($role === 'user' && $user->isDemo()) {
            abort(403, __('app.demo_access_denied'));
        }
        
        return $next($request);
    }
}
