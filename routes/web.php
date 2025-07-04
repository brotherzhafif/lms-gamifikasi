<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// 🔁 Home: Redirect ke panel sesuai role jika sudah login
Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'admin' => redirect('/admin'),
            'guru' => redirect('/guru'),
            'murid' => redirect('/siswa'),
            default => abort(403),
        };
    }

    return redirect('/admin/login');
});

// 🔐 Login: Redirect manual ke login Filament Admin (shared)
Route::get('/login', fn() => redirect('/admin/login'))->name('login');

// 🚪 Logout: Manual logout + redirect ke login admin
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/admin/login');
})->name('logout');

// 💡 Semua route panel ditangani oleh masing-masing PanelProvider:
// - /admin → AdminPanelProvider
// - /guru  → GuruPanelProvider
// - /siswa → SiswaPanelProvider
