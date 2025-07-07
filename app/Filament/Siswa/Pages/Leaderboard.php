<?php

namespace App\Filament\Siswa\Pages;

use App\Models\User;
use App\Models\Progress;
use App\Models\Modul;
use App\Models\MataPelajaran;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Leaderboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Progress Saya';
    protected static ?string $navigationLabel = 'Ranking Siswa';
    protected static string $view = 'filament.leaderboard';

    public $selectedSubject = '';

    public function getLeaderboard()
    {
        $userKelas = Auth::user()->kelas_id;

        $query = User::where('role', 'murid')
            ->where('users.kelas_id', $userKelas) // Only students from same class - specify table
            ->leftJoin('progress', 'users.id', '=', 'progress.user_id')
            ->leftJoin('modul', 'progress.modul_id', '=', 'modul.id')
            ->leftJoin('mata_pelajaran', 'modul.mata_pelajaran_id', '=', 'mata_pelajaran.id');

        if ($this->selectedSubject) {
            $query->where('mata_pelajaran.id', $this->selectedSubject);
        }

        return $query->select([
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

    public function getMyRanking()
    {
        $leaderboard = $this->getLeaderboard();
        $myRecord = $leaderboard->where('id', Auth::id())->first();
        return $myRecord ? $myRecord->ranking : 0;
    }

    public function getMyTotalPoints()
    {
        return Progress::where('user_id', Auth::id())->sum('jumlah_poin');
    }

    public function getTotalStudents()
    {
        return User::where('role', 'murid')->count();
    }

    public function getActiveStudents()
    {
        return User::where('role', 'murid')
            ->whereHas('progresses')
            ->count();
    }

    public function getTotalModules()
    {
        return Modul::count();
    }

    public function getTotalProgress()
    {
        return Progress::count();
    }

    public function getSubjects()
    {
        return MataPelajaran::where('is_active', true)
            ->orderBy('nama_mapel')
            ->get();
    }
}
