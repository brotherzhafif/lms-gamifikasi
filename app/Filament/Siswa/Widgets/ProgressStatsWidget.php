<?php

namespace App\Filament\Siswa\Widgets;

use App\Models\Progress;
use App\Models\Modul;
use App\Models\Jawaban;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ProgressStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        return [
            Stat::make('Total Poin', Progress::where('user_id', $userId)->sum('jumlah_poin'))
                ->description('Poin yang telah dikumpulkan')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),

            Stat::make('Modul Selesai', Progress::where('user_id', $userId)->distinct('modul_id')->count())
                ->description('Modul yang telah diselesaikan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            Stat::make('Tugas Dikerjakan', Jawaban::where('siswa_id', $userId)->whereIn('status', ['dikirim', 'dinilai'])->count())
                ->description('Tugas yang telah dikerjakan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Modul Tersedia', Modul::where('is_active', true)->count())
                ->description('Total modul yang tersedia')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),
        ];
    }
}
