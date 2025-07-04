<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use App\Http\Responses\LoginResponse;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function () {
            app()->bind(LoginResponseContract::class, LoginResponse::class);
        });
    }
}
