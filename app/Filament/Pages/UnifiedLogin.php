<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UnifiedLogin extends BaseLogin
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
        try {
            $data = $this->form->getState();

            if (
                !Auth::attempt([
                    'email' => $data['email'],
                    'password' => $data['password'],
                ], $data['remember'] ?? false)
            ) {
                $this->throwFailureValidationException();
            }

            $user = Auth::user();

            // Redirect berdasarkan role
            $redirectUrl = $this->getRedirectUrlBasedOnRole($user);

            session()->regenerate();

            return new class ($redirectUrl) implements LoginResponse {
                public function __construct(private string $url)
                {
                }

                public function toResponse($request)
                {
                    return redirect($this->url);
                }
            };

        } catch (ValidationException $exception) {
            throw $exception;
        }
    }

    protected function getRedirectUrlBasedOnRole(User $user): string
    {
        return match ($user->role) {
            'admin' => '/admin',
            'guru' => '/guru',
            'siswa' => '/siswa',
            default => '/admin'
        };
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
