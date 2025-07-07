<?php

namespace App\Filament\Siswa\Widgets;

use App\Models\Progress;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentProfileWidget extends Widget
{
    protected static string $view = 'filament.siswa.widgets.student-profile';

    protected int|string|array $columnSpan = 12;

    protected static ?int $sort = 0;

    public function getViewData(): array
    {
        $user = Auth::user();
        $totalPoin = Progress::where('user_id', $user->id)->sum('jumlah_poin');

        // Get ranking within class
        $classRanking = $this->getClassRanking($user);

        return [
            'user' => $user,
            'totalPoin' => $totalPoin,
            'classRanking' => $classRanking,
        ];
    }

    private function getClassRanking($user)
    {
        if (!$user->kelas_id) {
            return null;
        }

        $classmates = User::where('role', 'murid')
            ->where('users.kelas_id', $user->kelas_id)
            ->leftJoin('progress', 'users.id', '=', 'progress.user_id')
            ->select([
                'users.id',
                'users.nama',
                DB::raw('COALESCE(SUM(progress.jumlah_poin), 0) as total_poin')
            ])
            ->groupBy('users.id', 'users.nama')
            ->orderByDesc('total_poin')
            ->get()
            ->map(function ($student, $index) {
                $student->ranking = $index + 1;
                return $student;
            });

        return $classmates->where('id', $user->id)->first();
    }
}
