<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use App\Models\Modul;
use App\Models\Progress;
use App\Models\Jawaban;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Siswa', User::where('role', 'murid')->count())
                ->description('Siswa terdaftar')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),
            Stat::make('Total Guru', User::where('role', 'guru')->count())
                ->description('Guru aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
            Stat::make('Total Modul', Modul::count())
                ->description('Modul pembelajaran')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),
            Stat::make('Total Poin', Progress::sum('jumlah_poin'))
                ->description('Poin terkumpul')
                ->descriptionIcon('heroicon-m-star')
                ->color('primary'),
        ];
    }
}
