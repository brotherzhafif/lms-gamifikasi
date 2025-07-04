<?php

namespace App\Http\Controllers;

use App\Models\Modul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModulController extends Controller
{
    // List all modules (admin/guru/siswa)
    public function index(Request $request)
    {
        $query = Modul::with('guru');

        // Filter based on user role
        if (Auth::user()->role === 'guru') {
            $query->where('guru_id', Auth::id());
        } elseif (Auth::user()->role === 'murid') {
            $query->where('is_active', true);
        }

        if ($request->has('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->has('is_active') && Auth::user()->role !== 'murid') {
            $query->where('is_active', $request->is_active);
        }

        $modules = $query->paginate(20);

        // Return appropriate view based on role
        if (Auth::user()->role === 'guru') {
            return view('guru.modul.index', compact('modules'));
        } elseif (Auth::user()->role === 'murid') {
            return view('siswa.modul.index', compact('modules'));
        }

        return view('modul.index', compact('modules'));
    }

    // Show module detail
    public function show(Modul $modul)
    {
        // Check permissions for teachers
        if (Auth::user()->role === 'guru' && $modul->guru_id !== Auth::id()) {
            abort(403);
        }

        if (Auth::user()->role === 'guru') {
            return view('guru.modul.show', compact('modul'));
        } elseif (Auth::user()->role === 'murid') {
            return view('siswa.modul.show', compact('modul'));
        }

        return view('modul.show', compact('modul'));
    }

    // Guru: Show create form
    public function create()
    {
        if (Auth::user()->role !== 'guru') {
            abort(403);
        }

        return view('guru.modul.create');
    }

    // Guru: Show edit form
    public function edit(Modul $modul)
    {
        if (Auth::user()->role !== 'guru' || $modul->guru_id !== Auth::id()) {
            abort(403);
        }

        return view('guru.modul.edit', compact('modul'));
    }

    // Guru: Create module
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'guru') {
            abort(403);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'jenis' => 'required|in:materi,tugas,quiz',
            'file_path' => 'nullable',
            'deadline' => 'nullable|date',
            'poin_reward' => 'required|integer|min:0',
        ]);

        Modul::create([
            'guru_id' => Auth::id(),
            'judul' => $request->judul,
            'isi' => $request->isi,
            'jenis' => $request->jenis,
            'file_path' => $request->file_path,
            'deadline' => $request->deadline,
            'poin_reward' => $request->poin_reward,
            'is_active' => true,
        ]);

        return redirect()->route('guru.modul.index')->with('success', 'Modul berhasil dibuat!');
    }

    // Guru: Update module
    public function update(Request $request, Modul $modul)
    {
        if (Auth::user()->role !== 'guru' || $modul->guru_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'jenis' => 'required|in:materi,tugas,quiz',
            'file_path' => 'nullable',
            'deadline' => 'nullable|date',
            'poin_reward' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $modul->update($request->only([
            'judul',
            'isi',
            'jenis',
            'file_path',
            'deadline',
            'poin_reward',
            'is_active'
        ]));

        return redirect()->route('guru.modul.index')->with('success', 'Modul berhasil diupdate!');
    }

    // Admin/Guru: Delete module
    public function destroy(Modul $modul)
    {
        if (Auth::user()->role === 'guru' && $modul->guru_id !== Auth::id()) {
            abort(403);
        }

        $modul->delete();
        return redirect()->route('guru.modul.index')->with('success', 'Modul berhasil dihapus!');
    }
}
