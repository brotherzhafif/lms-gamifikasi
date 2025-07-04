<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        // Redirect based on user role
        return match ($user->role) {
            'admin' => redirect('/admin'),
            'guru' => redirect('/guru'),
            'murid' => redirect('/siswa'),
            default => abort(403, 'Role tidak dikenali'),
        };
    }
}
