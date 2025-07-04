<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin' => redirect()->intended('/admin'),
            'guru'  => redirect()->intended('/guru'),
            'murid' => redirect()->intended('/siswa'),
            default => abort(403),
        };
    }
}
