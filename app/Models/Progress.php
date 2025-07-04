<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progress extends Model
{
    use HasFactory;

    protected $table = 'progress';

    protected $fillable = [
        'user_id',
        'modul_id',
        'jumlah_poin',
        'jenis_aktivitas',
        'keterangan',
    ];

    /**
     * Relationship: Progress belongs to a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Progress belongs to a module
     */
    public function modul(): BelongsTo
    {
        return $this->belongsTo(Modul::class);
    }
}
