<?php

namespace App\Providers\Filament;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class LoginProvider implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        // Send welcome notification based on role
        $this->sendWelcomeNotification($user);

        // Redirect based on user role
        return match ($user->role) {
            'admin' => redirect()->intended('/admin'),
            'guru' => redirect()->intended('/guru'),
            'murid' => redirect()->intended('/siswa'),
            default => $this->handleInvalidRole(),
        };
    }

    private function sendWelcomeNotification($user)
    {
        $welcomeMessages = [
            'admin' => '🔧 Selamat datang Administrator! Kelola sistem LMS dengan bijak.',
            'guru' => '👨‍🏫 Selamat datang Guru! Semangat mengajar dan membimbing siswa.',
            'murid' => '👨‍🎓 Selamat datang Siswa! Semangat belajar dan raih prestasi terbaik!'
        ];

        $icons = [
            'admin' => '🛡️',
            'guru' => '📚',
            'murid' => '⭐'
        ];

        $message = $welcomeMessages[$user->role] ?? 'Selamat datang!';
        $icon = $icons[$user->role] ?? '👋';

        Notification::make()
            ->title("{$icon} Selamat Datang, {$user->nama}!")
            ->body($message)
            ->success()
            ->duration(5000)
            ->send();
    }

    private function handleInvalidRole()
    {
        Auth::logout();

        Notification::make()
            ->title('❌ Akses Ditolak')
            ->body('Role tidak valid. Silakan hubungi administrator.')
            ->danger()
            ->send();

        return redirect('/login')->with('error', 'Role tidak dikenali');
    }
}
