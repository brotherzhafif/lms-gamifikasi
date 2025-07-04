<?php

namespace App\Filament\Siswa\Pages;

use App\Models\User;
use App\Models\Progress;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Leaderboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Progress Saya';
    protected static ?string $navigationLabel = 'Ranking Siswa';
    protected static string $view = 'filament.siswa.pages.leaderboard';

    public function getLeaderboard()
    {
        return User::where('role', 'murid')
            ->leftJoin('progress', 'users.id', '=', 'progress.user_id')
            ->select([
                'users.id',
                'users.nama',
                'users.nis',
                DB::raw('COALESCE(SUM(progress.jumlah_poin), 0) as total_poin'),
                DB::raw('COUNT(DISTINCT progress.modul_id) as modul_selesai')
            ])
            ->groupBy('users.id', 'users.nama', 'users.nis')
            ->orderByDesc('total_poin')
            ->get()
            ->map(function ($user, $index) {
                $user->ranking = $index + 1;
                return $user;
            });
    }
}
