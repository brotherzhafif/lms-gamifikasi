<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Helpers\FileHelper;

class Jawaban extends Model
{
    use HasFactory;

    protected $table = 'jawaban';

    protected $fillable = [
        'modul_id',
        'siswa_id',
        'isi_jawaban',
        'url_file',
        'nilai',
        'status',
        'komentar_guru',
        'submitted_at',
    ];

    protected $casts = [
        'url_file' => 'array',
        'submitted_at' => 'datetime',
    ];

    /**
     * Relationship: Answer belongs to a module
     */
    public function modul(): BelongsTo
    {
        return $this->belongsTo(Modul::class);
    }

    /**
     * Relationship: Answer belongs to a student
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    /**
     * Get file URLs
     */
    public function getFileUrlsAttribute()
    {
        return FileHelper::getFileUrls($this->url_file);
    }

    /**
     * Check if has files
     */
    public function hasFiles()
    {
        return !empty($this->url_file);
    }

    protected static function booted()
    {
        // Auto-set submitted_at when status changes to 'dikirim'
        static::saving(function ($jawaban) {
            if ($jawaban->status === 'dikirim' && !$jawaban->submitted_at) {
                $jawaban->submitted_at = now();

                // Check deadline untuk set status terlambat
                if ($jawaban->modul && $jawaban->modul->deadline && now()->isAfter($jawaban->modul->deadline)) {
                    $jawaban->status = 'terlambat';
                }
            }
        });
    }
}
