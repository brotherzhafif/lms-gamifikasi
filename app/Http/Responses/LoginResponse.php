<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class LoginResponse implements LoginResponseContract
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
            default => abort(403, 'Role tidak dikenali'),
        };
    }

    private function sendWelcomeNotification($user)
    {
        $welcomeMessages = [
            'admin' => 'Selamat datang Administrator! Kelola sistem LMS dengan bijak.',
            'guru' => 'Selamat datang Guru! Semangat mengajar dan membimbing siswa.',
            'murid' => 'Selamat datang Siswa! Semangat belajar dan raih prestasi terbaik!'
        ];

        $message = $welcomeMessages[$user->role] ?? 'Selamat datang!';

        Notification::make()
            ->title('Selamat Datang!')
            ->body($message)
            ->success()
            ->send();
    }
}
