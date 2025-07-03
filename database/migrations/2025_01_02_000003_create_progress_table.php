<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('modul_id')->constrained('modul')->onDelete('cascade');
            $table->integer('jumlah_poin');
            $table->enum('jenis_aktivitas', ['selesai_materi', 'kirim_tugas', 'nilai_tugas', 'selesai_quiz']);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Prevent duplicate progress for same activity
            $table->unique(['user_id', 'modul_id', 'jenis_aktivitas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress');
    }
};
