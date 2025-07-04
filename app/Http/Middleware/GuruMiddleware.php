<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuruMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/guru/login');
        }

        $user = auth()->user();

        if ($user->role !== 'guru') {
            // Redirect to appropriate panel based on role
            return match ($user->role) {
                'admin' => redirect('/admin'),
                'murid' => redirect('/siswa'),
                default => redirect('/guru/login'),
            };
        }

        return $next($request);
    }
}

