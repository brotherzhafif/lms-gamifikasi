<?php

namespace App\Filament\Admin\Pages\Auth;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        if (
            !Auth::attempt([
                'email' => $data['email'],
                'password' => $data['password'],
            ], $data['remember'] ?? false)
        ) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        $user = Auth::user();

        // Redirect based on role after successful login
        if ($user->role !== 'admin') {
            Auth::logout();
            throw ValidationException::withMessages([
                'data.email' => 'Akses ditolak. Silakan gunakan panel yang sesuai.',
            ]);
        }

        return parent::authenticate();
    }

    public function getTitle(): string
    {
        return 'Login Admin';
    }

    public function getHeading(): string
    {
        return 'Masuk sebagai Admin';
    }

    public function getSubHeading(): ?string
    {
        return 'Kelola sistem LMS Gamifikasi';
    }
}
