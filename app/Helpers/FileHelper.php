<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Response;

class FileHelper
{
    /**
     * Generate URL untuk file yang di-upload
     */
    public static function getFileUrl($filePath, $disk = 'public')
    {
        if (empty($filePath)) {
            return null;
        }

        // Jika menggunakan S3 atau cloud storage
        if (config('filesystems.disks.' . $disk . '.driver') === 's3') {
            return Storage::disk($disk)->url($filePath);
        }

        // Jika menggunakan local storage
        return Storage::disk($disk)->url($filePath);
    }

    /**
     * Generate multiple URLs untuk array file paths
     */
    public static function getFileUrls($filePaths, $disk = 'public')
    {
        if (empty($filePaths) || !is_array($filePaths)) {
            return [];
        }

        return array_map(function ($path) use ($disk) {
            return [
                'path' => $path,
                'url' => self::getFileUrl($path, $disk),
                'name' => basename($path),
                'size' => Storage::disk($disk)->size($path) ?? 0,
            ];
        }, $filePaths);
    }

    /**
     * Download file secara aman
     */
    public static function downloadFile($filePath, $disk = 'public')
    {
        if (!Storage::disk($disk)->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk($disk)->download($filePath);
    }

    /**
     * Preview file di browser (bukan download)
     */
    public static function previewFile($filePath, $disk = 'public')
    {
        if (!Storage::disk($disk)->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        $mimeType = Storage::disk($disk)->mimeType($filePath);
        $content = Storage::disk($disk)->get($filePath);

        // Tentukan content type berdasarkan extension jika mime type tidak tersedia
        if (!$mimeType) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            $mimeType = self::getMimeTypeByExtension($extension);
        }

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Get mime type berdasarkan extension
     */
    private static function getMimeTypeByExtension($extension)
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    /**
     * Generate URL untuk preview file
     */
    public static function getPreviewUrl($filePath, $type, $id, $disk = 'public')
    {
        if (empty($filePath)) {
            return null;
        }

        $filename = basename($filePath);

        if ($type === 'modul') {
            return route('files.modul.preview', ['modul' => $id, 'filename' => $filename]);
        } else {
            return route('files.jawaban.preview', ['jawaban' => $id, 'filename' => $filename]);
        }
    }
}
