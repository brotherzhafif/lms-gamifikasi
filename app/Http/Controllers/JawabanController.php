<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Modul;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JawabanController extends Controller
{
    // Guru: List answers for a specific module
    public function index(Modul $modul)
    {
        // Check if teacher owns the module
        if (Auth::user()->role !== 'guru' || $modul->guru_id !== Auth::id()) {
            abort(403);
        }

        $answers = Jawaban::where('modul_id', $modul->id)
            ->with('siswa')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('guru.jawaban.index', compact('modul', 'answers'));
    }

    // Guru: Show specific answer
    public function show(Jawaban $jawaban)
    {
        // Check if teacher owns the module
        if (Auth::user()->role !== 'guru' || $jawaban->modul->guru_id !== Auth::id()) {
            abort(403);
        }

        return view('guru.jawaban.show', compact('jawaban'));
    }

    // Guru: Grade answer form
    public function grade(Jawaban $jawaban)
    {
        // Check if teacher owns the module
        if (Auth::user()->role !== 'guru' || $jawaban->modul->guru_id !== Auth::id()) {
            abort(403);
        }

        return view('guru.jawaban.grade', compact('jawaban'));
    }

    // Guru: Update answer with grade
    public function update(Request $request, Jawaban $jawaban)
    {
        // Check if teacher owns the module
        if (Auth::user()->role !== 'guru' || $jawaban->modul->guru_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'komentar_guru' => 'nullable|string',
        ]);

        $jawaban->update([
            'nilai' => $request->nilai,
            'komentar_guru' => $request->komentar_guru,
            'status' => 'dinilai',
        ]);

        // Add progress points if score is above threshold
        if ($request->nilai >= 70) {
            Progress::firstOrCreate([
                'user_id' => $jawaban->siswa_id,
                'modul_id' => $jawaban->modul_id,
            ], [
                'jumlah_poin' => $jawaban->modul->poin_reward,
                'jenis_aktivitas' => 'tugas_dinilai',
                'keterangan' => "Mendapat nilai {$request->nilai} untuk {$jawaban->modul->judul}",
            ]);
        }

        return redirect()->route('guru.jawaban.index', $jawaban->modul)
            ->with('success', 'Jawaban berhasil dinilai!');
    }

    // Student: Create answer form
    public function create(Modul $modul)
    {
        // Check if student already has answer for this module
        $existingAnswer = Jawaban::where('modul_id', $modul->id)
            ->where('siswa_id', Auth::id())
            ->first();

        if ($existingAnswer) {
            return redirect()->route('siswa.jawaban.edit', $existingAnswer);
        }

        return view('siswa.jawaban.create', compact('modul'));
    }

    // Student: Store new answer
    public function store(Request $request)
    {
        $request->validate([
            'modul_id' => 'required|exists:modul,id',
            'isi_jawaban' => 'nullable|string',
            'url_file' => 'nullable|array',
            'url_file.*' => 'file|max:10240', // 10MB max per file
        ]);

        $files = [];
        if ($request->hasFile('url_file')) {
            foreach ($request->file('url_file') as $file) {
                $path = $file->store('jawaban', 'public');
                $files[] = $path;
            }
        }

        Jawaban::create([
            'modul_id' => $request->modul_id,
            'siswa_id' => Auth::id(),
            'isi_jawaban' => $request->isi_jawaban,
            'url_file' => $files,
            'status' => $request->action === 'submit' ? 'dikirim' : 'draft',
            'submitted_at' => $request->action === 'submit' ? now() : null,
        ]);

        $message = $request->action === 'submit' ? 'Jawaban berhasil dikirim!' : 'Draft jawaban disimpan!';

        return redirect()->route('siswa.jawaban.index')->with('success', $message);
    }

    // Student: Show own answers
    public function userAnswers()
    {
        $answers = Jawaban::where('siswa_id', Auth::id())
            ->with('modul')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('siswa.jawaban.index', compact('answers'));
    }

    // Student: Edit draft answer
    public function edit(Jawaban $jawaban)
    {
        // Check if student owns the answer
        if (Auth::user()->role !== 'murid' || $jawaban->siswa_id !== Auth::id()) {
            abort(403);
        }

        return view('siswa.jawaban.edit', compact('jawaban'));
    }

    // Student: Update draft answer
    public function updateDraft(Request $request, Jawaban $jawaban)
    {
        // Check if student owns the answer
        if (Auth::user()->role !== 'murid' || $jawaban->siswa_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'isi_jawaban' => 'nullable|string',
            'url_file' => 'nullable|array',
            'url_file.*' => 'file|max:10240',
        ]);

        $files = $jawaban->url_file ?? [];
        if ($request->hasFile('url_file')) {
            foreach ($request->file('url_file') as $file) {
                $path = $file->store('jawaban', 'public');
                $files[] = $path;
            }
        }

        $jawaban->update([
            'isi_jawaban' => $request->isi_jawaban,
            'url_file' => $files,
            'status' => $request->action === 'submit' ? 'dikirim' : 'draft',
            'submitted_at' => $request->action === 'submit' ? now() : $jawaban->submitted_at,
        ]);

        $message = $request->action === 'submit' ? 'Jawaban berhasil dikirim!' : 'Draft jawaban diperbarui!';

        return redirect()->route('siswa.jawaban.index')->with('success', $message);
    }
}
