<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

    return redirect('/login');
});

// 🔐 Login: Halaman login terpusat
Route::get('/login', \App\Http\Controllers\Auth\LoginController::class)->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// 💡 Semua route panel ditangani oleh masing-masing PanelProvider:
// - /admin → AdminPanelProvider 
// - /guru  → GuruPanelProvider
// - /siswa → SiswaPanelProvider
