<?php

namespace App\Filament\Admin\Pages;

use App\Models\User;
use App\Models\Progress;
use App\Models\Modul;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Leaderboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Statistik';
    protected static ?string $navigationLabel = 'Ranking Siswa';
    protected static string $view = 'filament.admin.leaderboard';

    public $selectedSubject = '';

    public $selectedClass = '';

    public function getLeaderboard()
    {
        $query = User::where('role', 'murid')
            ->leftJoin('progress', 'users.id', '=', 'progress.user_id')
            ->leftJoin('modul', 'progress.modul_id', '=', 'modul.id')
            ->leftJoin('mata_pelajaran', 'modul.mata_pelajaran_id', '=', 'mata_pelajaran.id')
            ->leftJoin('kelas', 'users.kelas_id', '=', 'kelas.id');

        if ($this->selectedSubject) {
            $query->where('mata_pelajaran.id', $this->selectedSubject);
        }

        if ($this->selectedClass) {
            $query->where('users.kelas_id', $this->selectedClass);
        }

        return $query->select([
            'users.id',
            'users.nama',
            'users.nis',
            'kelas.nama_kelas',
            DB::raw('COALESCE(SUM(progress.jumlah_poin), 0) as total_poin'),
            DB::raw('COUNT(DISTINCT progress.modul_id) as modul_selesai')
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

    public function getClasses()
    {
        return Kelas::where('is_active', true)
            ->orderBy('nama_kelas')
            ->get();
    }
}
