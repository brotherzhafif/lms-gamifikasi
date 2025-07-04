<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Models\Modul;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    // Admin/Guru: Rekap poin siswa
    public function index()
    {
        $progresses = Progress::with('user', 'modul');

        // Filter based on role
        if (Auth::user()->role === 'guru') {
            $progresses->whereHas('modul', function ($q) {
                $q->where('guru_id', Auth::id());
            });
        }

        $progresses = $progresses->orderByDesc('created_at')->paginate(20);

        if (Auth::user()->role === 'guru') {
            return view('guru.progress.index', compact('progresses'));
        }

        return view('progress.index', compact('progresses'));
    }

    // Siswa: Lihat progress sendiri
    public function showUserProgress(Request $request)
    {
        $progresses = Progress::where('user_id', Auth::id())->with('modul')->get();
        $total_poin = $progresses->sum('jumlah_poin');
        return view('siswa.progress.index', compact('progresses', 'total_poin'));
    }

    // Siswa: Tandai materi selesai (tambah progress)
    public function store(Request $request)
    {
        $request->validate([
            'modul_id' => 'required|exists:modul,id',
        ]);

        $modul = Modul::findOrFail($request->modul_id);
        $exists = Progress::where('user_id', Auth::id())->where('modul_id', $modul->id)->exists();

        if ($exists) {
            return redirect()->back()->with('info', 'Modul sudah ditandai selesai.');
        }

        Progress::create([
            'user_id' => Auth::id(),
            'modul_id' => $modul->id,
            'jumlah_poin' => $modul->poin_reward,
            'jenis_aktivitas' => 'selesai_materi',
            'keterangan' => 'Menyelesaikan materi ' . $modul->judul,
        ]);

        return redirect()->back()->with('success', 'Materi berhasil ditandai selesai!');
    }

    // Leaderboard for students
    public function leaderboard()
    {
        $rankings = User::where('role', 'murid')
            ->withSum('progresses', 'jumlah_poin')
            ->orderByDesc('progresses_sum_jumlah_poin')
            ->paginate(20);

        return view('siswa.ranking.index', compact('rankings'));
    }
}
