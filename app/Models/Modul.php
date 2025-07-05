<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;

class Modul extends Model
{
    use HasFactory;

    protected $table = 'modul';

    protected $fillable = [
        'guru_id',
        'mata_pelajaran_id',
        'judul',
        'isi',
        'jenis',
        'file_path',
        'deadline',
        'poin_reward',
        'is_active',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'is_active' => 'boolean',
        'file_path' => 'array',
    ];

    /**
     * Relationship: Module belongs to a teacher (guru)
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /**
     * Relationship: Module belongs to a mata pelajaran
     */
    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    /**
     * Relationship: Module has many answers
     */
    public function jawabans(): HasMany
    {
        return $this->hasMany(Jawaban::class, 'modul_id');
    }

    /**
     * Relationship: Module has many progress records
     */
    public function progresses(): HasMany
    {
        return $this->hasMany(Progress::class, 'modul_id');
    }

    /**
     * Get file URLs
     */
    public function getFileUrlsAttribute()
    {
        return FileHelper::getFileUrls($this->file_path);
    }

    /**
     * Get single file URL (for backward compatibility)
     */
    public function getFileUrlAttribute()
    {
        if (empty($this->file_path)) {
            return null;
        }

        $firstFile = is_array($this->file_path) ? $this->file_path[0] : $this->file_path;
        return FileHelper::getFileUrl($firstFile);
    }

    /**
     * Check if has files
     */
    public function hasFiles()
    {
        return !empty($this->file_path);
    }
}
