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
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 12;

    protected function getStats(): array
    {
        $userId = Auth::id();

        return [
            Stat::make('Total Poin', Progress::where('user_id', $userId)->sum('jumlah_poin'))
                ->description('Poin yang telah dikumpulkan')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning')
                ->chart([7, 12, 8, 15, 23, 18, 25])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-gray-800 dark:to-gray-900 border-yellow-200 dark:border-gray-600',
                ]),

            Stat::make('Modul Selesai', Progress::where('user_id', $userId)->distinct('modul_id')->count())
                ->description('Modul yang telah diselesaikan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([5, 8, 12, 15, 18, 20, 22])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-900 border-green-200 dark:border-gray-600',
                ]),

            Stat::make('Tugas Dikerjakan', Jawaban::where('siswa_id', $userId)->whereIn('status', ['dikirim', 'dinilai'])->count())
                ->description('Tugas yang telah dikerjakan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([3, 6, 9, 12, 14, 16, 18])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-gray-800 dark:to-gray-900 border-blue-200 dark:border-gray-600',
                ]),

            Stat::make('Modul Tersedia', Modul::where('is_active', true)->where('kelas_id', Auth::user()->kelas_id)->count())
                ->description('Total modul untuk kelas Anda')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary')
                ->chart([20, 20, 20, 20, 20, 20, 20])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 border-purple-200 dark:border-gray-600',
                ]),
        ];
    }
}
