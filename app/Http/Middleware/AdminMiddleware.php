<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/admin/login');
        }

        $user = auth()->user();

        if ($user->role !== 'admin') {
            // Redirect to appropriate panel based on role
            return match ($user->role) {
                'guru' => redirect('/guru'),
                'murid' => redirect('/siswa'),
                default => redirect('/admin/login'),
            };
        }

        return $next($request);
    }
}
