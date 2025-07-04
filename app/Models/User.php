<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'nis',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's name for Filament compatibility
     */
    public function getNameAttribute(): string
    {
        return $this->nama;
    }

    /**
     * Get the user's name for Filament (alternative method)
     */
    public function getFilamentName(): string
    {
        return $this->nama;
    }

    /**
     * Relationship: User has many modules (for teachers)
     */
    public function moduls(): HasMany
    {
        return $this->hasMany(Modul::class, 'guru_id');
    }

    /**
     * Relationship: User has many answers (for students)
     */
    public function jawabans(): HasMany
    {
        return $this->hasMany(Jawaban::class, 'siswa_id');
    }

    /**
     * Relationship: User has many progress records
     */
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }
}
