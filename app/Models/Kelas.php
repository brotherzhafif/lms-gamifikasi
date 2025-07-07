<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'kode_kelas',
        'deskripsi',
        'tingkat',
        'jurusan',
        'kapasitas',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: Kelas has many users (students)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relationship: Kelas has many students only
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'murid');
    }

    /**
     * Relationship: Kelas has many modules
     */
    public function moduls(): HasMany
    {
        return $this->hasMany(Modul::class);
    }

    /**
     * Get formatted class name with level and major
     */
    public function getFormattedNameAttribute(): string
    {
        return "{$this->tingkat} {$this->jurusan} - {$this->nama_kelas}";
    }

    /**
     * Get total students count
     */
    public function getTotalSiswaAttribute(): int
    {
        return $this->siswa()->count();
    }
}
