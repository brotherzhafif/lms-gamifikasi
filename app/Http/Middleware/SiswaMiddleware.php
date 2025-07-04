<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SiswaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/siswa/login');
        }

        $user = auth()->user();

        if ($user->role !== 'murid') {
            // Redirect to appropriate panel based on role
            return match ($user->role) {
                'admin' => redirect('/admin'),
                'guru' => redirect('/guru'),
                default => redirect('/siswa/login'),
            };
        }

        return $next($request);
    }
}
