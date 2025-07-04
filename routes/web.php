<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Filament\Pages\UnifiedLogin;

// Public route - redirect to appropriate panel based on role
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        switch ($user->role) {
            case 'admin':
                return redirect('/admin');
            case 'guru':
                return redirect('/guru');
            case 'murid':
                return redirect('/siswa');
            default:
                return redirect('/login');
        }
    }
    return redirect('/login');
});

// Route login terpusat
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Custom login route dengan UnifiedLogin
Route::get('/admin/login', UnifiedLogin::class)->name('filament.admin.auth.login');

// All other routes are handled by Filament panels
// /admin - AdminPanelProvider
// /guru - GuruPanelProvider  
// /siswa - SiswaPanelProvider