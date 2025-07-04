<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modul extends Model
{
    use HasFactory;

    protected $table = 'modul';

    protected $fillable = [
        'guru_id',
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
     * Relationship: Module has many answers
     */
    public function jawabans(): HasMany
    {
        return $this->hasMany(Jawaban::class);
    }

    /**
     * Relationship: Module has many progress records
     */
    public function progresses(): HasMany
    {
        return $this->hasMany(Progress::class);
    }
}
