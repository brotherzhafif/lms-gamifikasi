<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use App\Models\Modul;
use App\Models\Jawaban;

class FileController extends Controller
{
    /**
     * Download file dari modul
     */
    public function downloadModulFile(Modul $modul, $filename)
    {
        // Check permissions
        if (!$this->canAccessModul($modul)) {
            abort(403, 'Tidak memiliki akses ke file ini');
        }

        $filePath = collect($modul->file_path)->first(function ($path) use ($filename) {
            return basename($path) === $filename;
        });

        if (!$filePath) {
            abort(404, 'File tidak ditemukan');
        }

        return FileHelper::downloadFile($filePath);
    }

    /**
     * Download file dari jawaban
     */
    public function downloadJawabanFile(Jawaban $jawaban, $filename)
    {
        // Check permissions
        if (!$this->canAccessJawaban($jawaban)) {
            abort(403, 'Tidak memiliki akses ke file ini');
        }

        $filePath = collect($jawaban->url_file)->first(function ($path) use ($filename) {
            return basename($path) === $filename;
        });

        if (!$filePath) {
            abort(404, 'File tidak ditemukan');
        }

        return FileHelper::downloadFile($filePath);
    }

    /**
     * Preview file dari modul di tab baru
     */
    public function previewModulFile(Modul $modul, $filename)
    {
        // Check permissions
        if (!$this->canAccessModul($modul)) {
            abort(403, 'Tidak memiliki akses ke file ini');
        }

        $filePath = collect($modul->file_path)->first(function ($path) use ($filename) {
            return basename($path) === $filename;
        });

        if (!$filePath) {
            abort(404, 'File tidak ditemukan');
        }

        return FileHelper::previewFile($filePath);
    }

    /**
     * Preview file dari jawaban di tab baru
     */
    public function previewJawabanFile(Jawaban $jawaban, $filename)
    {
        // Check permissions
        if (!$this->canAccessJawaban($jawaban)) {
            abort(403, 'Tidak memiliki akses ke file ini');
        }

        $filePath = collect($jawaban->url_file)->first(function ($path) use ($filename) {
            return basename($path) === $filename;
        });

        if (!$filePath) {
            abort(404, 'File tidak ditemukan');
        }

        return FileHelper::previewFile($filePath);
    }

    /**
     * Get file URL (for AJAX requests)
     */
    public function getFileUrl(Request $request)
    {
        $type = $request->input('type'); // 'modul' or 'jawaban'
        $id = $request->input('id');
        $filename = $request->input('filename');

        if ($type === 'modul') {
            $modul = Modul::findOrFail($id);
            if (!$this->canAccessModul($modul)) {
                abort(403);
            }

            $filePath = collect($modul->file_path)->first(function ($path) use ($filename) {
                return basename($path) === $filename;
            });
        } else {
            $jawaban = Jawaban::findOrFail($id);
            if (!$this->canAccessJawaban($jawaban)) {
                abort(403);
            }

            $filePath = collect($jawaban->url_file)->first(function ($path) use ($filename) {
                return basename($path) === $filename;
            });
        }

        if (!$filePath) {
            abort(404);
        }

        return response()->json([
            'url' => FileHelper::getFileUrl($filePath),
            'name' => basename($filePath),
            'size' => Storage::disk('public')->size($filePath)
        ]);
    }

    private function canAccessModul(Modul $modul)
    {
        $user = auth()->user();

        // Admin can access all
        if ($user->role === 'admin') {
            return true;
        }

        // Guru can access their own modules
        if ($user->role === 'guru' && $modul->guru_id === $user->id) {
            return true;
        }

        // Siswa can access active modules
        if ($user->role === 'siswa' && $modul->is_active) {
            return true;
        }

        return false;
    }

    private function canAccessJawaban(Jawaban $jawaban)
    {
        $user = auth()->user();

        // Admin can access all
        if ($user->role === 'admin') {
            return true;
        }

        // Guru can access answers for their modules
        if ($user->role === 'guru' && $jawaban->modul->guru_id === $user->id) {
            return true;
        }

        // Siswa can access their own answers
        if ($user->role === 'siswa' && $jawaban->siswa_id === $user->id) {
            return true;
        }

        return false;
    }
}
