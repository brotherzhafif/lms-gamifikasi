<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\FileController;

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

// Unified login route
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Handle login post
Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();
        switch ($user->role) {
            case 'admin':
                return redirect('/admin');
            case 'guru':
                return redirect('/guru');
            case 'murid':
                return redirect('/siswa');
            default:
                Auth::logout();
                return redirect('/login')->with('error', 'Role tidak valid');
        }
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
})->name('login.post');

// Logout route
Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Additional helper routes for students
Route::middleware(['auth', \App\Http\Middleware\SiswaMiddleware::class])->group(function () {
    Route::post('/siswa/progress/mark-complete', [ProgressController::class, 'store'])->name('siswa.progress.mark-complete');
});

// File download routes
Route::middleware(['auth'])->group(function () {
    // Preview routes (untuk buka di tab baru)
    Route::get('/files/modul/{modul}/preview/{filename}', [FileController::class, 'previewModulFile'])
        ->name('files.modul.preview');

    Route::get('/files/jawaban/{jawaban}/preview/{filename}', [FileController::class, 'previewJawabanFile'])
        ->name('files.jawaban.preview');

    // Download routes (untuk download file)
    Route::get('/files/modul/{modul}/download/{filename}', [FileController::class, 'downloadModulFile'])
        ->name('files.modul.download');

    Route::get('/files/jawaban/{jawaban}/download/{filename}', [FileController::class, 'downloadJawabanFile'])
        ->name('files.jawaban.download');

    Route::get('/files/url', [FileController::class, 'getFileUrl'])
        ->name('files.url');
});

// All other routes are handled by Filament panels
// /admin - AdminPanelProvider
// /guru - GuruPanelProvider  
// /siswa - SiswaPanelProvider