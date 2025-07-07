<?php

namespace App\Filament\Guru\Pages;

use App\Models\User;
use App\Models\Progress;
use App\Models\Modul;
use App\Models\Kelas;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Leaderboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Ranking Siswa';
    protected static string $view = 'filament.guru.leaderboard';

    public $selectedClass = '';

    public function getLeaderboard()
    {
        $query = User::where('role', 'murid')
            ->leftJoin('progress', 'users.id', '=', 'progress.user_id')
            ->leftJoin('modul', 'progress.modul_id', '=', 'modul.id')
            ->leftJoin('kelas', 'users.kelas_id', '=', 'kelas.id')
            ->where(function ($query) {
                $query->where('modul.guru_id', Auth::id())
                    ->orWhereNull('modul.guru_id');
            });

        if ($this->selectedClass) {
            $query->where('users.kelas_id', $this->selectedClass);
        }

        return $query->select([
            'users.id',
            'users.nama',
            'users.nis',
            'kelas.nama_kelas',
            DB::raw('COALESCE(SUM(CASE WHEN modul.guru_id = ' . Auth::id() . ' THEN progress.jumlah_poin ELSE 0 END), 0) as total_poin'),
            DB::raw('COUNT(DISTINCT CASE WHEN modul.guru_id = ' . Auth::id() . ' THEN progress.modul_id END) as modul_selesai')
        ])
            ->groupBy('users.id', 'users.nama', 'users.nis', 'kelas.nama_kelas')
            ->orderByDesc('total_poin')
            ->get()
            ->map(function ($user, $index) {
                $user->ranking = $index + 1;
                return $user;
            });
    }

    public function getTotalStudents()
    {
        return User::where('role', 'murid')->count();
    }

    public function getActiveStudents()
    {
        return User::where('role', 'murid')
            ->whereHas('progresses.modul', function ($query) {
                $query->where('guru_id', Auth::id());
            })
            ->count();
    }

    public function getTotalModules()
    {
        return Modul::where('guru_id', Auth::id())->count();
    }

    public function getTotalProgress()
    {
        return Progress::whereHas('modul', function ($query) {
            $query->where('guru_id', Auth::id());
        })->count();
    }

    public function getSubjects()
    {
        return collect(); // Guru doesn't need subject filter
    }

    public function getClasses()
    {
        return Kelas::where('is_active', true)
            ->orderBy('nama_kelas')
            ->get();
    }
}
